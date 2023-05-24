<?php
defined('BASEPATH') or exit('No direct script access alllowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class Admin_shift extends MY_Controller
{
    public function table_users_data()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $now_date = $year.'-'.$month.'-'.$month_days;
        $config_data = $this->data['configs'];

        $this->load->model('model_group');
        $result = $this->model_group->find_group1_all();
        foreach ($result as $row) {
            $group1_name[$row->id] = $row->group_name;
        }
        $result = $this->model_group->find_group2_all();
        foreach ($result as $row) {
            $group2_name[$row->id] = $row->group_name;
        }
        $result = $this->model_group->find_group3_all();
        foreach ($result as $row) {
            $group3_name[$row->id] = $row->group_name;
        }
        $this->load->model('model_group_history');
        $result = $this->model_group_history->find_all();
        foreach ($result as $row) {
            if (new DateTime($row->to_date) <= new DateTime()) {
                $group1_id[$row->user_id] = $row->group1_id;
                $group2_id[$row->user_id] = $row->group2_id;
                $group3_id[$row->user_id] = $row->group3_id;
            }
        }
        if ((int)$config_data['auto_shift_flag']->value === 0) {
            $shift_data= [];
            $this->load->model('model_shift');
            if ($this->model_shift->gets_all_month($year, $month)) {
                $shift_data = $this->model_shift->gets_all_month($year, $month);
            }
            foreach ($shift_data as $key => $value) {
                if (isset($shift[$value->user_id])) {
                    $shift[$value->user_id]++;
                } else {
                    $shift[$value->user_id] = 1;
                }
            }
        }
        // 申請データ取得
        $select = 'user_id';
        $where = ['date_format(dk_date, "%Y%m") = ' => $year.$month, 'flag' => 1];
        $orderby = '';
        $this->load->model('model_shift_register_data');
        $result = $this->model_shift_register_data->find($select, $where, $orderby);
        foreach ($result as $value) {
            if (isset($register[$value->user_id])) {
                $register[$value->user_id]++;
            } else {
                $register[$value->user_id] = 1;
            }
        }

        $this->load->model('model_user');
        $users = $this->model_user->find_exist_all($now_date);
        $users_data = [];
        foreach ($users as $user) {
            $user_id = $user->user_id;
            $users_data[] = [
                'id'=> str_pad($user_id, (int)$config_data['id_size']->value, '0', STR_PAD_LEFT),
                'user_id'=>str_pad($user_id, (int)$config_data['id_size']->value, '0', STR_PAD_LEFT),
                'user_name'=>$user->name_sei.' '.$user->name_mei,
                'group1_name'=>@$group1_name[$group1_id[$user_id]] ?: '',
                'group2_name'=>@$group2_name[$group2_id[$user_id]] ?: '',
                'group3_name'=>@$group3_name[$group3_id[$user_id]] ?: '',
                'shift'=>@$shift[$user_id] ?: 0,
                'register'=>@$register[$user_id] ?: 0,
            ];
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($users_data));
    }

    public function table_shift_data()
    {
        $shift_data = [];
        $shift_data_w = [];
        $user_id = $this->input->post('user_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = (int)cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $week = array("日", "月", "火", "水", "木", "金", "土", "祝");
        $flag = $this->input->post('flag');

        $this->load->model('model_shift');
        $shift_db_data = $this->model_shift->gets_status_month_userid($year, $month, $user_id);
        if (isset($shift_db_data)) {
            foreach ($shift_db_data as $row) {
                $day = substr($row->dk_date, -2);
                $shift_status = $shift_in_time = $shift_out_time = $shift_hour = $shift_hour2 = $shift_rest = $shift_rest2 = '';
                if ($row->status == 0) {
                    $shift_status = '出勤';
                    $shift_in_time = $row->in_time === null ? '' : substr($row->in_time, 0, 5);
                    $shift_out_time = $row->out_time === null ? '' : substr($row->out_time, 0, 5);
                    if ($row->in_time !== null) {
                        $shift_in_h = substr($row->in_time, 0, 2);
                        $shift_in_m = substr($row->in_time, 3, 2);
                    }
                    if ($row->out_time !== null) {
                        $shift_out_h = substr($row->out_time, 0, 2);
                        $shift_out_m = substr($row->out_time, 3, 2);
                    }
                    if (isset($shift_in_h) && isset($shift_out_h)) {
                        $shift_hour_data[(int)$day] = [];
                        $color = '#9abcea';
                        for ($h = (int)$shift_in_h; $h <= (int)$shift_out_h; $h++) {
                            if ((int)$shift_in_h < $h && (int)$shift_out_h > $h) {
                                $shift_hour_data[(int)$day] += [$h.'_0' => $color];
                                $shift_hour_data[(int)$day] += [$h.'_30' => $color];
                            }
                            if ((int)$shift_in_h === $h) {
                                if ((int)$shift_in_m >= 30) {
                                    $shift_hour_data[(int)$day] += [$h.'_0' => ''];
                                    $shift_hour_data[(int)$day] += [$h.'_30' => $color];
                                } else {
                                    $shift_hour_data[(int)$day] += [$h.'_0' => $color];
                                    $shift_hour_data[(int)$day] += [$h.'_30' => $color];
                                }
                            }
                            if ((int)$shift_out_h === $h) {
                                if ((int)$shift_out_m < 30) {
                                    $shift_hour_data[(int)$day] += [$h.'_0' => ''];
                                    $shift_hour_data[(int)$day] += [$h.'_30' => ''];
                                } else {
                                    $shift_hour_data[(int)$day] += [$h.'_0' => $color];
                                    $shift_hour_data[(int)$day] += [$h.'_30' => ''];
                                }
                            }
                        }
                    }
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
        $config_data = $this->data['configs'];

        $rules = [];
        $basic_in_time = '';
        $basic_out_time = '';
        $basic_rest_week = [];
        $hour = $hour2 = '';
        $shift_rest = '';
        $shift_rest2 = 0;
        if ((int)$config_data['auto_shift_flag']->value === 1) {
            // ルールの取得
            $this->load->library('process_rules_lib'); // rules lib 読込
            $rules = $this->process_rules_lib->get_rule($user_id);
            //
            if (isset($rules->basic_in_time) && $rules->basic_in_time !== NULL) {
                $basic_in_time = substr($rules->basic_in_time, 0, 5);
                $basic_in_h = substr($rules->basic_in_time, 0, 2);
                $basic_in_m = substr($rules->basic_in_time, 3, 2);
            }
            if (isset($rules->basic_out_time) && $rules->basic_out_time !== NULL) {
                $basic_out_time = substr($rules->basic_out_time, 0, 5);
                $basic_out_h = substr($rules->basic_out_time, 0, 2);
                $basic_out_m = substr($rules->basic_out_time, 3, 2);
            }
            if ((int)$rules->rest_rule_flag === 1) {
                $select = 'rest_time';
                $where = ['config_rules_id'=>$rules->id];
                $this->load->model('model_rest_rules');
                $shift_rest2 = (int)$this->model_rest_rules->find_row($select, $where)->rest_time;
            }
            if ($basic_in_time && $basic_out_time) {
                $basic_hour_data = [];
                $color = '#9abcea';
                for ($h = (int)$basic_in_h; $h <= (int)$basic_out_h; $h++) {
                    if ((int)$basic_in_h < $h && (int)$basic_out_h > $h) {
                        $basic_hour_data[$h.'_0'] = $color;
                        $basic_hour_data[$h.'_30'] = $color;
                    }
                    if ((int)$basic_in_h === $h) {
                        if ((int)$basic_in_m >= 30) {
                            $basic_hour_data[$h.'_0'] = '';
                            $basic_hour_data[$h.'_30'] = $color;
                        } else {
                            $basic_hour_data[$h.'_0'] = $color;
                            $basic_hour_data[$h.'_30'] = $color;
                        }
                    }
                    if ((int)$basic_out_h === $h) {
                        if ((int)$basic_out_m < 30) {
                            $basic_hour_data[$h.'_0'] = '';
                            $basic_hour_data[$h.'_30'] = '';
                        } else {
                            $basic_hour_data[$h.'_0'] = $color;
                            $basic_hour_data[$h.'_30'] = '';
                        }
                    }
                }
            }
            if (isset($rules->basic_rest_weekday)) {
                $basic_rest_week = str_split($rules->basic_rest_weekday);
            }
        }

        $now_date = new DateTime();
        $register_shift_data = $this->model_shift->gets_register_month_userid($now_date->format('Y-m-d'), $user_id);
        $register_shift_date = [];
        if ($register_shift_data) {
            $register_shift_date = array_column($register_shift_data, 'dk_date');
        }

        $this->load->helper('holiday_date');
        $i = 0;
        for ($day = 1; $day <= $month_days; $day++) {
            $dk_date = $year.'-'.$month.'-'.$day;
            $datetime = new DateTime($dk_date);
            $holiday_datetime = new HolidayDateTime($dk_date);
            $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
            $week_day = $week[$w];

            $status = '未登録';
            $shift_in_time = '';
            $shift_out_time = '';
            $hour = '';
            $hour2 = 0;
            $rest = '';
            $rest2 = 0;
            if ($basic_rest_week) {
                if ($basic_rest_week[$w] === '1') {
                    $status = '公休';
                }
                if ($basic_rest_week[$w] === '0' && $basic_in_time && $basic_out_time) {
                    $status = '出勤';
                    $shift_in_time = $basic_in_time;
                    $shift_out_time = $basic_out_time;
                    $hour2 = (strtotime($basic_out_time) - strtotime($basic_in_time)) / 60;
                    $hour2 -= $shift_rest2;
                    $hour = sprintf('%d:%02d', floor($hour2/60), $hour2%60);
                    $rest = sprintf('%d:%02d', floor($shift_rest2/60), $shift_rest2%60);
                    $rest2 = $shift_rest2;
                }
            }

            if ($now_date->format('Y-m-d') < $datetime->format('Y-m-d')) {
                $date_name = 'shift_future';
            }
            if ($now_date->format('Y-m-d') > $datetime->format('Y-m-d')) {
                $date_name = 'shift_past';
            }
            if ($now_date->format('Y-m-d') === $datetime->format('Y-m-d')) {
                $date_name = 'shift_today';
            }
            if (in_array($datetime->format('Y-m-d'), $register_shift_date, true)) {
                $date_name .= ' in-register';
            }

            if ($flag === 'cal') {
                $status_w = isset($shift_data_w[$day]['shift_status']) ? $shift_data_w[$day]['shift_status'] : $status;
                if ($status_w === '出勤') {
                    $shift_id = 0;
                    if ($shift_in_time && $shift_out_time) {
                        $status_w = $shift_in_time.' - '.$shift_out_time;
                        $shift_id = $shift_in_time.':'.$shift_out_time;
                    }
                    if (isset($shift_data_w[$day]['shift_in_time']) && isset($shift_data_w[$day]['shift_out_time'])) {
                        $status_w = $shift_data_w[$day]['shift_in_time'].' - '.$shift_data_w[$day]['shift_out_time'];
                        $shift_id = $shift_data_w[$day]['shift_in_time'].':'.$shift_data_w[$day]['shift_out_time'];
                    }
                    $backgroud_color = '#3788d8';
                    $text_color = '#fff';
                }
                if ($status_w === '公休') {
                    $backgroud_color = '#ff7d73';
                    $text_color = '#fff';
                    $shift_id = 1;
                }
                if ($status_w === '有給') {
                    $backgroud_color = '#40a598';
                    $text_color = '#fff';
                    $shift_id = 2;
                }
                if ($status_w === '未登録') {
                    $backgroud_color = '#ccc';
                    $text_color = '#fff';
                    $shift_id = 0;
                }
                $shift_data[$i] = [
                    'title'=>'・'.$status_w,
                    'start'=>$datetime->format('Y-m-d'),
                    'color'=>$backgroud_color,
                    'textColor'=>$text_color,
                    'id'=>$shift_id,
                    'classNames'=>$date_name,
                    'extendedProps' => [
                        'status'=> isset($shift_data_w[$day]['shift_status']) ? $shift_data_w[$day]['shift_status'] : $status,
                        'in_time'=> isset($shift_data_w[$day]['shift_in_time']) ? $shift_data_w[$day]['shift_in_time'] : $shift_in_time,
                        'out_time'=> isset($shift_data_w[$day]['shift_out_time']) ? $shift_data_w[$day]['shift_out_time'] : $shift_out_time,
                        'hour'=> isset($shift_data_w[$day]['shift_hour']) ? $shift_data_w[$day]['shift_hour'] : $hour,
                        'hour2'=> isset($shift_data_w[$day]['shift_hour2']) ? $shift_data_w[$day]['shift_hour2'] : $hour2,
                        'rest'=> isset($shift_data_w[$day]['shift_rest']) ? $shift_data_w[$day]['shift_rest'] : $rest,
                        'rest2'=> isset($shift_data_w[$day]['shift_rest2']) ? $shift_data_w[$day]['shift_rest2'] : $rest2,
                        'date'=>$datetime->format('Y-m-d'),
                        'dateW'=>$datetime->format('Y年m月d日('.$week_day.')'),
                    ]
                ];
            }
            if ($flag === 'list') {
                $shift_data[$i] = [
                    'day'=> $day,
                    'week'=> $week_day,
                    'status'=> isset($shift_data_w[$day]['shift_status']) ? $shift_data_w[$day]['shift_status'] : $status,
                    'in_time'=> isset($shift_data_w[$day]['shift_in_time']) ? $shift_data_w[$day]['shift_in_time'] : $shift_in_time,
                    'out_time'=> isset($shift_data_w[$day]['shift_out_time']) ? $shift_data_w[$day]['shift_out_time'] : $shift_out_time,
                    'hour'=> isset($shift_data_w[$day]['shift_hour']) ? $shift_data_w[$day]['shift_hour'] : $hour,
                    'hour2'=> isset($shift_data_w[$day]['shift_hour2']) ? $shift_data_w[$day]['shift_hour2'] : $hour2,
                    'rest'=> isset($shift_data_w[$day]['shift_rest']) ? $shift_data_w[$day]['shift_rest'] : $rest,
                    'rest2'=> isset($shift_data_w[$day]['shift_rest2']) ? $shift_data_w[$day]['shift_rest2'] : $rest2,
                    'dateView'=>$date_name,
                    'date'=>$datetime->format('Y-m-d'),
                    'dateW'=>$datetime->format('Y年m月d日('.$week_day.')'),
                ];
                if (isset($shift_hour_data[$day])) {
                    foreach ($shift_hour_data[$day] as $key => $value) {
                        $shift_data[$i] += [$key => $value];
                    }
                }
                if (isset($shift_data_w[$day]['shift_status'])) {
                    if ($shift_data_w[$day]['shift_status'] !== '出勤') {
                        $status = $shift_data_w[$day]['shift_status'];
                    }
                }
                if (isset($shift_hour_data[$day]) === false && isset($basic_hour_data) && $status === '出勤') {
                    foreach ($basic_hour_data as $key => $value) {
                        $shift_data[$i] += [$key => $value];
                    }
                }
            }

            $i++;
        }
        $this->output // 出力
        ->set_content_type('application/json')
        ->set_output(json_encode($shift_data));
    }

    // 出勤状況データ　出力
    public function workStatus()
    {
        // config data取得
        $config_data = $this->data['configs'];

        $user_id = $this->input->post('user_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = (int)cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $first_date = $year.'-'.$month.'-01';
        $end_date = $year.'-'.$month.'-'.$month_days;
        $now_date = new DateTime();
        $now_year = $now_date->format('Y');
        $now_month = $now_date->format('m');
        if ($year == $now_year && $month == $now_month) {
            $end_date = $now_date->format('Y-m-d');
        }
        $data = [];
        $this->load->model('model_time');
        $time_data = $this->model_time->gets_to_end_date_user_id($first_date, $end_date, $user_id);
        if ($time_data) {
            foreach ($time_data as $row) {
                $in_time = $row->in_time === null ? '' : substr($row->in_time, 0, 5);
                if ($row->revision_in == 1) {
                    $in_time = $row->in_work_time === null ? '' : substr($row->in_work_time, 0, 5);
                }
                $out_time = $row->out_time === null ? '' : substr($row->out_time, 0, 5);
                if ($row->revision_out == 1) {
                    $out_time = $row->out_work_time === null ? '' : substr($row->out_work_time, 0, 5);
                }
                if ((int)$config_data['mypage_my_inout_view_flag']->value === 2) {
                    $in_time = $row->in_work_time === null ? '' : substr($row->in_work_time, 0, 5);
                    $out_time = $row->out_work_time === null ? '' : substr($row->out_work_time, 0, 5);
                }
                $time_w = '';
                if ($in_time) {
                    $time_w = "\n".$in_time.' - '.$out_time;
                }
                $status = $row->status;
                $text_color = '#9abcea';
                $color = 'rgba(255, 255, 255, 0)';
                if ($status === '公休') {
                    $text_color = '#F44336';
                }
                if ($status === '未出勤') {
                    $text_color = '#673ab7';
                }
                if(strpos($status,'片打刻') !== false){
                    $text_color = '#F44336';
                }
                if ($status === '有給' || $status === '有給取得') {
                    $text_color = '#fff';
                    $color = '#40a598';
                }
                if ($status === '欠勤') {
                    $text_color = '#fff';
                    $color = '#fd6b80';
                }
                $data[] = [
                    'title'=>$status.$time_w,
                    'start'=>$row->dk_date,
                    'color'=>$color,
                    'textColor'=>$text_color,
                    'classNames'=>'work-status'
                ];
            }
        }
        $this->output // 出力
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    // シフト申請データ 出力
    public function registerStatus()
    {
        $flag = $this->input->post('flag');
        $user_id = $this->input->post('user_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = (int)cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $first_date = $year.'-'.$month.'-01';
        $end_date = $year.'-'.$month.'-'.$month_days;
        $week = array("日", "月", "火", "水", "木", "金", "土", "祝");
        $now = new DateTime();
        $now_date = $now->format('Y-m-d');
        $now_yearmonth = $now->format('Ym');
        $register_shift = [];
        if ($now_yearmonth <= $year.$month) {
            $this->load->model('model_shift_register_data');
            $select = 'id, dk_date, shift_status, in_time, out_time, hour, rest';
            $orderby = '';
            $register_shift_data = [];
            $where = [
                'user_id'=>(int)$user_id,
                'date_format(dk_date, "%Y%m") = '=>$year.$month,
                'flag'=>1
            ];
            $register_shift_data = $this->model_shift_register_data->find($select, $where, $orderby);
            if ($flag === 'mypage') {
                $backgroud_color = '#1b97a8';
            }
            if ($flag === 'admin') {
                $backgroud_color = '#555555';
            }
            $this->load->helper('holiday_date');
            foreach ($register_shift_data as $row) {
                $status = (int)$row->shift_status;
                $hour = $rest = '';
                $hour2 = $rest2 = 0;
                $date = $row->dk_date;
                $datetime = new DateTime($date);
                $holiday_datetime = new HolidayDateTime($date);
                $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
                $week_day = $week[$w];
                if ($status === 0) {
                    $status_w = substr($row->in_time, 0, 5).' - '.substr($row->out_time, 0, 5);
                    $shift_id = substr($row->in_time, 0, 5).':'.substr($row->out_time, 0, 5);
                    $shift_status = '出勤';
                    $hour2 = $row->hour > 0 ? $row->hour : (strtotime($row->out_time) - strtotime($row->in_time)) / 60;
                    $hour = sprintf('%d:%02d', floor($hour2/60), $hour2%60);
                    $rest2 = $row->rest;
                    $rest = $rest2 > 0 ? sprintf('%d:%02d', floor($rest2/60), $rest2%60) : '';
                }
                if ($status === 1) {
                    $status_w = '公休';
                    $shift_id = 1;
                    $shift_status = '公休';
                }
                if ($status === 2) {
                    $status_w = '有給';
                    $shift_id = 2;
                    $shift_status = '有給';
                }
                $text_color = '#fff';
                $register_shift[] = [
                    'title'=>$status_w,
                    'start'=>$row->dk_date,
                    'color'=>$backgroud_color,
                    'textColor'=>$text_color,
                    'id'=>$shift_id,
                    'classNames'=>'register',
                    'extendedProps' => [
                        'status'=> $shift_status,
                        'in_time'=> substr($row->in_time, 0, 5),
                        'out_time'=> substr($row->out_time, 0, 5),
                        'hour'=> $hour,
                        'hour2'=> $hour2,
                        'rest'=> $rest,
                        'rest2'=> $rest2,
                        'date'=>$date,
                        'dateW'=>$datetime->format('Y年m月d日('.$week_day.')'),
                        'flag'=>'register',
                        'id'=>$row->id
                    ]
                ];
            }
        }

        $this->output // 出力
        ->set_content_type('application/json')
        ->set_output(json_encode($register_shift));
    }

    // カレンダー用　祝日データ
    public function calHoriday()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $first_date = new DateTime($year.'-'.$month.'-01');
        $end_date = new DateTime($year.'-'.$month.'-'.$month_days.' 00:00:01');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($first_date, $interval, $end_date);
        $this->load->helper('holiday_date');
        foreach ($period as $datetime) {
            $holiday_datetime = new HolidayDateTime($datetime->format('Y-m-d'));
            $data[] = [
                'date' => $datetime->format('Y-m-d'),
                'holiday' => $holiday_datetime->holiday() ? $holiday_datetime->holiday() : ''
            ];
        }
        $this->output // 出力
        ->set_content_type('application/json')
    ->set_output(json_encode($data));
    }

    // 登録用ファイルダウンロード
    public function downloadCsv()
    {
        // config data取得
        $config_data = $this->data['configs'];

        $user_id = $this->input->post('user_id');
        $name = $this->input->post('name');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $this->load->model('model_shift');
        if ((int)$user_id > 0) {
            $shift_db_data = $this->model_shift->gets_status_month_userid($year, $month, $user_id);
            if (isset($shift_db_data)) {
                foreach ($shift_db_data as $row) {
                    $shift_status = $row->status;
                    $in_date_h = $in_date_m = $out_date_h = $out_date_m = $shift_rest = '';
                    if ($row->status == 0) {
                        $shift_status = '';
                        $in_date_h = $row->in_time === null ? '' : substr($row->in_time, 0, 2);
                        $in_date_m = $row->in_time === null ? '' : substr($row->in_time, 3, 2);
                        $out_date_h = $row->out_time === null ? '' : substr($row->out_time, 0, 2);
                        $out_date_m = $row->out_time === null ? '' : substr($row->out_time, 3, 2);
                        if ($row->rest > 0) {
                            $shift_rest = $row->rest;
                        }
                    }
                    $day = substr($row->dk_date, -2);
                    $shift_data_w[(int)$day]['shift_status'] = $shift_status;
                    $shift_data_w[(int)$day]['in_date_h'] = (int)$in_date_h;
                    $shift_data_w[(int)$day]['in_date_m'] = (int)$in_date_m;
                    $shift_data_w[(int)$day]['out_date_h'] = (int)$out_date_h;
                    $shift_data_w[(int)$day]['out_date_m'] = (int)$out_date_m;
                    $shift_data_w[(int)$day]['shift_rest'] = $shift_rest;
                }
                //
                $rules = [];
                $basic_in_time = '';
                $basic_out_time = '';
                $basic_rest_week = [];
                if ((int)$config_data['auto_shift_flag']->value === 1) {
                    // ルールの取得
                    $this->load->library('process_rules_lib'); // rules lib 読込
                    $rules = $this->process_rules_lib->get_rule($user_id);
                    //
                    if ($rules->basic_in_time) {
                        $basic_in_time = substr($rules->basic_in_time, 0, 5);
                        $basic_in_h = substr($rules->basic_in_time, 0, 2);
                        $basic_in_m = substr($rules->basic_in_time, 3, 2);
                    }
                    if ($rules->basic_out_time) {
                        $basic_out_time = substr($rules->basic_out_time, 0, 5);
                        $basic_out_h = substr($rules->basic_out_time, 0, 2);
                        $basic_out_m = substr($rules->basic_out_time, 3, 2);
                    }
                    if ($basic_in_time && $basic_out_time) {
                        $hour2 = (strtotime($basic_out_time) - strtotime($basic_in_time)) / 60;
                        $hour = sprintf('%d:%02d', floor($hour2/60), $hour2%60);
                    } else {
                        $hour = $hour2 = '';
                    }
                    if ($rules->basic_rest_weekday) {
                        $basic_rest_week = str_split($rules->basic_rest_weekday);
                    }
                }
            }
        } else {
            $user_id = '';
            $name = '';
        }
        $this->load->helper('holiday_date');
        $week = array("日", "月", "火", "水", "木", "金", "土", "祝");
        $header = ['従業員ID', '名前', '年', '月', '日', '曜', '出勤(時)', '出勤(分)', '退勤(時)', '退勤(分)', '休憩時間(分)', '予定：1=公休：2=有休：空白=出勤'];
        for ($day = 1; $day <= $month_days; $day++) {
            $datetime = new DateTime($year.'-'.$month.'-'.$day);
            $holiday_datetime = new HolidayDateTime($year.'-'.$month.'-'.$day);
            $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
            $week_day = $week[$w];

            $status = '';
            $shift_in_h = '';
            $shift_in_m = '';
            $shift_out_h = '';
            $shift_out_m = '';
            if ($basic_rest_week) {
                if ($basic_rest_week[$w] == 1) {
                    $status = 1;
                }
                if ($basic_rest_week[$w] == 0) {
                    $status = '';
                    $shift_in_h = $basic_in_h;
                    $shift_in_m = $basic_in_m;
                    $shift_out_h = $basic_out_h;
                    $shift_out_m = $basic_out_m;
                }
            }
            $shift_status = isset($shift_data_w[$day]['shift_status']) ? $shift_data_w[$day]['shift_status'] : $status;
            $in_date_h = isset($shift_data_w[$day]['in_date_h']) ? $shift_data_w[$day]['in_date_h'] : $shift_in_h;
            $in_date_m = isset($shift_data_w[$day]['in_date_m']) ? $shift_data_w[$day]['in_date_m'] : $shift_in_m;
            $out_date_h = isset($shift_data_w[$day]['out_date_h']) ? $shift_data_w[$day]['out_date_h'] : $shift_out_h;
            $out_date_m = isset($shift_data_w[$day]['out_date_m']) ? $shift_data_w[$day]['out_date_m'] : $shift_out_m;
            $shift_rest = isset($shift_data_w[$day]['shift_rest']) ? $shift_data_w[$day]['shift_rest'] : '';
            $csv_shift_data[] = [
                $user_id, $name, (int)$year, (int)$month, (int)$day, $week_day, (int)$in_date_h, (int)$in_date_m, (int)$out_date_h, (int)$out_date_m, (int)$shift_rest, (int)$shift_status
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($header);
        $sheet->fromArray($csv_shift_data, null, 'A2');

        $file_name = 'シフト登録用('.$year.'年'.$month.'月)'.$name;
        if ((int)$config_data['download_filetype']->value == 1) {
            $file_type = 'xlsx';
            $writer = new Xlsx($spreadsheet);
        }
        if ((int)$config_data['download_filetype']->value == 2) {
            $file_type = 'xls';
            $writer = new Xls($spreadsheet);
        }
        if ((int)$config_data['download_filetype']->value == 3) {
            $file_type = 'csv';
            $writer = new Csv($spreadsheet);
            $writer->setUseBOM(true);
        }
        $writer->save('./files/'.$file_name.'.'.$file_type);

        $this->load->helper('download');
        force_download('./files/'.$file_name.'.'.$file_type, null);
    }

    // 登録用ファイル　アップロード
    public function uploadCsv()
    {
        // config data取得
        $config_data = $this->data['configs'];

        $config['upload_path'] = './files/';
        if ((int)$config_data['download_filetype']->value === 1) {
            $config['allowed_types'] = 'xlsx';
            $config['file_name'] = 'up_shift.xlsx';
        }
        if ((int)$config_data['download_filetype']->value === 2) {
            $config['allowed_types'] = 'xls';
            $config['file_name'] = 'up_shift.xls';
        }
        if ((int)$config_data['download_filetype']->value === 3) {
            $config['allowed_types'] = 'csv';
            $config['file_name'] = 'up_shift.csv';
        }
        $config['overwrite'] = true;
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('files')) {
            if ((int)$config_data['download_filetype']->value === 1) {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load('./files/up_shift.xlsx');
            }
            if ((int)$config_data['download_filetype']->value === 2) {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xls();
                $spreadsheet = $reader->load('./files/up_shift.xls');
            }
            if ((int)$config_data['download_filetype']->value === 3) {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Csv();
                $spreadsheet = $reader->load('./files/up_shift.csv');
            }
            $sheet = $spreadsheet->getActiveSheet()->toArray();
            $temp_userid = '';
            $user_name = '';
            $now = new DateTime();
            $now_date = $now->format('Y-m-d');
            foreach ($sheet as $val) {
                if ($val[0] === null || $val[0] === '' || !is_numeric($val[0])) {
                    continue;
                }
                if (!is_numeric($val[2]) && !is_numeric($val[3]) && !is_numeric($val[4])) {
                    continue;
                }
                if (checkdate($val[3], $val[4], $val[2]) === false) {
                    continue;
                }
                $shift_data['year'] = $val[2];
                $shift_data['month'] = $val[3];
                $shift_data['day'] = $val[4];
                $shift_data['dk_date'] = $val[2].'-'.$val[3].'-'.$val[4];
                $shift_data['in_time'] = null;
                $shift_data['out_time'] = null;
                $shift_data['rest'] = 0;
                $shift_data['hour'] = 0;
                $get_date = $shift_data['year'].'-'.$shift_data['month'].'-'.$shift_data['day'];

                if ($temp_userid !== $val[0]) {
                    $shift_data['user_id'] = (int)$val[0];
                    $temp_userid = (int)$val[0];
                    $this->load->model('model_user');
                    $user_check = $this->model_user->find_exist_userid($get_date, $shift_data['user_id']);
                    if (!$user_check) {
                    $message = '従業員IDエラー';
                        continue;
                    } else {
                        $user_name .= $user_check->name_sei.' '.$user_check->name_mei.',';
                    }
                }

                $shift_data['status'] = $val[11];
                if ($shift_data['status'] == null || $shift_data['status'] == '') {
                    if (!is_numeric($val[6]) && !is_numeric($val[7]) && !is_numeric($val[8]) && !is_numeric($val[9])) {
                        $message = '登録エラー';
                        continue;
                    }
                    if ($val[6] < 1 || $val[6] > 24 || $val[7] < 0 || $val[7] > 60 || $val[8] < 1 || $val[8] > 24 || $val[9] < 0 || $val[9] > 60) {
                        $message = '登録エラー';
                        continue;
                    }
                    if ($val[7] == '') {
                        $val[7] = 0;
                    }
                    if ($val[9] == '') {
                        $val[9] = 0;
                    }
                    if ($val[10] == '') {
                        $val[10] = 0;
                    }
                    $shift_data['status'] = 0;
                    $shift_data['in_time'] = strftime('%H:%M:%S', mktime($val[6], $val[7], 0));
                    $shift_data['out_time'] = strftime('%H:%M:%S', mktime($val[8], $val[9], 0));
                    $shift_data['rest'] = $val[10];
                    // シフト労働時間取得
                    $in_shift_time = new DateTimeImmutable($shift_data['in_time']);
                    $out_shift_time = new DateTimeImmutable($shift_data['out_time']);
                    if ($in_shift_time >= $out_shift_time) {
                        continue;
                    } else {
                        $shift_diff_hour = $in_shift_time->diff($out_shift_time)->format('%H');
                        $shift_diff_min = $in_shift_time->diff($out_shift_time)->format('%i');
                        $shift_diff = $shift_diff_hour*60+$shift_diff_min;
                        // $shift_data['hour'] = (strtotime($out_shift_time) - strtotime($in_shift_time)) / 60;
                        $shift_data['hour'] = $shift_diff - $shift_data['rest'];
                    }
                }
                if ($shift_data['status'] == 1) {
                    $shift_data['in_time'] = null;
                    $shift_data['out_time'] = null;
                    $shift_data['rest'] = 0;
                    $shift_data['hour'] = 0;
                    $shift_data['paid_hour'] = null;
                }
                if ($val[10] == 2) {
                    $shift_data['in_time'] = null;
                    $shift_data['out_time'] = null;
                    $shift_data['rest'] = 0;
                    $shift_data['hour'] = 0;
                    $shift_data['paid_hour'] = null;
                }

                if ($shift_data) {
                    $this->load->model('model_shift');
                    $get_shift_data = $this->model_shift->check_day_userid($shift_data['dk_date'], $shift_data['user_id']);
                    if ($get_shift_data) {
                        $shift_data['id'] = $get_shift_data->id;
                        if ($this->model_shift->update_shift($shift_data)) {
                            $message = 'ok';
                        } else {
                            $message = '登録エラー';
                        }
                    } else {
                        if ($this->model_shift->insert_shift($shift_data)) {
                            $message = 'ok';
                        } else {
                            $message = '登録エラー';
                        }
                    }
                }

                if ($shift_data['dk_date'] <= $now_date) {
                    // 分析
                    $this->load->library('process_status_lib'); // 分析処理用 lib 読込
                    $status_data['flag'] = 'shift';
                    $status_data['dk_datetime'] = $shift_data['dk_date'];
                    $status_data['user_id'] = $shift_data['user_id'];
                    $status_data['shift_status'] = $shift_data['status'];
                    $status_data['shift_in_time'] = $shift_data['in_time'];
                    $status_data['shift_out_time'] = $shift_data['out_time'];
                    if ($this->process_status_lib->status($status_data)) {
                        $message = 'ok';
                    } else {
                        $message = '登録エラー';
                    }
                }
            }
        } else {
            $message = '登録エラー';
        }
        if ($message === 'ok') {
            $message = $user_name.' : 登録完了';
        }
        $this->output
        ->set_content_type('application/text')
        ->set_output($message);
    }

    public function userData()
    {
        $user = []; // 出力データ初期化
        $user_id = $this->input->post('user_id');
        $this->load->model('model_user'); // パーソナルデータ取得
        $user_data = $this->model_user->get_now_state_userid($user_id); // 従業員IDで検索し従業員データを取得
        if ($user_data) {
            $this->load->model('model_group_history'); // グループデータ取得
            $group_history_data = $this->model_group_history->get_last_userid($user_id);
            $this->load->model('model_group');
            $group_name = array_fill(1, 3, '');
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
            $user = [ // 出力データ
                'user_name'=>$user_data->name_sei.' '.$user_data->name_mei,
                'group1_name'=>$group[1],
                'group2_name'=>$group[2],
                'group3_name'=>$group[3]
            ];
        }
        $this->output // 出力 json
        ->set_content_type('application/json')
        ->set_output(json_encode($user));
    }


    public function saveData()
    {
        // config data取得
        $config_data = $this->data['configs'];

        $shiftData['user_id'] = (int)$this->input->post('user_id'); // 従業員ID
        $shiftData['dk_date'] = $this->input->post('dk_date');
        $shift_in_time = $this->input->post('in_time');
        $shift_out_time = $this->input->post('out_time');
        if ($shift_in_time !== '') {
            $shiftData['in_time'] = $shift_in_time.':00';
        } else {
            $shiftData['in_time'] = NULL;
        }
        if ($shift_out_time !== '') {
            $shiftData['out_time'] = $shift_out_time.':00';
        } else {
            $shiftData['out_time'] = NULL;
        }
        if ((int)$config_data['over_day']->value > 0) { // 日またぎ対応
            if ($shift_in_time !== '') {
                $shift_in_hour = substr($shift_in_time, 0, 2);
                $shift_in_minute = substr($shift_in_time, 3, 2);
                if ((int)$shift_in_hour <= (int)$config_data['over_day']->value) {
                    $shift_in_hour = (int)$shift_in_hour + 24;
                    $shiftData['in_time'] = $shift_in_hour.':'.$shift_in_minute.':00';
                }
            }
            if ($shift_out_time !== '') {
                $shift_out_hour = substr($shift_out_time, 0, 2);
                $shift_out_minute = substr($shift_out_time, 3, 2);
                if ((int)$shift_out_hour <= (int)$config_data['over_day']->value) {
                    $shift_out_hour = (int)$shift_out_hour + 24;
                    $shiftData['out_time'] = $shift_out_hour.':'.$shift_out_minute.':00';
                }
            }
        }
        $shiftData['rest'] = $this->input->post('rest');
        $shiftData['status'] = $this->input->post('status');
        $shiftData['hour'] = $this->input->post('hour');
        if ($shiftData['status'] == 1 || $shiftData['status'] == 2) {
            $shiftData['rest'] = 0;
            $shiftData['hour'] = 0;
        }
        // shift data 保存
        $select = 'id';
        $where = ['dk_date'=>$shiftData['dk_date'], 'user_id'=>$shiftData['user_id']];
        $this->load->model('Model_shift_data');
        if ($this->Model_shift_data->find_row($select, $where)) {
            $shift_db_id = $this->Model_shift_data->find_row($select, $where)->id;
            if ($shiftData['status'] === 'del') {
                $this->Model_shift_data->delete($shift_db_id);
            } else {
                $this->Model_shift_data->update($shift_db_id, $shiftData);
            }
        } else {
            $this->Model_shift_data->user_id = $shiftData['user_id'];
            $this->Model_shift_data->dk_date = $shiftData['dk_date'];
            $this->Model_shift_data->in_time = $shiftData['in_time'];
            $this->Model_shift_data->out_time = $shiftData['out_time'];
            $this->Model_shift_data->hour = $shiftData['hour'];
            $this->Model_shift_data->rest = $shiftData['rest'];
            $this->Model_shift_data->status = $shiftData['status'];
            $this->Model_shift_data->insert();
        }
        $message = 'ok';

        $status_data['user_id'] = $shiftData['user_id'];
        $status_data['dk_datetime'] = $shiftData['dk_date'];
        $now = new DateTime();
        $now_date = $now->format('Y-m-d');
        if ($shiftData['dk_date'] <= $now_date) {
            // 分析
            $status_data['flag'] = 'shift'; // フラグ
            $status_data['shift_status'] = $shiftData['status'];
            $status_data['shift_in_time'] = $shiftData['in_time'];
            $status_data['shift_out_time'] = $shiftData['out_time'];
            $this->load->library('process_status_lib'); // 分析処理用 lib 読込
            if ($this->process_status_lib->status($status_data)) {
                $message = 'ok';
            } else {
                $message = 'err';
            }
        }

        $callback = [
            'message'=>$message,
            'user_id'=>$status_data['user_id'],
            'today'=>$status_data['dk_datetime']
        ];

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function saveRegister()
    {
        $data['user_id'] = (int)$this->input->post('user_id');
        $data['dk_date'] = $this->input->post('dk_date');
        $data['shift_status'] = (int)$this->input->post('shift_status');

        if ($data['shift_status'] === 0) {
            $data['in_time'] = $this->input->post('in_time');
            $data['out_time'] = $this->input->post('out_time');

            // 従業員のグループIDを取得
            $group = $this->db->query("SELECT group1_id, group2_id, group3_id FROM group_history WHERE user_id = {$data['user_id']} AND to_date <= NOW() ORDER BY to_date DESC LIMIT 1")->row();
            $group_id[1] = isset($group->group1_id) ? $group->group1_id : null;
            $group_id[2] = isset($group->group2_id) ? $group->group2_id : null;
            $group_id[3] = isset($group->group3_id) ? $group->group3_id : null;

            // 休憩ルールを取得
            $rest_rules = $this->db->query('SELECT `user_id`, group_id, group_no, all_flag, rest_time, rest_type, limit_work_hour, rest_in_time, rest_out_time FROM config_rules JOIN rest_rules ON config_rules.id = rest_rules.config_rules_id WHERE rest_rule_flag = 1')->result();
            $rest_rule = [];
            if ($rest_rules) {
                $spec = -1; // 優先順位　数値が大きいほど優先度高　all=0, group1=1, gruop2=2, group3=3, user_id=4
                $spec_tmp = -1;
                foreach($rest_rules as $value) {
                    if ($value->user_id == $data['user_id']) {
                        $spec_tmp = 4;
                    }
                    if (isset($group_id[$value->group_id])) {
                        if ($group_id[$value->group_id] == $value->group_no) {
                            $spec_tmp = $value->group_id;
                        }
                    }
                    if ($value->all_flag == 1) {
                        $spec_tmp = 0;
                    }
                    if ($spec_tmp > $spec) { // 優先度高のルールをストック
                        $rest_rule = [
                            'type'=> $value->rest_type, // 休憩ルール 1=limit時間以上で適応 2=時刻内であれば適応
                            'limit'=> $value->limit_work_hour, // ルール1　リミット時間
                            'in'=> $value->rest_in_time, // ルール２ 休憩入 時刻
                            'out'=> $value->rest_out_time, // ルール2 休憩出 時刻
                            'time'=> $value->rest_time // 適応　休憩時刻 分
                        ];
                        $spec = $spec_tmp;
                    }
                }
            }

            $data['hour'] = (strtotime($data['dk_date'].' '.$data['out_time']) - strtotime($data['dk_date'].' '.$data['in_time']))/60; // シフト労働時間を取得

            // 適応するルールがあれば
            if ($rest_rule) {
                $data['rest'] = 0;
                if ($rest_rule['type'] == 1) { // ルール1　
                    if ($data['hour'] >= $rest_rule['limit']) {
                        $data['rest'] = $rest_rule['time'];
                    }
                }
                if ($rest_rule['type'] == 2) { // ルール2　
                    if (strtotime($data['dk_date'].' '.$data['in_time']) <= strtotime($data['dk_date'].' '.$rest_rule['in']) && strtotime($data['dk_date'].' '.$data['out_time']) >= strtotime($data['dk_date'].' '.$rest_rule['out'])) {
                        $data['rest'] = $rest_rule['time'];
                    }
                }
                $data['hour'] = $data['hour'] - $data['rest']; // シフト労働時間 - 休憩時間
            }
        } else {
            $data['in_time'] = NULL;
            $data['out_time'] = NULL;
        }

        // 保存処理
        $id = $this->db->query("SELECT id FROM shift_register_data WHERE `user_id` = {$data['user_id']} AND dk_date = '{$data['dk_date']}'")->row(); // フィールドがあるか検索、あればID取得

        $data['flag'] = 1;
        $now = new DateTime();
        if (isset($id)) { // idがあれば、アップデート
            $this->db->where('id', $id->id);
            $data['up_datetime'] = $now->format('Y-m-d H:i:s');
            $data['updated_at'] = $now->format('Y-m-d H:i:s');
            $message = $this->db->update('shift_register_data', $data) ? 'ok' : 'ng';
        } else { // なければ、新規保存
            $data['up_datetime'] = $now->format('Y-m-d H:i:s');
            $data['updated_at'] = $now->format('Y-m-d H:i:s');
            $data['created_at'] = $now->format('Y-m-d H:i:s');
            $message = $this->db->insert('shift_register_data', $data) ? 'ok' : 'ng';
        }

        $this->output
        ->set_content_type('application/text')
        ->set_output($message);
    }

    public function saveUndataChange()
    {
        $message = '';
        $user_id = (int)$this->input->post('user_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = (int)cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $now = new DateTime();
        $now_year = $now->format('Y');
        $now_month = $now->format('m');
        $first_date = $year.'-'.$month.'-01';
        if ((int)$now_year === (int)$year && (int)$now_month === (int)$month) {
            $first_date = $now->format('Y-m-d');
        }
        $end_date = $year.'-'.$month.'-'.$month_days;
        $this->load->model('model_shift');
        $result = $this->model_shift->gets_register_all_first_to_end_date_user_id($first_date, $end_date, $user_id);
        $register_shift_data = [];
        if ($result) {
            $register_shift_data = array_column($result, 'dk_date');
        }
        $begin = new DateTime($first_date);
        $end = new DateTime($end_date);
        $end = $end->modify( '+1 day' );
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);
        foreach($daterange as $date) {
            if (!in_array($date->format('Y-m-d'), $register_shift_data, true)) {
                $data['dk_date'] = $date->format('Y-m-d');
                $data['user_id'] = $user_id;
                $data['shift_status'] = 1;
                $data['in_time'] = NULL;
                $data['out_time'] = NULL;
                $this->model_shift->insert_register_shift($data);
                $message = 'ok';
            }
        }

        $this->output
        ->set_content_type('application/text')
        ->set_output($message);
    }

    public function saveUndataDel()
    {
        $message = '';
        $user_id = (int)$this->input->post('user_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = (int)cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $now = new DateTime();
        $now_year = $now->format('Y');
        $now_month = $now->format('m');
        $first_date = $year.'-'.$month.'-01';
        if ((int)$now_year === (int)$year && (int)$now_month === (int)$month) {
            $first_date = $now->format('Y-m-d');
        }
        $end_date = $year.'-'.$month.'-'.$month_days;
        $this->load->model('model_shift');
        if ($this->model_shift->del_register_all_first_to_end_date_user_id($first_date, $end_date, $user_id)) {
            $message = 'ok';
        }
        $this->output
        ->set_content_type('application/text')
        ->set_output($message);
    }

    // 申請データ　反映
    public function refRegister()
    {
        // config data取得
        $config_data = $this->data['configs'];

        $shiftData['user_id'] = (int)$this->input->post('user_id'); // 従業員ID
        $shiftData['dk_date'] = $this->input->post('dk_date');
        $shift_in_time = $this->input->post('in_time');
        $shift_out_time = $this->input->post('out_time');
        if ($shift_in_time !== '') {
            $shiftData['in_time'] = $shift_in_time.':00';
        } else {
            $shiftData['in_time'] = NULL;
        }
        if ($shift_out_time !== '') {
            $shiftData['out_time'] = $shift_out_time.':00';
        } else {
            $shiftData['out_time'] = NULL;
        }
        if ((int)$config_data['over_day']->value > 0) { // 日またぎ対応
            if ($shift_in_time !== '') {
                $shift_in_hour = substr($shift_in_time, 0, 2);
                $shift_in_minute = substr($shift_in_time, 3, 2);
                if ((int)$shift_in_hour <= (int)$config_data['over_day']->value) {
                    $shift_in_hour = (int)$shift_in_hour + 24;
                    $shiftData['in_time'] = $shift_in_hour.':'.$shift_in_minute.':00';
                }
            }
            if ($shift_out_time !== '') {
                $shift_out_hour = substr($shift_out_time, 0, 2);
                $shift_out_minute = substr($shift_out_time, 3, 2);
                if ((int)$shift_out_hour <= (int)$config_data['over_day']->value) {
                    $shift_out_hour = (int)$shift_out_hour + 24;
                    $shiftData['out_time'] = $shift_out_hour.':'.$shift_out_minute.':00';
                }
            }
        }
        $shiftData['rest'] = $this->input->post('rest');
        $shiftData['status'] = $this->input->post('status');
        $shiftData['hour'] = $this->input->post('hour');
        // shift data 保存
        $select = 'id';
        $where = ['dk_date'=>$shiftData['dk_date'], 'user_id'=>$shiftData['user_id']];
        $this->load->model('Model_shift_data');
        if ($this->Model_shift_data->find_row($select, $where)) {
            $shift_db_id = $this->Model_shift_data->find_row($select, $where)->id;
            $this->Model_shift_data->update($shift_db_id, $shiftData);
        } else {
            $this->Model_shift_data->user_id = $shiftData['user_id'];
            $this->Model_shift_data->dk_date = $shiftData['dk_date'];
            $this->Model_shift_data->in_time = $shiftData['in_time'];
            $this->Model_shift_data->out_time = $shiftData['out_time'];
            $this->Model_shift_data->hour = $shiftData['hour'];
            $this->Model_shift_data->rest = $shiftData['rest'];
            $this->Model_shift_data->status = $shiftData['status'];
            $this->Model_shift_data->insert();
        }
        // shift 申請データ flag -> 2
        $id = (int)$this->input->post('id');
        $data = ['flag'=>2];
        $this->load->model('model_shift_register_data');
        $this->model_shift_register_data->update($id, $data);

        $message = 'ok';

        $now = new DateTime();
        $now_date = $now->format('Y-m-d');
        if ($shiftData['dk_date'] <= $now_date) {
            // 分析
            $status_data['flag'] = 'shift'; // フラグ
            $status_data['user_id'] = $shiftData['user_id'];
            $status_data['dk_datetime'] = $shiftData['dk_date'];
            $status_data['shift_status'] = $shiftData['status'];
            $status_data['shift_in_time'] = $shiftData['in_time'];
            $status_data['shift_out_time'] = $shiftData['out_time'];
            $this->load->library('process_status_lib'); // 分析処理用 lib 読込
            if ($this->process_status_lib->status($status_data)) {
                $message = 'ok';
            } else {
                $message = 'err';
            }
        }
        $this->output
        ->set_content_type('application/text')
        ->set_output($message);
    }

    public function refRegisterAll()
    {
        $user_id = (int)$this->input->post('user_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $register_shift_data = [];
        $this->load->model('model_shift_register_data');
        $select = 'id, user_id, dk_date, in_time, out_time, shift_status, hour, rest';
        $orderby = '';
        $where = [
            'user_id'=>(int)$user_id,
            'date_format(dk_date, "%Y%m") = '=>$year.$month,
            'flag'=>1
        ];
        $register_shift_data = $this->model_shift_register_data->find($select, $where, $orderby);

        $this->load->model('Model_shift_data');

        $now = new DateTime();
        $now_date = $now->format('Y-m-d');
        $return_data = [];
        foreach ($register_shift_data as $value) {
            $id = (int)$value->id;
            $user_id = (int)$value->user_id;
            $dk_date = $value->dk_date;
            $shift_status = (int)$value->shift_status;
            $in_time = $out_time = NULL;
            $hour = $rest = 0;
            if ($shift_status === 0) {
                $in_time = $value->in_time;
                $out_time = $value->out_time;
                $rest = $value->rest;
                $hour = $value->hour > 0 ? $value->hour : ((strtotime($out_time) - strtotime($in_time)) / 60) - $rest;
            }

            // 申請DB 更新 flag -> 2
            $data = ['flag'=>2];
            $this->model_shift_register_data->update($id, $data);
            $message = 'ok';
            // シフトDB 保存
            $select = 'id';
            $where = [
                'dk_date'=>$dk_date,
                'user_id'=>$user_id
            ];
            $shiftData = [];
            $shiftData = $this->Model_shift_data->find_row($select, $where);
            if ($shiftData) {
                $shift_db_id = (int)$shiftData->id;
                $data = [
                    'in_time'=>$in_time,
                    'out_time'=>$out_time,
                    'status'=>$shift_status,
                    'hour'=>$hour,
                    'rest'=>$rest
                ];
                $this->Model_shift_data->update($shift_db_id, $data);
            } else {
                $this->Model_shift_data->user_id = $user_id;
                $this->Model_shift_data->dk_date = $dk_date;
                $this->Model_shift_data->in_time = $in_time;
                $this->Model_shift_data->out_time = $out_time;
                $this->Model_shift_data->status = $shift_status;
                $this->Model_shift_data->hour = $hour;
                $this->Model_shift_data->rest = $rest;
                $this->Model_shift_data->insert();
            }

            if ($dk_date <= $now_date) {
                // 分析
                $this->load->library('process_status_lib'); // 分析処理用 lib 読込
                $status_data['flag'] = 'shift';
                $status_data['dk_datetime'] = $dk_date;
                $status_data['user_id'] = $user_id;
                $status_data['shift_status'] = $shift_status;
                $status_data['shift_in_time'] = $in_time;
                $status_data['shift_out_time'] = $out_time;
                if ($this->process_status_lib->status($status_data)) {
                    $message = 'ok';
                } else {
                    $message = 'err_lib';
                }
            }
            $return_data[] = [
            'dk_date' => $dk_date,
            'status' => $shift_status,
            'message' => $message
            ];
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($return_data));
    }

    public function allData()
    {
        $users = $this->input->post('filterUserData');
        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $this->load->database();
        if ($users) {
            $in_values = implode(',', $users);
            $query = $this->db->query('SELECT user_id, group1_id, group2_id, group3_id, to_date FROM group_history WHERE user_id IN ('.$in_values.')')->result();
            $users_data = [];
            if ($query) {
                foreach ((array) $query as $key => $value) {
                    $sort[$key] = $value->to_date;
                }
                array_multisort($sort, SORT_DESC, $query);
                $i = 0;
                $key_array = [];
                foreach ($query as $val) {
                    if (!in_array($val->user_id, $key_array)) {
                        $key_array[$i] = $val->user_id;
                        $users_data[$val->user_id] = $val;
                    }
                    $i++;
                }
            }
        } else {
            $users = [];
        }

        // ルールの取得
        $this->load->model('model_config_values');
        $where = ['config_name'=>'auto_shift_flag'];
        $auto_shift_flag = (int)$this->model_config_values->find_row('value', $where)->value;
        if ($auto_shift_flag === 1) {
            $rules_data = [];
            $select = 'id, all_flag, user_id, group_id, group_no, basic_rest_weekday, basic_in_time, basic_out_time, rest_rule_flag';
            $where = [];
            $orderby = 'order';
            $this->load->model('model_config_rules');
            $rules_data = $this->model_config_rules->find($select, $where, $orderby);
            $rest_data = $this->db->query('SELECT * FROM rest_rules')->result();
        }
        // シフトデータの取得
        if ($users) {
            $shfit_data = $this->db->query('SELECT dk_date, user_id, hour FROM shift_data WHERE date_format(dk_date, "%Y%m") = '.$year.$month.' AND user_id IN ('.$in_values.')')->result();
            foreach ($shfit_data as $value) {
                $shift[$value->dk_date][(int)$value->user_id] = (int)$value->hour;
            }
        }

        $this->load->helper('holiday_date');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $first_date = new DateTime($year.'-'.$month.'-01');
        $end_date = new DateTime($year.'-'.$month.'-'.$month_days.' 00:00:01');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($first_date, $interval, $end_date);
        $data = [];
        foreach ($period as $datetime) {
            $dk_date = $datetime->format('Y-m-d');
            foreach ($users as $user_id) {
                $hour = 0;
                // 自動シフトの場合
                if ($auto_shift_flag === 1) {
                    $datetime = new DateTime($dk_date);
                    $holiday_datetime = new HolidayDateTime($dk_date);
                    $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');

                    if (array_search(1, array_column($rules_data, 'all_flag')) !== FALSE) {
                        $rulesIndex = array_search(1, array_column($rules_data, 'all_flag'));
                    }
                    if (array_search(1, array_column($rules_data, 'group_id')) !== FALSE) {
                        if (array_search($users_data[(int)$user_id]->group1_id, array_column($rules_data, 'group_no')) !== FALSE) {
                            $rulesIndex = array_search($users_data[(int)$user_id]->group1_id, array_column($rules_data, 'group_no'));
                        }
                    }
                    if (array_search(2, array_column($rules_data, 'group_id')) !== FALSE) {
                        if (array_search($users_data[(int)$user_id]->group2_id, array_column($rules_data, 'group_no')) !== FALSE) {
                            $rulesIndex = array_search($users_data[(int)$user_id]->group2_id, array_column($rules_data, 'group_no'));
                        }
                    }
                    if (array_search(3, array_column($rules_data, 'group_id')) !== FALSE) {
                        if (array_search($users_data[(int)$user_id]->group3_id, array_column($rules_data, 'group_no')) !== FALSE) {
                            $rulesIndex = array_search($users_data[(int)$user_id]->group3_id, array_column($rules_data, 'group_no'));
                        }
                    }
                    if (array_search($user_id, array_column($rules_data, 'user_id')) !== FALSE) {
                        $rulesIndex = array_search($user_id, array_column($rules_data, 'user_id'));
                    }
                    $rules = @$rules_data[$rulesIndex] ?: NULL;
                    if ($rules) {
                        $basic_rest_week = str_split($rules->basic_rest_weekday);
                        if ($basic_rest_week[$w] == 0 && $rules->basic_in_time && $rules->basic_out_time) {
                            $hour = (strtotime($rules->basic_out_time) - strtotime($rules->basic_in_time)) / 60;
                            if ((int)$rules->rest_rule_flag === 1) {
                                $rest = 0;
                                foreach ($rest_data as $value) {
                                    if ($value->config_rules_id === $rules->id) {
                                        if ((int)$value->rest_type === 1) {
                                            if ((int)$hour >= (int)$value->limit_work_hour) {
                                                $rest = (int)$value->rest_time;
                                            }
                                        }
                                        if ((int)$value->rest_type === 2) {
                                            if (strtotime($rules->basic_in_time) <= strtotime($value->rest_in_time) && strtotime($rules->basic_out_time) >= strtotime($value->rest_out_time)) {
                                                $rest = $value->rest_time;
                                            }
                                        }
                                    }
                                }
                                $hour -= $rest;
                            }
                        }
                    }
                }
                // シフトの挿入
                if (isset($shift[$dk_date][(int)$user_id])) {
                    $hour = $shift[$dk_date][(int)$user_id];
                }
                $data[$dk_date][(int)$user_id] = [
                    'dk_date' => $dk_date,
                    'year' => $datetime->format('Y'),
                    'month' => $datetime->format('Y'),
                    'day' => $datetime->format('d'),
                    'hour' => $hour
                ];
            }
        }

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}
