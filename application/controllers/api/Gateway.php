<?php defined('BASEPATH') or exit('No direct script access alllowed');

header('Access-Control-Allow-Origin: *');

class Gateway extends CI_Controller
{
  public function user()
  {
    $user = [];
    $this->load->model('model_user_data');
    if ($this->input->get('user_id')) {
      $user_id = $this->input->get('user_id');
      $select = 'name_sei, name_mei';
      $where = ['user_id'=>$user_id, 'state'=>1];
      $user_data = $this->model_user_data->find_row($select, $where);
    }
    if ($this->input->get('idm')) {
      $idm = $this->input->get('idm');
      $select = 'name_sei, name_mei, user_id';
      $where = ['idm'=>$idm, 'state'=>1];
      $user_data = $this->model_user_data->find_row($select, $where);
      $user_id = $user_data->user_id;
    }

    if ($user_data) {
      $now = new DateTime();
      $year = $now->format('Y');
      $month = $now->format('m');
      $day = $now->format('d');
      $now_date = $now->format('Y-m-d');

      // グループデータ取得
      $this->load->model('model_group_history');
      $group_history_data = $this->model_group_history->get_last_userid($user_id);
      $this->load->model('model_group');
      $group_name = array_fill(1, 4, ' '); // $group_name[1-3]を空白で埋める
      if ($group_history_data->group1_id) {
        $group_name[1] = $this->model_group->get_group1_id($group_history_data->group1_id)->group_name;
      }
      if ($group_history_data->group2_id) {
        $group_name[2] = $this->model_group->get_group2_id($group_history_data->group2_id)->group_name;
      }
      if ($group_history_data->group3_id) {
        $group_name[3] = $this->model_group->get_group3_id($group_history_data->group3_id)->group_name;
      }
      $this->load->model('model_group_title');
      $group_title = $this->model_group_title->gets_data();
      foreach ($group_title as $value) {
        $group[$value->group_id] = ($value->title) ? $value->title.'：'.$group_name[$value->group_id] : '';
      }

      // 時間データ取得
      $this->load->model('model_time');
      $time_data = $this->model_time->get_day_userid($now_date, $user_id);
      $in_flag = $out_flag = $time_id = '';
      if ($time_data) {
        $in_flag = $time_data->in_flag; // 出勤フラグ
        $out_flag = $time_data->out_flag; // 退勤フラグ
        $time_id = $time_data->id; // time_data id
        $area_id = $time_data->area_id;
      }
      $month_time_data = (int) $this->model_time->get_fact_work_hour_sum_time_month_userid($year, $month, $user_id)->fact_work_hour; // 月間総労働時間(分)
      $month_time = sprintf('%d:%02d', floor($month_time_data / 60), $month_time_data % 60); // 月間総労働時間 表示用
      $month_count = $this->model_time->get_fact_work_hour_count_time_month_userid($year, $month, $user_id); // 月間出勤日数

      // configデータ取得
      $this->load->model('model_config_values');
      $where = [];
      $result = $this->model_config_values->find('config_name, value', $where, '');
      $config_data = array_column($result, 'value', 'config_name');

      // 休憩データ取得
      $rest_flag = 0; // 休憩フラグ
      if ((int)$config_data['rest_input_flag'] === 1 && $time_id) {
        $this->load->model('model_rest_data');
        $rest_data = $this->model_rest_data->get_timeid($time_id);
        if ($rest_data) {
          $rest_flag = $rest_data->flag;
        }
      }

      // 中抜けデータ取得
      $goaway_flag = 0; // 中抜けフラグ
      if ((int)$config_data['goaway_input_flag'] === 1 && $time_id) {
        $this->load->model('model_goaway_data');
        $goaway_data = $this->model_goaway_data->get_timeid($time_id);
        if ($goaway_data) {
          $goaway_flag = $goaway_data->flag;
        }
      }

      // 出力データ
      $user = [
        'user_id' => str_pad($user_id, (int)$config_data['id_size'], '0', STR_PAD_LEFT),
        'user_name' => $user_data->name_sei.' '.$user_data->name_mei,
        'group1_name' => $group[1],
        'group2_name' => $group[2],
        'group3_name' => $group[3],
        'in_flag' => (int)$in_flag,
        'out_flag' => (int)$out_flag,
        'time' => $month_time,
        'count' => $month_count,
        'rest_flag' => $rest_flag,
        'goaway_flag' => $goaway_flag,
        'area_id' => $area_id,
        'user_area_id' =>@$user_data->user_area_id ?: ''
      ];
    }
    // 出力 json
    $this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($user));
  }

  //
  public function insert()
  {
    $data['user_id'] = (int)$this->input->get('user_id');
    // $this->load->model('model_user'); // パーソナルデータ取得
    // $user_data = $this->model_user->get_now_state_userid($data['user_id']);
    $this->load->model('model_user_data');
    $select = 'name_sei, name_mei';
    $where = ['user_id'=>$user_id, 'state'=>1];
    $user_data = $this->model_user_data->find_row($select, $where);
    $user_name = $user_data->name_sei.' '.$user_data->name_mei;
    $flag = $this->input->get('flag');
    $data['area_id'] = (int)$this->input->get('area_id');
    $this->load->model('model_area_data');
    $where = ['id'=>$data['area_id']];
    $area_name = $this->model_area_data->find_row('area_name', $where)->area_name;

    $now = new DateTime(); // 時刻データ取得
    $data['dk_date'] = $now->format('Y-m-d'); // 入力日
    $this->load->library('process_time_lib'); // 分析処理用 lib 読込

    if ($flag == 'in') { // 出勤時
      // $data['flag'] = 'gateway'; // 分析処理時　判別用フラグ
      $data['in_flag'] = 1; // 出勤フラグ
      $data['in_time'] = $now->format('H:i:s'); // 出勤時刻
      $in_time_w = $now->format('H時i分'); // 表示用　出勤時刻

      $data['in_work_time'] = $data['in_time'];
      // $data = $this->process_time_lib->in_time($data); // 分析処理lib

      $message = $user_name.' '.$in_time_w.' 出勤'; // メッセージ
    }
    if ($flag == 'out') { // 退勤時
      $data['flag'] = 'gateway'; // 分析処理時　判別用フラグ
      $data['out_flag'] = 1; // 退勤フラグ
      $data['out_time'] = $now->format('H:i:s'); // 退勤時刻
      $out_time_w = $now->format('H時i分'); // 表示用　退勤時刻
      $data = $this->process_time_lib->out_time($data); // 分析処理lib
      $message = $user_name.' '.$out_time_w.' 退勤'; // メッセージ
    }

    // area
    if (isset($area_name)) {
      $message = $message."\n場所：".$area_name;
    }

    // DB保存
    $this->load->model('model_time'); // model time 読込
    $now_time_data = $this->model_time->check_day_userid($data['dk_date'], $data['user_id']);
    if (!$now_time_data) {
      $this->model_time->insert_data($data); // 新規登録
    } else {
      $data['id'] = $now_time_data->id;
      $this->model_time->update_data($data); // アップデート
    }

    // // LINE通知
    // if ((int)$this->model_config->get_data()->line_flag === 1) { // line_flag = 1 -> LINE通知
    //   $line_message = [
    //     'type' => 'text',
    //     'text' => $message
    //   ];
    //   $this->load->library('process_line_lib'); // LINE通知用 lib 読込
    //   $this->process_line_lib->line_message($line_message, $data['user_id']);
    //   if (isset($gps_data['flag'])) {
    //     $line_message = [
    //       'type' => 'location',
    //       'title' => $user_name,
    //       'address' => '入力場所',
    //       'latitude' => $gps_data['latitude'],
    //       'longitude' => $gps_data['longitude']
    //     ];
    //     $this->process_line_lib->line_message($line_message, $data['user_id']);
    //   }
    // }
    // // mail通知
    // // notice_mail_flag = 1 メールのみ　notice_mail_flag = 9 すべて
    // if ((int)$this->model_config->get_data()->notice_mail_flag === 1 || (int)$this->model_config->get_data()->notice_mail_flag === 9) {
    //   $mail_subject = '出退勤通知';
    //   $this->load->library('process_mail_lib');
    //   if (isset($gps_data['flag'])) {
    //     $this->process_mail_lib->mail_send($mail_subject, $message."\n入力場所GPS：".$gps_url);
    //   } else {
    //     $this->process_mail_lib->mail_send($mail_subject, $message);
    //   }
    // }

    // 出力データ
    $callback = [
      'message' => $message,
    ];

    // 出力 json
    $this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($callback));

  }

  public function userIdmList()
  {
    $this->load->model('model_user_data');
    $select = 'id, user_id, name_sei, name_mei, idm';
    $where = ['state'=>1];
    $user_data = $this->model_user_data->find($select, $where, '');
    // 出力
    $this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($user_data));
  }
}
