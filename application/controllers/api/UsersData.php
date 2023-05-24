<?php defined('BASEPATH') or exit('No direct script access alllowed');

header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Origin: https://shelter.cybozu.com/');

class UsersData extends CI_Controller
{
  public function index()
  {
    $get_date = $this->input->get('date');
    $error_flag = false;
    $message = '';
    $format_str = '%Y-%m-%d';
    if (strptime($get_date, $format_str) == false) {
      $error_flag = true;
      $message = '日付の形式が正しくありません。';
    }
    if (!$error_flag) {
      list($year, $month, $day) = explode('-', $get_date);
      if (checkdate($month, $day, $year) == false) {
        $error_flag = true;
        $message = '日付が正しくありません。';
      } else {
        $date = new DateTime($get_date);
        $w = $date->format('w');
        $week = ['日', '月', '火', '水', '木', '金', '土'];
        $weekday = $week[$w];
      }
    }

    $user_data = [];
    $data = [];
    if (!$error_flag) {
      $this->load->model('model_shift');
      $result = $this->model_shift->find_day_all($get_date);
      $shift_data = [];
      if ($result) {
        $shift_data = array_column($result, null, 'user_id');
      }
      $this->load->model('model_time');
      $result = $this->model_time->gets_day_all($get_date);
      $times_data = [];
      if ($result) {
        $times_data = array_column($result, null, 'user_id');
      }
      $this->load->model('model_user_data');
      $select = 'user_id, name_sei, name_mei';
      $where = ['api_output'=>1, 'state'=>1];
      $result = $this->model_user_data->find($select, $where, '');
      if ($result) {
        foreach ($result as $user) {
          if ($times_data[$user->user_id]->fact_work_hour) {
            $hour = (int)$times_data[$user->user_id]->fact_work_hour;
            $time = $hour > 0 ? sprintf("%d:%02d", floor($hour/60), $hour%60) : '0:00';
          } else {
            $hour = "";
            $time = "";
          }
          $data[] = [
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'weekday' => $weekday,
            'user_id' => (string)$user->user_id,
            'name' => $user->name_sei.' '.$user->name_mei,
            'shift_in' => @$shift_data[$user->user_id]->in_time ?: '',
            'shift_out' => @$shift_data[$user->user_id]->out_time ?: '',
            'in' => @$times_data[$user->user_id]->in_time ?: '',
            'out' => @$times_data[$user->user_id]->out_time ?: '',
            'hour' => @$hour ?: '',
            'time' => @$time ?: ''
          ];
        }
        $message = count($data).'件のデータを取得';
      } else {
        $message = 'データはありません';
      }
    }
    $user_data['data'] = $data;
    $user_data['message'] = $message;

    // 出力 json
    $this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($user_data));
  }
}
