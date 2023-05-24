<?php

defined('BASEPATH') or exit('No direct script access alllowed');

class UserStatus extends CI_Controller
{
    /**
    *  指定ID、指定期間の従業員勤務データを返す
    *  期間の指定がない場合は当日のデータを返す
    *  @param string $user_id required
    *  @param string year YYYY
    *  @param string month YYYY-MM
    *  @param string date YYYY-MM-DD
    *  @param array period {first_date, end_date}
    *  @return array 従業員勤務データ
    */
    public function get()
    {
        // エラーで返す
        function error($message) {
            $userStatusData['message'] = $message;
            $userStatusData['type'] = "error";
            $ci =& get_instance();
            $ci->output
            ->set_content_type('application/json')
            ->set_output(json_encode($userStatusData));
        }

        $userStatusData = [];
        if (empty($this->input->post('user_id'))) {
            error("従業員IDは必須項目です");
            return;
        }
        $user_id = $this->input->post('user_id');

        $now = new DateTime();
        $this_year = $now->format('Y');
        $this_month = $now->format('Y-m');
        $this_day = $now->format('Y-m-d');
        if ($this->input->post('year')) {
            $year = $this->input->post('year');
            if ((int)$year <= 1980 || (int)$year >= 2100) {
                error("日付形式が間違っています");
                return;
            }
            $first_date = $year.'-01-01';
            $end_date = $year.'-12-31';
        }
        elseif ($this->input->post('month')) {
            $month = $this->input->post('month');
            $first_date = date('Y-m-d', strtotime('first day of ' . $month));
            $end_date = date('Y-m-d', strtotime('last day of ' . $month));
        }
        elseif ($this->input->post('date')) {
            $first_date = $this->input->post('date');
            $end_date = $this->input->post('date');
        }
        elseif ($this->input->post('first_date') && $this->input->post('end_date')) {
            $first_date = $this->input->post('first_date');
            $end_date = $this->input->post('end_date');
        }
        else {
            $first_date = $now->format('Y-m-d');
            $end_date = $now->format('Y-m-d');
        }

        $userStatusData['getTimes']['first'] = $first_date;
        $userStatusData['getTimes']['end'] = $end_date;
        $userStatusData['getTimes']['get_datetime'] = $now->format('Y-m-d H:i:s');
        $userStatusData['user']['user_id'] = $user_id;

        $this->load->database();
        $from = 'config_values';
        $select = 'config_name, value';
        $query = $this->db->query("SELECT {$select} FROM {$from}");
        $result = $query->result();
        $config_data = array_column($result, 'value', 'config_name');

        $basic_in_time = '';
        $basic_out_time = '';
        $basic_rest_week = [];
        $shift_rest = 0;
        if ($config_data['auto_shift_flag'] === '1') {
                $this->load->library('process_rules_lib');
                $rules = $this->process_rules_lib->get_rule($user_id);
            if (isset($rules->basic_in_time)) {
                $basic_in_time = substr($rules->basic_in_time, 0, 5);
                $basic_in_h = substr($rules->basic_in_time, 0, 2);
                $basic_in_m = substr($rules->basic_in_time, 3, 2);
            }
            if (isset($rules->basic_out_time)) {
                $basic_out_time = substr($rules->basic_out_time, 0, 5);
                $basic_out_h = substr($rules->basic_out_time, 0, 2);
                $basic_out_m = substr($rules->basic_out_time, 3, 2);
            }
            if (isset($rules->basic_rest_weekday)) {
                $basic_rest_week = str_split($rules->basic_rest_weekday);
            }
            if (isset($rules->rest_rule_flag)) {
                if ((int)$rules->rest_rule_flag === 1) {
                    $select = 'rest_time';
                    $where = ['config_rules_id'=>$rules->id];
                    $this->load->model('model_rest_rules');
                    $shift_rest = (int)$this->model_rest_rules->find_row($select, $where)->rest_time;
                }
            }
        }

        $from = 'area_data';
        $select = 'id, area_name';
        $query = $this->db->query("SELECT {$select} FROM {$from}");
        $result = $query->result();
        $area_name = array_column($result, 'area_name', 'id');

        $from = 'shift_data';
        $select = 'dk_date, in_time, out_time, status';
        $query = $this->db->query("SELECT {$select} FROM {$from} WHERE user_id = \"{$user_id}\" and dk_date >= \"{$first_date}\" and dk_date <= \"{$end_date}\"");
        $result = $query->result();
        $shift_data = array_column($result, NULL, 'dk_date');

        $from = 'time_data';
        $select = 'dk_date, in_time, out_time, in_work_time, out_work_time, rest, fact_work_hour, status, over_hour, night_hour, left_hour, late_hour, area_id, memo, notice_memo, status_flag, revision_in, revision_out';
        $query = $this->db->query("SELECT {$select} FROM {$from} WHERE user_id = \"{$user_id}\" and dk_date >= \"{$first_date}\" and dk_date <= \"{$end_date}\"");
        $result = $query->result();
        $time_data = array_column($result, NULL, 'dk_date');

        $from = 'notice_data';
        $query = $this->db->query("SELECT * FROM {$from} WHERE to_user_id = \"{$user_id}\" and to_date >= \"{$first_date}\" and to_date <= \"{$end_date}\"");
        $notice_data = $query->result();
        // $notice_data = array_column($result, NULL, 'to_date');

        $from = 'notice_status_data';
        $select = 'notice_status_id, notice_status_title';
        $query = $this->db->query("SELECT {$select} FROM {$from}");
        $result = $query->result();
        $notice_title_data = array_column($result, 'notice_status_title', 'notice_status_id');

        $user_works_hour = ""; // 総労働時間 0:00
        $user_works_hour2 = 0; // 総労働時間 min
        $user_works_num = 0; // 総出勤数
        $user_holiday_num = 0; // 総公休数
        $user_paid_num = 0; // 総有給数
        $user_absent_num = 0; // 総欠勤数
        $user_error_num = 0; // 入力エラー数
        $this->load->helper('holiday_date');
        $interval = new DateInterval('P1D');
        $per = new DatePeriod(new DateTime($first_date), $interval, new DateTime($end_date.' 00:00:01'));
        foreach ($per as $index => $datetime) {
            $this_date = $datetime->format('Y-m-d');
            $userStatusData['data'][$index]['date'] = $this_date;
            $userStatusData['data'][$index]['year'] = $datetime->format('Y');
            $userStatusData['data'][$index]['month'] = $datetime->format('m');
            $userStatusData['data'][$index]['day'] = $datetime->format('d');
            $userStatusData['data'][$index]['this_year'] = false;
            $userStatusData['data'][$index]['this_month'] = false;
            $userStatusData['data'][$index]['this_day'] = false;
            if ($this_year === $datetime->format('Y')) {
                $userStatusData['data'][$index]['this_year'] = true;
            }
            if ($this_month === $datetime->format('Y-m')) {
                $userStatusData['data'][$index]['this_month'] = true;
            }
            if ($this_day === $this_date) {
                $userStatusData['data'][$index]['this_day'] = true;
            }
            $holiday_datetime = new HolidayDateTime($this_date);
            $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
            $userStatusData['data'][$index]['week'] = ['日', '月', '火', '水', '木', '金', '土', '祝'][$w];

            // shift data ->
            $userStatusData['data'][$index]['shift'] = false;
            if ($config_data['auto_shift_flag'] === '1') {
                if ($basic_rest_week) {
                    $userStatusData['data'][$index]['shift'] = true;
                    $userStatusData['data'][$index]['shift_type'] = 'auto';
                    if ($basic_rest_week[$w] == 1) {
                        $shift_status = '公休';
                        $userStatusData['data'][$index]['shift_status_id'] = 1;
                    }
                    if ($basic_rest_week[$w] == 0 && $basic_in_time && $basic_out_time) {
                        $shift_status = '出勤';
                        $userStatusData['data'][$index]['shift_in_time'] = substr($basic_in_time, 0, 5);
                        $userStatusData['data'][$index]['shift_out_time'] = substr($basic_out_time, 0, 5);
                        $userStatusData['data'][$index]['shift_status_id'] = 0;
                    }
                    $userStatusData['data'][$index]['shift_status'] = @$shift_status ?: "";
                }
            }
            if (isset($shift_data[$this_date])) {
                $userStatusData['data'][$index]['shift'] = true;
                $userStatusData['data'][$index]['shift_type'] = 'input';
                $shift_status_id = (int)$shift_data[$datetime->format('Y-m-d')]->status;
                $userStatusData['data'][$index]['shift_status'] = ['出勤', '公休', '有給'][$shift_status_id];
                $userStatusData['data'][$index]['shift_status_id'] = $shift_status_id;
                $userStatusData['data'][$index]['shift_in_time'] = substr($shift_data[$datetime->format('Y-m-d')]->in_time, 0, 5);
                $userStatusData['data'][$index]['shift_out_time'] = substr($shift_data[$datetime->format('Y-m-d')]->out_time, 0, 5);
            }

            // time data ->
            if (empty($time_data[$this_date])) {
                $userStatusData['data'][$index]['time'] = false;
            } else {
                $userStatusData['data'][$index]['time'] = true;
                $userStatusData['data'][$index]['status'] = isset($time_data[$this_date]->status) ? $time_data[$this_date]->status : "";
                $userStatusData['data'][$index]['in_time'] = isset($time_data[$this_date]->in_time) ? substr($time_data[$this_date]->in_time, 0, 5) : "";
                $userStatusData['data'][$index]['out_time'] = isset($time_data[$this_date]->out_time) ? substr($time_data[$this_date]->out_time, 0, 5) : "";
                $userStatusData['data'][$index]['in_work_time'] = isset($time_data[$this_date]->in_work_time) ? substr($time_data[$this_date]->in_work_time, 0, 5) : "";
                $userStatusData['data'][$index]['out_work_time'] = isset($time_data[$this_date]->out_work_time) ? substr($time_data[$this_date]->out_work_time, 0, 5) : "";
                if (isset($time_data[$this_date]->fact_work_hour)) {
                        $fact_work_hour2 = (int)$time_data[$this_date]->fact_work_hour;
                    if ($fact_work_hour2 > 0) {
                        $fact_work_hour = sprintf('%d:%02d', floor($fact_work_hour2/60), $fact_work_hour2%60);
                        $user_works_hour2 += $fact_work_hour2;
                    } else {
                        $fact_work_hour = "0:00";
                    }
                }
                $userStatusData['data'][$index]['fact_work_hour'] = isset($fact_work_hour) ? $fact_work_hour : "";
                $userStatusData['data'][$index]['fact_work_hour2'] = isset($fact_work_hour2) ? $fact_work_hour2 : "";
                if (isset($time_data[$this_date]->rest)) {
                    $rest2 = (int)$time_data[$this_date]->rest;
                    if ($rest2 > 0) {
                        $rest = sprintf('%d:%02d', floor($rest2/60), $rest2%60);
                    } else {
                        $rest = "0:00";
                    }
                }
                $userStatusData['data'][$index]['rest'] = isset($rest) ? $rest : "";
                $userStatusData['data'][$index]['rest2'] = isset($rest2) ? $rest2 : "";
                if (isset($time_data[$this_date]->over_hour)) {
                    $over_hour2 = (int)$time_data[$this_date]->over_hour;
                    if ($over_hour2 > 0) {
                        $over_hour = sprintf('%d:%02d', floor($over_hour2/60), $over_hour2%60);
                    }
                }
                $userStatusData['data'][$index]['over_hour'] = isset($over_hour) ? $over_hour : "";
                $userStatusData['data'][$index]['over_hour2'] = isset($over_hour2) ? $over_hour2 : "";
                if (isset($time_data[$this_date]->night_hour)) {
                    $night_hour2 = (int)$time_data[$this_date]->night_hour;
                    if ($night_hour2 > 0) {
                        $night_hour = sprintf('%d:%02d', floor($night_hour2/60), $night_hour2%60);
                    }
                }
                $userStatusData['data'][$index]['night_hour'] = isset($night_hour) ? $night_hour : "";
                $userStatusData['data'][$index]['night_hour2'] = isset($night_hour2) ? $night_hour2 : "";
                if (isset($time_data[$this_date]->left_hour)) {
                    $left_hour2 = (int)$time_data[$this_date]->left_hour;
                    if ($left_hour2 > 0) {
                        $left_hour = sprintf('%d:%02d', floor($left_hour2/60), $left_hour2%60);
                    }
                }
                $userStatusData['data'][$index]['left_hour'] = isset($left_hour) ? $left_hour : "";
                $userStatusData['data'][$index]['left_hour2'] = isset($left_hour2) ? $left_hour2 : "";
                if (isset($time_data[$this_date]->late_hour)) {
                    $late_hour2 = (int)$time_data[$this_date]->late_hour;
                    if ($late_hour2 > 0) {
                        $late_hour = sprintf('%d:%02d', floor($late_hour2/60), $late_hour2%60);
                    }
                }
                $userStatusData['data'][$index]['late_hour'] = isset($late_hour) ? $late_hour : "";
                $userStatusData['data'][$index]['late_hour2'] = isset($late_hour2) ? $late_hour2 : "";
                $userStatusData['data'][$index]['memo'] = isset($time_data[$this_date]->memo) ? $time_data[$this_date]->memo : "";
                $userStatusData['data'][$index]['notice_memo'] = isset($time_data[$this_date]->notice_memo) ? $time_data[$this_date]->notice_memo : "";
                $userStatusData['data'][$index]['area_id'] = isset($time_data[$this_date]->area_id) ? $time_data[$this_date]->area_id : "";
                $userStatusData['data'][$index]['area'] = isset($time_data[$this_date]->area_id) ? $area_name[$time_data[$this_date]->area_id] : "";
                if ($config_data['gateway_status_view_flag'] == 0) { // gateway 0=実出退勤表示
                    $userStatusData['data'][$index]['gateway_view_in_time'] = @$userStatusData['data'][$index]['in_time'] ?: "";
                    if ($time_data[$this_date]->revision_in == 1) {
                        $userStatusData['data'][$index]['gateway_view_in_time'] = @$userStatusData['data'][$index]['in_work_time'] ?: "";
                    }
                    $userStatusData['data'][$index]['gateway_view_out_time'] = @$userStatusData['data'][$index]['out_time'] ?: "";
                    if ($time_data[$this_date]->revision_out == 1) {
                        $userStatusData['data'][$index]['gateway_view_out_time'] = @$userStatusData['data'][$index]['out_work_time'] ?: "";
                    }
                }
                if ($config_data['gateway_status_view_flag'] == 1) { // gateway 1=出退勤表示
                    $userStatusData['data'][$index]['gateway_view_in_time'] = @$userStatusData['data'][$index]['in_work_time'] ?: "";
                    $userStatusData['data'][$index]['gateway_view_out_time'] = @$userStatusData['data'][$index]['out_work_time'] ?: "";
                }
                if ($config_data['gateway_status_view_flag'] == 2) { // gateway 2=表示しない
                    $userStatusData['data'][$index]['gateway_view_in_time'] = "";
                    $userStatusData['data'][$index]['gateway_view_out_time'] = "";
                }
                if ($config_data['mypage_my_inout_view_flag'] == 0) { // MyPage マイ出勤状況 0=実出退勤表示
                    $userStatusData['data'][$index]['mypage_my_view_in_time'] = @$userStatusData['data'][$index]['in_time'] ?: "";
                    $userStatusData['data'][$index]['mypage_my_view_out_time'] = @$userStatusData['data'][$index]['out_time'] ?: "";
                }
                if ($config_data['mypage_my_inout_view_flag'] == 1) { // MyPage マイ出勤状況 1=出退勤表示
                    $userStatusData['data'][$index]['mypage_my_view_in_time'] = @$userStatusData['data'][$index]['in_work_time'] ?: "";
                    $userStatusData['data'][$index]['mypage_my_view_out_time'] = @$userStatusData['data'][$index]['out_work_time'] ?: "";
                }
                if ($config_data['mypage_my_inout_view_flag'] == 2) { // MyPage マイ出勤状況 2=表示しない
                    $userStatusData['data'][$index]['mypage_my_view_in_time'] = "";
                    $userStatusData['data'][$index]['mypage_my_view_out_time'] = "";
                }
                if ($config_data['mypage_status_inout_view_flag'] == 0) { // MyPage 従業員勤務状況 0=実出退勤表示
                    $userStatusData['data'][$index]['mypage_staff_view_in_time'] = @$userStatusData['data'][$index]['in_time'] ?: "";
                    $userStatusData['data'][$index]['mypage_staff_view_out_time'] = @$userStatusData['data'][$index]['out_time'] ?: "";
                }
                if ($config_data['mypage_status_inout_view_flag'] == 1) { // MyPage 従業員勤務状況 1=出退勤表示
                    $userStatusData['data'][$index]['mypage_staff_view_in_time'] = @$userStatusData['data'][$index]['in_work_time'] ?: "";
                    $userStatusData['data'][$index]['mypage_staff_view_out_time'] = @$userStatusData['data'][$index]['out_work_time'] ?: "";
                }
                if ($config_data['mypage_status_inout_view_flag'] == 2) { // MyPage 従業員勤務状況 2=表示しない
                    $userStatusData['data'][$index]['mypage_staff_view_in_time'] = "";
                    $userStatusData['data'][$index]['mypage_staff_view_out_time'] = "";
                }
                $working_flag = false;
                $workOn_flag = false;
                $holiday_flag = false;
                $paid_flag = false;
                $absent_flag = false;
                $error_flag = false;
                if (isset($time_data[$this_date]->status_flag)) {
                    $status_flag = $time_data[$this_date]->status_flag;
                    $working = [1, 7, 8, 23, 30]; // 出勤中
                    $workOn = [2, 4, 5, 6, 12, 13, 14, 15, 16, 17, 18, 19, 20, 24, 25, 26, 27, 31, 32, 33, 34, 37, 38, 39, 40, 43, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 60, 61, 64, 65, 66, 67, 71, 72, 73, 74]; // 勤務
                    $holiday = [22, 68]; // 公休
                    $paid = [29, 75]; // 有給
                    $absent = [59]; // 欠勤
                    $err = [21, 28, 35, 36, 41, 44, 45, 62, 63, 69, 70, 76]; //入力エラー
                    if (in_array($status_flag, $working)) {
                        $working_flag = true;
                    }
                    if (in_array($status_flag, $workOn)) {
                        $user_works_num ++;
                        $workOn_flag = true;
                    }
                    if (in_array($status_flag, $holiday)) {
                        $user_holiday_num ++;
                        $holiday_flag = true;
                    }
                    if (in_array($status_flag, $absent)) {
                        $user_absent_num ++;
                        $absent_flag = true;
                    }
                    if (in_array($status_flag, $paid)) {
                        $user_paid_num ++;
                        $paid_flag = true;
                    }
                    if (in_array($status_flag, $err)) {
                        $user_error_num ++;
                        $error_flag = true;
                    }
                    $userStatusData['data'][$index]['status_flag'] = $status_flag;
                }
                $userStatusData['data'][$index]['working'] = $working_flag;
                $userStatusData['data'][$index]['workOn'] = $workOn_flag;
                $userStatusData['data'][$index]['holiday'] = $holiday_flag;
                $userStatusData['data'][$index]['paid'] = $paid_flag;
                $userStatusData['data'][$index]['absent'] = $absent_flag;
                $userStatusData['data'][$index]['error'] = $error_flag;
            }

            // notice data ->
            $userStatusData['data'][$index]['notice'] = false;
            foreach ($notice_data as $key => $value) {
                if ($value->to_date === $this_date) {
                    $userStatusData['data'][$index]['notice'] = true;
                    // $userStatusData['data'][$index]['notice_data'][$key] = $value;
                    $userStatusData['data'][$index]['notice_data'][] = [
                        'notice_title'=> $notice_title_data[$value->notice_flag],
                        'notice_flag'=> $value->notice_flag,
                        'status'=> ['申請中', '承認', 'NG'][$value->notice_status],
                        'notice_status_flag'=> $value->notice_status,
                        'datetime'=>$value->notice_datetime,
                        'notice_in_time'=>$value->notice_in_time,
                        'notice_out_time'=>$value->notice_out_time,
                        'end_date'=>$value->end_date,
                        'from_user_id'=>$value->from_user_id
                    ];
                }
            }
            // if (empty($notice_data[$this_date])) {
            //   $userStatusData['data'][$index]['notice'] = false;
            // } else {
            //   $userStatusData['data'][$index]['notice_data'] = $notice_data[$this_date];
            //   $userStatusData['data'][$index]['notice_title'] = isset($notice_data[$this_date]->notice_flag) ? $notice_title_data[$notice_data[$this_date]->notice_flag] : "";
            //   $userStatusData['data'][$index]['notice_status'] = isset($notice_data[$this_date]->notice_status) ? ['申請中', '承認', 'NG'][$notice_data[$this_date]->notice_status] : "";
            // }
        }

        $userStatusData['user']['all_works_hour2'] = $user_works_hour2;
        if ($user_works_hour2 > 0) {
            $userStatusData['user']['all_works_hour'] = sprintf('%d:%02d', floor($user_works_hour2/60), $user_works_hour2%60);
        }
        $userStatusData['user']['all_works_num'] = $user_works_num;
        $userStatusData['user']['all_holiday_num'] = $user_holiday_num;
        $userStatusData['user']['all_paid_num'] = $user_paid_num;
        $userStatusData['user']['all_absent_num'] = $user_absent_num;
        $userStatusData['user']['all_error_num'] = $user_error_num;

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($userStatusData));
    }
}
