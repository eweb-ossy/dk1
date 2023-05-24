<?php defined('BASEPATH') or exit('No direct script access alllowed');

/**
 * gateway 出退勤入力画面　データ
 */
class Gateway extends CI_Controller
{
    public $config_data = [];

    public function __construct()
	{
		parent::__construct();
        $this->load->database();

        // config data取得
        $result = $this->db->query('SELECT config_name, value FROM config_values')->result();
        $this->config_data = array_column($result, 'value', 'config_name');
        $this->config_data['area_id'] = (int)$this->session->area_id; // エリアID
        $row = $this->db->query('SELECT id FROM message_title_data WHERE type = "gateway" AND flag = 1')->row();
        $this->config_data['message_flag'] = $row ? 1 : 0; // message data の有無
        $this->config_data['notice_status_data'] = $this->db->query('SELECT * FROM notice_status_data')->result(); // 申請タイトルdata取得 notice_status_data
	}

    // 設定データを返す　 gateway ver.2 用
    public function init()
    {
        // config data取得
        $sql = "SELECT config_name, value FROM config_values WHERE work = 'front'";
        $result = $this->db->query($sql)->result();
        $config_data = array_column($result, 'value', 'config_name');
        $config_data['area_id'] = (int)$this->session->area_id; // エリアID
        $row = $this->db->query('SELECT id FROM message_title_data WHERE type = "gateway" AND flag = 1')->row();
        $config_data['message_flag'] = $row ? 1 : 0; // message data の有無
        $config_data['notice_status_data'] = $this->db->query('SELECT * FROM notice_status_data')->result(); // 申請タイトルdata取得 notice_status_data

        // user_agent
        $this->load->library('user_agent');
        if ($this->agent->is_mobile()) {
            $config_data['agent'] = 'mobile';
        } else {
            $config_data['agent'] = 'pc';
        }

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($config_data));
    }
    
    /**
     * 従業員パーゾナル情報を返す
     *
     * @param int $user_id
     * @return array $user
     *
     */
    public function user()
    {
        $user = []; // 出力データ初期化

        $user_id = (int)$this->input->post('user_id');

        // user data 取得
        $sql = "SELECT name_sei, name_mei, shift_alert_flag, management_flag, input_confirm_flag FROM user_data WHERE user_id = {$user_id} AND ( entry_date <= DATE_FORMAT(now(), '%Y-%m-%d') OR entry_date IS NULL) AND ( resign_date >= DATE_FORMAT(now(), '%Y-%m-%d') OR resign_date IS NULL)";
        $user_data = $this->db->query($sql)->row();

        if ($user_data) {
            $now = new DateTime();
            $year = $now->format('Y');
            $month = $now->format('m');
            $day = $now->format('d');
            $now_date = $now->format('Y-m-d');
            $now_month = $now->format('Ym');

            // 日付またぎあり && 日付またぎ締め時間内
            // format('G') 時 24時間単位　0-23
            $over_day = 0;
            if (
                (int)$this->config_data['over_day'] > 0 &&
                (int)$now->format('G') >= 0 && 
                (int)$now->format('G') <= (int)$this->config_data['over_day']
                ) {
                $yesterday = $now->sub(DateInterval::createFromDateString('1 day'));
                $now_date = $yesterday->format('Y-m-d');
                $now_month = $yesterday->format('Ym');
            }

            // group title
            $result = $this->db->query('SELECT group_id, title FROM group_title')->result();
            $group_title = array_column($result, 'title', 'group_id');
            // group history 
            $sql = "SELECT group1_id, group2_id, group3_id FROM group_history WHERE user_id = {$user_id} ORDER BY to_date DESC";
            $group_history_data = $this->db->query($sql)->row_array();
            // group name 
            for ($i=1; $i<=3; $i++) {
                $group[$i] = isset($group_title[$i]) ? $group_title[$i]."：" : '';
                if (isset($group_history_data['group'.$i.'_id'])) {
                    $sql = "SELECT group_name FROM user_groups{$i} WHERE id = {$group_history_data['group'.$i.'_id']} AND state = 1";
                    $group[$i] .= @$this->db->query($sql)->row()->group_name ?: ' ';
                } else {
                    $group[$i] .= ' ';
                }
            }

            // time data 
            $sql = "SELECT id, in_flag, out_flag FROM time_data WHERE dk_date = '{$now_date}' AND user_id = {$user_id}";
            $time_data = $this->db->query($sql)->row();
            $time_id = $time_data->id ?? FALSE; // time_data id

            // 当月出勤状況
            $sql = "SELECT fact_work_hour FROM time_data WHERE date_format(dk_date, '%Y%m') = {$now_month} AND user_id = {$user_id} AND fact_work_hour > 0";
            $result = $this->db->query($sql)->result();
            $month_time_data = array_column($result, "fact_work_hour");
            $month_all_time = array_sum($month_time_data);
            $month_time = sprintf('%d:%02d', floor($month_all_time / 60), $month_all_time % 60); // 月間総労働時間 表示用
            $month_count = count($month_time_data); // 出勤数

            // 休憩データ取得
            $rest_flag = 1; // 休憩フラグ
            if ($time_id) {
                $this->db->where('time_data_id', $time_id);
                $this->db->order_by('in_time', 'DESC');
                $rest_data = $this->db->get('rest_data')->first_row();
                if ($rest_data) {
                    $rest_flag = (int)$rest_data->flag;
                }
            }
            if ($this->config_data['rest_input_flag'] == 0) {
                $rest_flag = 3; // 休憩設定なし
            }

            // 中抜けデータ取得
            $goaway_flag = 0; // 中抜けフラグ
            if ((int)$this->config_data['goaway_input_flag'] === 1 && $time_id) {
                $this->load->model('model_goaway_data');
                $goaway_data = $this->model_goaway_data->get_timeid($time_id);
                if ($goaway_data) {
                    $goaway_flag = $goaway_data->flag;
                }
            }

            // ルールデータ　 2021.09.01追加　　休憩入力機能時に、自動休憩ルールの場合、休憩ボタンを押せなくする設定のため
            $auto_rest = FALSE;
            if ($this->config_data['rest_input_flag'] == 1) {
                $this->load->database();
                $sql = 'SELECT all_flag, group_id, group_no, user_id, rest_rule_flag FROM config_rules';
                $query = $this->db->query($sql);
                $result = $query->result_array();
                foreach ($result as $key => $value) {
                    if ($value['all_flag'] == 1 && $value['rest_rule_flag'] == 1) {
                        $auto_rest = TRUE;
                    }
                    if (isset($group_no[(int)$value['group_id']]) && $value['group_id'] && $value['group_no']) {
                        if ($group_no[(int)$value['group_id']] == $value['group_no']) {
                            $auto_rest = $value['rest_rule_flag'] == 1 ? TRUE : FALSE;
                        }
                    }
                    if ((int)$value['user_id'] === $user_id) {
                        $auto_rest = $value['rest_rule_flag'] == 1 ? TRUE : FALSE;
                        break; // userがマッチした場合は最優先のため break
                    }
                }
            }

            $user = [
                'user_name' => $user_data->name_sei.' '.$user_data->name_mei,
                'group1_name' => $group[1],
                'group2_name' => $group[2],
                'group3_name' => $group[3],
                'in_flag' => @$time_data->in_flag ? (int)$time_data->in_flag : 0,
                'out_flag' => @$time_data->out_flag ? (int)$time_data->out_flag : 0,
                'time' => $month_time,
                'count' => (int)$month_count,
                'rest_flag' => (int)$rest_flag,
                'goaway_flag' => (int)$goaway_flag,
                'shift_alert_flag'=> (int)$user_data->shift_alert_flag,
                'management_flag'=> (int)$user_data->management_flag,
                'auto_rest'=> $auto_rest, // 2021.09.01 追加 自動休憩ルール boolean
                'input_confirm_flag'=> (int)$user_data->input_confirm_flag
            ];
        }
        // 出力 json
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($user));
    }

    /**
    * 出退勤登録　処理
    *
    * @param int user_id
    * @param string user_name
    * @param string flag
    * @param int area_id
    * @return array $callback
    *
    */
    public function insert()
    {
        $status_data['user_id'] = (int)$this->input->post('user_id');
        if ($status_data['user_id'] === 0) {
            // 出力データ
            $callback = [
                'message' => 'ng'
            ];
            // 出力 json
            $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($callback));
            return;
        }

        $user_name = $this->input->post('user_name');
        $flag = $this->input->post('flag'); // 出勤 or 退勤　判別用フラグ
        $status_data['area_id'] = (int)$this->input->post('area_id'); // エリアID

        if ($this->config_data['area_flag'] == 1) {
            if (!$status_data['area_id']) {
                $status_data['area_id'] = $this->session->area_id; // エリアID
            }
            if ($status_data['area_id']) {
                $this->load->model('model_area_data');
                $where = ['id'=>(int)$status_data['area_id']];
                $area_name = $this->model_area_data->find_row('area_name', $where)->area_name;
            }
        }

        $now = new DateTimeImmutable(); // 時刻データ取得

        // 通常
        $time = $now->format('H:i:s');
        $time_w = $now->format('H時i分');
        $status_data['dk_datetime'] = $now->format('Y-m-d H:i:s');
        $message = '';

        // 日付またぎあり && 日付またぎ締め時間内
        // format('G') 時 24時間単位　0-23
        $over_day = 0;
        if ((int)$this->config_data['over_day'] > 0 && (int)$now->format('G') >= 0 && (int)$now->format('G') <= (int)$this->config_data['over_day']) {
            $yesterday = $now->sub(DateInterval::createFromDateString('1 day'));
            $dk_datetime = $yesterday->format('Y-m-d H:i:s');
            $dk_datetime_array = date_parse_from_format('Y-m-d h:i:s', $dk_datetime);
            $hour = $dk_datetime_array['hour'] + 24;
            $min = sprintf('%02d', $dk_datetime_array['minute']);
            $sec = sprintf('%02d', $dk_datetime_array['second']);
            $time = $hour.':'.$min.':'.$sec;
            $time_w = $hour.'時'.$min.'分';
            $over_day = 1;
            $over_date = $yesterday->format('Y-m-d');
            $status_data['dk_datetime'] = $over_date.' 23:59:59';
        }

        // gateway 出退勤
        if ($flag == 'in') { // 出勤時
            $status_data['flag'] = 'in';
            $status_data['in_time'] = $time;
            $message = $user_name.' '.$time_w.' 出勤'; // メッセージ
            // log_message('info', $message);
        }
        if ($flag == 'out') { // 退勤時
            $status_data['flag'] = 'out';
            $status_data['out_time'] = $time;
            $message = $user_name.' '.$time_w.' 退勤'; // メッセージ
            // log_message('info', $message);
        }

        // 直行・直帰
        if ($flag == 'nonstop_in') { // 直行出勤時
            $status_data['flag'] = 'nonstop_in';
            $status_data['in_time'] = $time; // 出勤時刻（入力時刻）
            $this->load->library('process_rules_lib'); // rules lib 読込
            $rules = $this->process_rules_lib->get_rule($status_data['user_id']);
            $status_data['in_work_time'] = $rules->basic_in_time;
            $in_work_time_date = new DateTimeImmutable($status_data['in_work_time']);
            $in_work_time_w = $in_work_time_date->format('H時i分');
            $message = $user_name.' '.$time_w.' 直行出勤を入力'."\n".'出勤時刻を'.$in_work_time_w.'に設定しました'; // メッセージ
            // log_message('info', $message);
        }
        if ($flag == 'nonstop_out') { // 直帰退勤時
            $status_data['flag'] = 'nonstop_out';
            $status_data['out_time'] = $time; // 退勤時刻（入力時刻）
            $this->load->library('process_rules_lib'); // rules lib 読込
            $rules = $this->process_rules_lib->get_rule($status_data['user_id']);
            $status_data['out_work_time'] = $rules->basic_out_time;
            $out_work_time_date = new DateTimeImmutable($status_data['out_work_time']);
            $out_work_time_w = $out_work_time_date->format('H時i分');
            $message = $user_name.' '.$time_w.' 直帰退勤を入力'."\n".'退勤時刻を'.$out_work_time_w.'に設定しました'; // メッセージ
            // log_message('info', $message);
        }

        // 分析処理
        $this->load->library('process_status_lib');
        $processing = $this->process_status_lib->status($status_data);
        if ($processing === false) {
            $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['message'=>'ng']));
            return;
        }

        // area
        if (isset($area_name)) {
            $message = $message."\nエリア：".$area_name;
        }

        // GPS
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $info = $this->input->post('info');
        $this->load->model('model_gps_data');
        if ($over_day === 0) {
            $this->model_gps_data->gps_date = $now->format('Y-m-d');
        }
        if ($over_day === 1) {
            $this->model_gps_data->gps_date = $over_date;
        }
        if ($flag === 'in') {
            $this->model_gps_data->flag = 1;
        }
        if ($flag === 'out') {
            $this->model_gps_data->flag = 2;
        }
        $this->model_gps_data->user_id = (int)$status_data['user_id'];
        $this->model_gps_data->latitude = $latitude;
        $this->model_gps_data->longitude = $longitude;
        $this->model_gps_data->ip_address = $_SERVER['REMOTE_ADDR'];
        $this->load->library('user_agent');
        $this->model_gps_data->browser = $this->agent->browser();
        $this->model_gps_data->version = $this->agent->version();
        $this->model_gps_data->mobile = $this->agent->mobile();
        $this->model_gps_data->platform = $this->agent->platform();
        $this->model_gps_data->info = $info;
        $this->model_gps_data->insert();

        // LINE通知
        if ($this->config_data['line_flag'] == 1) { // line_flag = 1 -> LINE通知
            $line_message = [
                'type' => 'text',
                'text' => $message
            ];
            $this->load->library('process_line_lib'); // LINE通知用 lib 読込
            $this->process_line_lib->line_message($line_message, $status_data['user_id']);

            if ($this->config_data['gps_flag'] == 1 || ($this->config_data['gps_flag'] == 2 && $this->agent->is_mobile())) {
                $line_message = [
                    'type' => 'location',
                    'title' => $user_name,
                    'address' => '入力場所',
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ];
                $this->process_line_lib->line_message($line_message, $status_data['user_id']);
            }
        }
        // mail通知
        // notice_mail_flag = 1 メールのみ　notice_mail_flag = 9 すべて
        if ($this->config_data['notice_mail_flag'] == 1 || $this->config_data['notice_mail_flag'] == 9) {
            $mail_subject = $this->config_data['mail_title_gateway']; // メールタイトル
            if (!$mail_subject) {
                $mail_subject = '出退勤通知';
            }
            $gps_url = '';
            if ($this->config_data['gps_flag'] == 1 || ($this->config_data['gps_flag'] == 2 && $this->agent->is_mobile())) {
                $gps_url = "\n入力場所GPS：https://www.google.com/maps?q=".$latitude.','.$longitude;
            }
            $this->load->library('process_mail_lib');
            $this->process_mail_lib->mail_send($mail_subject, $message.$gps_url);
        }
        // Slack通知
        if ($this->config_data['slack_flag'] == 1) {
            $slack_message = [
                'username' => '打刻keeperBOT',
                'text' => $message
            ];
            $this->load->library('process_slack_lib'); // Slack通知用 lib 読込
            $this->process_slack_lib->slack_message($slack_message);
        }
        // resq用
        if ($this->config_data['resq_flag'] == 1 && $flag === 'out') {
            $now_date = $now->format('Y-m-d');
            $this->load->model('model_time');
            // 年月日で検索し、該当する全ての勤務状況+従業員情報を返す day all + user_data
            $result = $this->model_time->get_date_user_id_status($now_date, (int)$status_data['user_id']);
            if ((int)$result->fact_work_hour > 0) {
                $post = [
                    'company_code' => $this->config_data['resq_company_code'],
                    'employee_code' => $status_data['user_id'],
                    'target_date' => $now_date,
                    'working_hours' => (int)$result->fact_work_hour / 60
                ];
                $this->load->library('process_resq_lib'); // resQ lib 読込
                $this->process_resq_lib->resq_send($post);
            }
        }

        // 出力データ
        $callback = [
            'message' => $message
        ];

        // 出力 json
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    /**
    * 個人勤務状況情報を返す
    *
    * @param int $user_id
    * @return array $data
    *
    */
    public function status()
    {
        $user_id = (int)$this->input->post('user_id');
        $today = new DateTime();
        $today_year = $today->format('Y');
        $today_month = $today->format('m');
        $today_day = $today->format('d');
        $today_hour = $today->format('H');
        $today_date = $today->format('Y-m-01');
        $this->load->helper('holiday_date');
        $week = array('日', '月', '火', '水', '木', '金', '土', '祝');
        $this->load->model('model_time_data');
        for ($mon = 0; $mon < 3; ++$mon) {
            $now = new DateTime($today_date);
            if ($mon > 0) {
                $now->sub(DateInterval::createFromDateString($mon.' month'));
            }
            $year = $now->format('Y');
            $month = $now->format('m');
            $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $now_month = $now->format('Ym');

            $select = 'dk_date, in_time, out_time, in_work_time, out_work_time, memo, revision, revision_in, revision_out';
            $where = [
                'user_id'=>$user_id,
                'date_format(dk_date, "%Y%m") = '=>$now_month
            ];
            $orderby = '';
            $time_data = $this->model_time_data->find($select, $where, $orderby);

            $time_data_w = [];
            if ($time_data) {
                foreach ($time_data as $val) {
                    $day = substr($val->dk_date, -2);
                    $time_data_w[(int)$day] = [
                        'in_time' => $val->in_time === null ? '' : substr($val->in_time, 0, 5),
                        'out_time' => $val->out_time === null ? '' : substr($val->out_time, 0, 5),
                        'in_work_time' => $val->in_work_time === null ? '' : substr($val->in_work_time, 0, 5),
                        'out_work_time' => $val->out_work_time === null ? '' : substr($val->out_work_time, 0, 5),
                        'memo' => $val->memo,
                        'revision' => $val->revision,
                        'revision_in' => $val->revision_in,
                        'revision_out' => $val->revision_out
                    ];
                }
            }
            for ($day = 1; $day <= $month_days; ++$day) {
                $dk_date = $year.'-'.$month.'-'.$day;
                $datetime = new DateTime($year.'-'.$month.'-'.$day);
                $holiday_datetime = new HolidayDateTime($year.'-'.$month.'-'.$day);
                $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
                $week_day = $week[$w];
                $today_flag = (int)$today_year === (int)$year && (int)$today_month === (int)$month && (int)$today_day === (int)$day ? 1 : 0;
                $in_time = $out_time = '';
                // 下記のやってることが不明
                if (isset($time_data_w[(int)$day]['in_time'])) {
                    $in_time = $time_data_w[(int)$day]['in_time'];
                    // もし、出勤時刻が修正されており(revision_in = 1)、＆ 修正後出勤時刻があれば、実出勤時刻に修正後出勤時刻を挿入？
                    if ($time_data_w[(int)$day]['revision_in'] == 1 && isset($time_data_w[(int)$day]['in_work_time'])) {
                        $in_time = $time_data_w[(int)$day]['in_work_time'];
                    }
                }
                if (isset($time_data_w[(int)$day]['out_time'])) {
                    $out_time = $time_data_w[(int)$day]['out_time'];
                    if ($time_data_w[(int)$day]['revision_out'] == 1 && isset($time_data_w[(int)$day]['out_work_time'])) {
                        $out_time = $time_data_w[(int)$day]['out_work_time'];
                    }
                    if ($out_time === '' && $today_flag === 1) {
                        $out_time = '勤務中';
                    }
                }
                $data[$mon][] = [
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'week' => $week_day,
                    'w' => $w,
                    'in_time' => $in_time,
                    'out_time' => $out_time,
                    'in_work_time' => @$time_data_w[(int)$day]['in_work_time'] ?: '',
                    'out_work_time' => @$time_data_w[(int)$day]['out_work_time'] ?: '',
                    'memo' => @$time_data_w[(int)$day]['memo'] ?: '',
                    'today_flag' => $today_flag,
                    'revision' => @$time_data_w[(int)$day]['revision'] ?: '',
                    'date' => $datetime->format('Y-m-d'),
                    'revision_in' => @$time_data_w[(int)$day]['revision_in'] ?: '',
                    'revision_out' => @$time_data_w[(int)$day]['revision_out'] ?: ''
                ];
            }
        }
        $this->output // 出力 json
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    // シフト状況を返す
    public function shift()
    {
    $user_id = $this->input->post('user_id');
    $today = new DateTime();
    $today_year = $today->format('Y');
    $today_month = $today->format('m');
    $today_day = $today->format('d');
    $this->load->helper('holiday_date');
    $week = array('日', '月', '火', '水', '木', '金', '土', '祝');
    for ($mon = 0; $mon <= 2; ++$mon) {
    $now = new DateTime();
    $now->add(DateInterval::createFromDateString('1 month')); // ver2 から
    if ($mon > 0) {
    $now->sub(DateInterval::createFromDateString($mon.' month'));
    }
    $year = $now->format('Y');
    $month = $now->format('m');
    $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $this->load->model('model_shift');
    $shift_data = $this->model_shift->gets_status_month_userid($year, $month, $user_id);
    $shift_data_w = [];
    $hour = $hour2 = '';
    if (isset($shift_data)) {
    foreach ($shift_data as $row) {
    $day = substr($row->dk_date, -2);
    $shift_status = $shift_in_time = $shift_out_time = $shift_hour = $shift_hour2 = $shift_rest = $shift_rest2 = '';
    if ($row->status == 0) {
    $shift_status = '出勤';
    $shift_in_time = $row->in_time === null ? '' : substr($row->in_time, 0, 5);
    $shift_out_time = $row->out_time === null ? '' : substr($row->out_time, 0, 5);
    if ($row->hour > 0) {
    $shift_hour = sprintf('%d:%02d', floor($row->hour/60), $row->hour%60);
    $shift_hour2 = $row->hour;
    }
    if ($row->rest > 0) {
    $shift_rest = sprintf('%d:%02d', floor($row->rest/60), $row->rest%60);
    $shift_rest2 = $row->rest;
    }
    }
    if ($row->status == 1) {
    $shift_status = '公休';
    }
    if ($row->status == 2) {
    $shift_status = '有給';
    }
    $shift_data_w[(int)$day]['shift_status'] = $shift_status;
    $shift_data_w[(int)$day]['shift_in_time'] = $shift_in_time;
    $shift_data_w[(int)$day]['shift_out_time'] = $shift_out_time;
    $shift_data_w[(int)$day]['shift_hour'] = $shift_hour;
    $shift_data_w[(int)$day]['shift_hour2'] = $shift_hour2;
    $shift_data_w[(int)$day]['shift_rest'] = $shift_rest;
    $shift_data_w[(int)$day]['shift_rest2'] = $shift_rest2;
    }
    }

    // config data取得
    $this->load->model('model_config_values');
    $where = [];
    $result = $this->model_config_values->find('id, config_name, value', $where, '');
    $config_data = array_column($result, 'value', 'config_name');

    if ((int)$config_data['auto_shift_flag'] === 1) {
    // ルールの取得
    $this->load->library('process_rules_lib'); // rules lib 読込
    $rules = $this->process_rules_lib->get_rule($user_id);

    //
    if ($rules) {
    if ($rules->basic_in_time) {
    $basic_in_time = substr($rules->basic_in_time, 0, 5);
    }
    if ($rules->basic_out_time) {
    $basic_out_time = substr($rules->basic_out_time, 0, 5);
    }
    if ($basic_in_time && $basic_out_time) {
    $hour2 = (strtotime($basic_out_time) - strtotime($basic_in_time)) / 60;
    $hour = sprintf('%d:%02d', floor($hour2/60), $hour2%60);
    }
    if ($rules->basic_rest_weekday) {
    $basic_rest_week = str_split($rules->basic_rest_weekday);
    }
    }
    }

    $i = 0;
    for ($day = 1; $day <= $month_days; ++$day) {
    $dk_date = $year.'-'.$month.'-'.$day;
    $datetime = new DateTime($year.'-'.$month.'-'.$day);
    $holiday_datetime = new HolidayDateTime($year.'-'.$month.'-'.$day);
    $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
    $week_day = $week[$w];

    $today_flag = (int) $today_year === (int) $year && (int) $today_month === (int) $month && (int) $today_day === (int) $day ? 1 : 0;

    $status = '未登録';
    $shift_in_time = '';
    $shift_out_time = '';
    if (isset($basic_rest_week)) {
    if ($basic_rest_week[$w] == 1) {
    $status = '公休';
    }
    if ($basic_rest_week[$w] == 0) {
    $status = '出勤';
    $shift_in_time = $basic_in_time;
    $shift_out_time = $basic_out_time;
    }
    }
    $data[$mon][$i] = [
    'year' => $year,
    'month' => $month,
    'day'=> $day,
    'week'=> $week_day,
    'w' => $w,
    'status'=> isset($shift_data_w[$day]['shift_status']) ? $shift_data_w[$day]['shift_status'] : $status,
    'in_time'=> isset($shift_data_w[$day]['shift_in_time']) ? $shift_data_w[$day]['shift_in_time'] : $shift_in_time,
    'out_time'=> isset($shift_data_w[$day]['shift_out_time']) ? $shift_data_w[$day]['shift_out_time'] : $shift_out_time,
    'hour'=> isset($shift_data_w[$day]['shift_hour']) ? $shift_data_w[$day]['shift_hour'] : $hour,
    'hour2'=> isset($shift_data_w[$day]['shift_hour2']) ? $shift_data_w[$day]['shift_hour2'] : $hour2,
    'rest'=> isset($shift_data_w[$day]['shift_rest']) ? $shift_data_w[$day]['shift_rest'] : '',
    'rest2'=> isset($shift_data_w[$day]['shift_rest2']) ? $shift_data_w[$day]['shift_rest2'] : '',
    'today_flag' => $today_flag
    ];
    $i++;
    }
    }
    $this->output // 出力 json
    ->set_content_type('application/json')
    ->set_output(json_encode($data));
    }

    /**
    * 休憩登録　処理
    */
    public function insert_rest()
    {
    $data['user_id'] = $this->input->post('user_id');
    $user_name = $this->input->post('user_name');
    $flag = $this->input->post('flag'); // 休憩開始 or 休憩終了　判別用フラグ

    $now = new DateTime(); // 時刻データ取得
    $data['rest_date'] = $now->format('Y-m-d'); // 入力日

    // 出退勤データを取得
    $this->load->model('model_time'); // model time 読込
    $now_time_data = $this->model_time->get_day_userid($data['rest_date'], $data['user_id']);
    $data['time_data_id'] = (int)$now_time_data->id;

    $this->load->model('model_rest_data'); // model rest_data 読込
    if ($flag === 'in') {
    $data['in_time'] = $now->format('H:i:s');
    $in_time = $now->format('H時i分');
    $data['flag'] = 0;
    if ($this->model_rest_data->insert($data)) {
    $message = $user_name.' '.$in_time.' 休憩開始';
    } else {
    $message = 'err';
    }
    }
    if ($flag === 'out') {
    $now_rest_data = $this->model_rest_data->get_day_userid($data['rest_date'], $data['user_id']);
    $data['id'] = $now_rest_data->id;
    $rest_in_time = $now_rest_data->in_time;
    $data['out_time'] = $now->format('H:i:s');
    $out_time = $now->format('H時i分');
    $data['rest_hour'] = (strtotime($data['out_time']) - strtotime($rest_in_time)) / 60;
    $data['flag'] = 1;
    if ($this->model_rest_data->update($data) === false) {
    $message = 'err';
    }
    // 対象の全ての休憩時間を取得
    $all_rest_data = $this->model_rest_data->get_timeid_all_reathour($data['time_data_id']);
    $data_time['id'] = $data['time_data_id'];
    $data_time['in_work_time'] = $now_time_data->in_work_time;
    $data_time['in_flag'] = 1;
    $data_time['status'] = $now_time_data->status;
    $data_time['status_flag'] = $now_time_data->status_flag;
    $data_time['left_hour'] = $now_time_data->left_hour;
    $data_time['late_hour'] = $now_time_data->late_hour;
    $data_time['night_hour'] = $now_time_data->night_hour;
    $data_time['area_id'] = $now_time_data->area_id;
    $data_time['rest'] = array_sum(array_column($all_rest_data, 'rest_hour'));
    if ($this->model_time->update_data($data_time)) {
    $message = $user_name.' '.$out_time.' 休憩終了';
    } else {
    $message = 'err';
    }
    }

    // LINE通知

    // config data取得
    $this->load->model('model_config_values');
    $where = [];
    $result = $this->model_config_values->find('id, config_name, value', $where, '');
    $config_data = array_column($result, 'value', 'config_name');

    if ((int)$config_data['line_flag'] === 1) { // line_flag = 1 -> LINE通知
    $line_message = [
    'type' => 'text',
    'text' => $message
    ];
    $this->load->library('process_line_lib'); // LINE通知用 lib 読込
    $this->process_line_lib->line_message($line_message, $data['user_id']);
    }
    // mail通知
    // notice_mail_flag = 1 メールのみ　notice_mail_flag = 9 すべて
    if ((int)$config_data['notice_mail_flag'] === 1 || (int)$config_data['notice_mail_flag'] === 9) {
    $mail_subject = '休憩通知';
    $this->load->library('process_mail_lib');
    $this->process_mail_lib->mail_send($mail_subject, $message);
    }

    // 出力データ
    $callback = [
    'message' => $message,
    ];

    // 出力 json
    $this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($callback));
    }

    public function insert_message()
    {
    $now = new DateTime();
    $now_date = $now->format('Y-m-d H:i:s');
    $message_data = [
    'type'=> $this->input->post('type'),
    'user_id'=> $this->input->post('user_id'),
    'message'=> $this->input->post('message'),
    'created_at'=> $now_date,
    'updated_at'=> $now_date
    ];
    $this->load->database();
    $this->db->set($message_data);
    if ($this->db->insert('message_data')) {
    $message = 'ok';
    } else {
    $message = 'err';
    }
    $callback = [
    'message'=>$message
    ];
    $this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($callback));
    }
}
