<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_status_lib
{
    protected $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function status($status_data) {
        $user_id = $in_time = $out_time = $in_work_time = $out_work_time = $area_id = $time_data_id = $memo = $notice_memo = $revision_user = $revision_datetime = NULL;
        $fact_hour = $rest = $revision = 0;
        if (isset($status_data['dk_datetime'])) {
            $dk_datetime = $status_data['dk_datetime'];
        } else {
            return;
        }
        if (isset($status_data['user_id'])) {
            $user_id = $status_data['user_id'];
        }
        if (isset($status_data['in_time'])) {
            $in_time = $status_data['in_time'];
        }
        if (isset($status_data['out_time'])) {
            $out_time = $status_data['out_time'];
        }
        if (isset($status_data['in_work_time'])) {
            $in_work_time = $status_data['in_work_time'];
        }
        if (isset($status_data['out_work_time'])) {
            $out_work_time = $status_data['out_work_time'];
        }
        if (isset($status_data['area_id'])) {
            $area_id = $status_data['area_id'];
        }
        $flag = '';
        $user_data = [];
        if (isset($status_data['flag'])) {
            $flag = $status_data['flag'];
        }
        if (isset($status_data['rest'])) {
            $rest = $status_data['rest'];
        }
        if (isset($status_data['memo'])) {
            $memo = $status_data['memo'];
        }
        if (isset($status_data['shift_status'])) {
            $shift_data[$user_id] = [
                'in_time' => $status_data['shift_in_time'],
                'out_time' => $status_data['shift_out_time'],
                'status' => $status_data['shift_status']
            ];
        }
        if (isset($status_data['revision'])) {
            $revision = $status_data['revision'];
        }
        if (isset($status_data['notice_memo'])) {
            $notice_memo = $status_data['notice_memo'];
        }
        if (isset($status_data['revision_user'])) {
            $revision_user = $status_data['revision_user'];
        }
        if (isset($status_data['revision_datetime'])) {
            $revision_datetime = $status_data['revision_datetime'];
        }
        if (isset($status_data['revision_in'])) {
            $revision_in = $status_data['revision_in'];
        }
        if (isset($status_data['revision_out'])) {
            $revision_out = $status_data['revision_out'];
        }

        // config data取得
        $this->CI->load->model('model_config_values');
        $where = [];
        $result = $this->CI->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        // 日付処理
        $now = new DateTimeImmutable(); // 時刻データ取得
        $date = new DateTimeImmutable($dk_datetime);
        $dk_date = $date->format('Y-m-d');
        if ($date->format('Ymd') == $now->format('Ymd')) {
            $date_diff = 0;
        }
        if ($date->format('Ymd') < $now->format('Ymd')) {
            $date_diff = 1;
        }
        if ($date->format('Ymd') > $now->format('Ymd')) {
            $date_diff = 2;
        }

        $this->CI->load->helper('holiday_date');
        $holiday_datetime = new HolidayDateTime($dk_date);
        $holiday_datetime->holiday() ? $w = 7 : $w = $date->format('w');
        // group_id data
        $this->CI->load->model('model_group_history');
        $result = $this->CI->model_group_history->find_all();
        foreach ($result as $row) {
            if (new DateTimeImmutable($row->to_date) <= new DateTimeImmutable($dk_date)) {
                $group1_id[$row->user_id] = $row->group1_id;
                $group2_id[$row->user_id] = $row->group2_id;
                $group3_id[$row->user_id] = $row->group3_id;
            }
        }

        $this->CI->load->model('model_time');
        if ($user_id !== NULL) { // $user_id がある場合は、出退勤、直行出退勤、修正の時のみ
            $user_data[] = (object) [
                'user_id' => $user_id
            ];
            if ($flag == 'in' || $flag == 'out' || $flag == 'nonstop_in' || $flag == 'nonstop_out') {
                $times_data = $this->CI->model_time->gets_day_user_id_status($dk_date, $user_id);
                foreach ($times_data as $time) {
                    $time_data[$time->user_id] = [
                        'id' => $time->id,
                        'in_time' => $time->in_time,
                        'in_work_time' => $time->in_work_time,
                        'rest' => $time->rest,
                        'memo' => $time->memo,
                        'revision_user' => $time->revision_user,
                        'revision_datetime' => $time->revision_datetime,
                        'notice_memo' => $time->notice_memo
                    ];
                }
            }
        }
        if ($flag == 'auto') {
            // 対象の日付のuser dataを取得
            $user_data = [];
            $this->CI->load->model('model_user');
            $user_data = $this->CI->model_user->find_exist_all($dk_date);
            // 対象の日付のtime dataを取得
            $times_data = [];
            $times_data = $this->CI->model_time->gets_day_all($dk_date);
            foreach ($times_data as $time) {
                $time_data[$time->user_id] = [
                    'id' => $time->id,
                    'in_time' => $time->in_time,
                    'in_work_time' => $time->in_work_time,
                    'out_time' => $time->out_time,
                    'out_work_time' => $time->out_work_time,
                    'rest' => $time->rest,
                    'memo' => $time->memo,
                    'area_id' => $time->area_id,
                    'revision_user' => $time->revision_user,
                    'revision_datetime' => $time->revision_datetime,
                    'notice_memo' => $time->notice_memo,
                    'revision' => $time->revision,
                    'revision_in' => $time->revision_in,
                    'revision_out' => $time->revision_out
                ];
            }
        }
        if ($flag == 'shift') {
            $times_data = $this->CI->model_time->gets_day_in_userid($dk_date, $user_id);
            foreach ($times_data as $time) {
                $time_data[$time->user_id] = [
                    'id' => $time->id,
                    'in_time' => $time->in_time,
                    'in_work_time' => $time->in_work_time,
                    'out_time' => $time->out_time,
                    'out_work_time' => $time->out_work_time,
                    'rest' => $time->rest,
                    'memo' => $time->memo,
                    'area_id' => $time->area_id,
                    'revision_user' => $time->revision_user,
                    'revision_datetime' => $time->revision_datetime,
                    'notice_memo' => $time->notice_memo,
                    'revision' => $time->revision,
                    'revision_in' => $time->revision_in,
                    'revision_out' => $time->revision_out
                ];
            }
        }
        // edit
        if ($flag == 'edit') {
            $times_data = $this->CI->model_time->gets_day_user_id_status($dk_date, $user_id);
            foreach ($times_data as $time) {
                $time_data[$time->user_id] = [
                    'id' => $time->id,
                    'in_time' => $time->in_time,
                    'out_time' => $time->out_time,
                    'notice_memo' => $time->notice_memo
                ];
            }
        }
        if ($flag != 'shift') {
            // 対象の日付のshift dataを取得
            $this->CI->load->model('model_shift');
            $shiftData = $this->CI->model_shift->find_day_all($dk_date);
            if ($shiftData) {
                foreach ($shiftData as $shift) {
                    $shift_data[$shift->user_id] = [
                        'in_time' => $shift->in_time,
                        'out_time' => $shift->out_time,
                        'status' => $shift->status
                    ];
                }
            }
        }
        if ($flag == 'notice') { // 申請の承認時
            $times_data = $this->CI->model_time->gets_day_user_id_status($dk_date, $user_id);
            foreach ($times_data as $time) {
                $time_data[$time->user_id] = [
                    'id' => $time->id,
                    'in_time' => $time->in_time,
                    'out_time' => $time->out_time,
                    'rest' => $time->rest,
                    'memo' => $time->memo,
                    'area_id' => $time->area_id
                ];
            }
        }

        foreach ($user_data as $user) {
            if ($flag === 'in' || $flag === 'nonstop_in' || $flag === 'out' || $flag === 'nonstop_out' || $flag === 'edit' || $flag === 'shift' || $flag === 'auto' || $flag === 'notice') {
                // ルールデータ取得
                $in_marume_flag = $out_marume_flag = $in_marume_hour = $out_marume_hour = 0; // まるめ
                $in_marume_time = $out_marume_time = '';
                $shift_status = $shift_in_time = $shift_out_time = $config_rules_id = ''; // シフト
                $over_limit_hour = ''; // 稼働時間
                $this->CI->load->library('process_rules_lib'); // rules lib 読込
                $rules = $this->CI->process_rules_lib->get_rule($user->user_id);
                if ($rules) {
                    $in_marume_flag = (int)$rules->in_marume_flag;
                    $out_marume_flag = (int)$rules->out_marume_flag;
                    // まるめ
                    if ($in_marume_flag === 1 || $in_marume_flag === 3 || $in_marume_flag === 5) {
                        $in_marume_hour = (int)$rules->in_marume_hour; // まるめ時間
                    }
                    if ($out_marume_flag === 1 || $out_marume_flag === 3 || $out_marume_flag === 5) {
                        $out_marume_hour = (int)$rules->out_marume_hour;
                    }
                    // 時刻合わせ
                    if ($in_marume_flag === 2 || $in_marume_flag === 3) {
                        $in_marume_time = $rules->in_marume_time;
                    }
                    if ($out_marume_flag === 2 || $out_marume_flag === 3) {
                        $out_marume_time = $rules->out_marume_time;
                    }
                    // 自動シフト時
                    if ((int)$config_data['auto_shift_flag'] === 1) {
                        if ($rules->basic_out_time && $rules->basic_rest_weekday) {
                            $basic_rest_week = str_split($rules->basic_rest_weekday);
                            if ((int)$basic_rest_week[$w] === 0) {
                                $shift_in_time = $rules->basic_in_time;
                                $shift_out_time = $rules->basic_out_time;
                                $shift_status = 0;
                            }
                            if ((int)$basic_rest_week[$w] === 1) {
                                $shift_status = 1;
                            }
                        }
                    }
                    // シフトデータ取得
                    $shift_data = [];
                    $this->CI->load->model('model_shift');
                    $shift_data = $this->CI->model_shift->find_day_userid($dk_date, $user->user_id);
                    if ($shift_data) {
                        $shift_status = (int)$shift_data->status;
                        if ($shift_status === 0) {
                            $shift_in_time = $shift_data->in_time;
                            $shift_out_time = $shift_data->out_time;
                        }
                    }
                    // シフト合わせ
                    if ($in_marume_flag === 4 || $in_marume_flag === 5) {
                        if ((int)$config_data['auto_shift_flag'] === 1) {
                            if ($rules->basic_in_time && $rules->basic_rest_weekday) {
                                $basic_rest_week = str_split($rules->basic_rest_weekday);
                                if ((int)$basic_rest_week[$w] === 0) {
                                    $shift_in_time = $rules->basic_in_time;
                                }
                            }
                        }
                        if ($shift_data) {
                            if ($shift_status === 0) {
                                $shift_in_time = $shift_data->in_time;
                            }
                        }
                    }
                    if ($out_marume_flag === 4 || $out_marume_flag === 5 || $out_marume_flag === 7) {
                        if ((int)$config_data['auto_shift_flag'] === 1) {
                            if ($rules->basic_out_time && $rules->basic_rest_weekday) {
                                $basic_rest_week = str_split($rules->basic_rest_weekday);
                                if ((int)$basic_rest_week[$w] === 0) {
                                    $shift_out_time = $rules->basic_out_time;
                                }
                            }
                        }
                        if ($shift_data) {
                            if ($shift_status === 0) {
                                $shift_out_time = $shift_data->out_time;
                            }
                        }
                    }
                    if ($out_marume_flag === 6) {
                        $basic_out_time = '';
                        if ($rules->basic_out_time) {
                            $basic_out_time = $rules->basic_out_time;
                        }
                    }
                    if ((int)$rules->rest_rule_flag === 1) {
                        $config_rules_id = (int)$rules->id;
                    }
                    // 稼働時間
                    if ((int)$rules->over_limit_hour > 0) {
                        $over_limit_hour = (int)$rules->over_limit_hour;
                    }
                }
            }

            // 実出勤処理
            if ($flag === 'in') {
                $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$in_time);
                $in_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                $in_work_time = $in_time;
                $in_marume = '';
                if ($in_marume_flag === 2 || $in_marume_flag === 3) {
                    if (strtotime($dk_date.' '.$in_marume_time) >= strtotime($in_time_calc)) {
                        $in_work_time = $in_marume_time;
                        $in_marume = 'on';
                    }
                }
                if ($in_marume_flag === 4 || $in_marume_flag === 5) {
                    if ($shift_in_time) {
                        if (strtotime($dk_date.' '.$shift_in_time) >= strtotime($in_time_calc)) {
                            $in_work_time = $shift_in_time;
                            $in_marume = 'on';
                        }
                    }
                }
                if ($in_marume_flag === 1 || $in_marume_flag === 3 || $in_marume_flag === 5) {
                    if ($in_marume === '') {
                        $in_time_array = date_parse_from_format('h:i:s', $in_time);
                        if ($in_marume_hour === 5) { // 5分まるめ
                            if ($in_time_array['minute'] < 5 && $in_time_array['minute'] != 0) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 5, 0));
                            }
                            if ($in_time_array['minute'] > 5 && $in_time_array['minute'] <= 10) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 10, 0));
                            }
                            if ($in_time_array['minute'] > 10 && $in_time_array['minute'] <= 15) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 15, 0));
                            }
                            if ($in_time_array['minute'] > 15 && $in_time_array['minute'] <= 20) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 20, 0));
                            }
                            if ($in_time_array['minute'] > 20 && $in_time_array['minute'] <= 25) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 25, 0));
                            }
                            if ($in_time_array['minute'] > 25 && $in_time_array['minute'] <= 30) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 30, 0));
                            }
                            if ($in_time_array['minute'] > 30 && $in_time_array['minute'] <= 35) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 35, 0));
                            }
                            if ($in_time_array['minute'] > 35 && $in_time_array['minute'] <= 40) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 40, 0));
                            }
                            if ($in_time_array['minute'] > 40 && $in_time_array['minute'] <= 45) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 45, 0));
                            }
                            if ($in_time_array['minute'] > 45 && $in_time_array['minute'] <= 50) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 50, 0));
                            }
                            if ($in_time_array['minute'] > 50 && $in_time_array['minute'] <= 55) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 55, 0));
                            }
                            if ($in_time_array['minute'] > 55) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'] + 1, 0, 0));
                            }
                        }
                        if ($in_marume_hour === 15) { // 15分まるめ
                            if ($in_time_array['minute'] < 15 && $in_time_array['minute'] != 0) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 15, 0));
                            }
                            if ($in_time_array['minute'] > 15 && $in_time_array['minute'] <= 30) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 30, 0));
                            }
                            if ($in_time_array['minute'] > 30 && $in_time_array['minute'] <= 45) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 45, 0));
                            }
                            if ($in_time_array['minute'] > 45) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'] + 1, 0, 0));
                            }
                        }
                        if ($in_marume_hour === 30) { // 30分まるめ
                            if ($in_time_array['minute'] < 30 && $in_time_array['minute'] != 0) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'], 30, 0));
                            }
                            if ($in_time_array['minute'] > 30) {
                                $in_work_time = strftime('%H:%M:%S', mktime($in_time_array['hour'] + 1, 0, 0));
                            }
                        }
                        if ($in_time_array['hour'] >= 24) { //24時以降対策
                            $in_time_array = date_parse_from_format('h:i:s', $in_work_time);
                            $hour = $in_time_array['hour'] + 24;
                            $min = $in_time_array['minute'];
                            $sec = $in_time_array['second'];
                            $in_work_time = $hour.':'.$min.':'.$sec;
                        }
                    }
                }
                if ($out_marume_flag === 6) {
                    if ($basic_out_time) {
                        $out_work_time = $basic_out_time;
                    }
                }
                if ($out_marume_flag === 7) {
                    if ($shift_out_time) {
                        $out_work_time = $shift_out_time;
                    }
                }
            }

            // 実退勤処理
            if ($flag === 'out') {
                $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$out_time);
                $out_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                $out_work_time = $out_time;
                $out_marume = '';
                if ($out_marume_flag === 2 || $out_marume_flag === 3) {
                    if (strtotime($dk_date.' '.$out_marume_time) <= strtotime($dk_date.' '.$out_time)) {
                        $out_work_time = $out_marume_time;
                        $out_marume = 'on';
                    }
                }
                if ($out_marume_flag === 4 || $out_marume_flag === 5) {
                    if ($shift_out_time) {
                        if (strtotime($dk_date.' '.$shift_out_time) <= strtotime($dk_date.' '.$out_time)) {
                            $out_work_time = $shift_out_time;
                            $out_marume = 'on';
                        }
                    }
                }
                if ($out_marume_flag === 1 || $out_marume_flag === 3 || $out_marume_flag === 5) {
                if ($out_marume === '') {
                $out_time_array = date_parse_from_format('h:i:s', $out_time);
                if ($out_marume_hour === 5) { // 5分まるめ
                if ($out_time_array['minute'] < 5 && $out_time_array['minute'] != 0) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 0, 0));
                }
                if ($out_time_array['minute'] >= 5 && $out_time_array['minute'] < 10) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 5, 0));
                }
                if ($out_time_array['minute'] >= 10 && $out_time_array['minute'] < 15) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 10, 0));
                }
                if ($out_time_array['minute'] >= 15 && $out_time_array['minute'] < 20) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 15, 0));
                }
                if ($out_time_array['minute'] >= 20 && $out_time_array['minute'] < 25) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 20, 0));
                }
                if ($out_time_array['minute'] >= 25 && $out_time_array['minute'] < 30) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 25, 0));
                }
                if ($out_time_array['minute'] >= 30 && $out_time_array['minute'] < 35) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 30, 0));
                }
                if ($out_time_array['minute'] >= 35 && $out_time_array['minute'] < 40) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 35, 0));
                }
                if ($out_time_array['minute'] >= 40 && $out_time_array['minute'] < 45) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 40, 0));
                }
                if ($out_time_array['minute'] >= 45 && $out_time_array['minute'] < 50) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 45, 0));
                }
                if ($out_time_array['minute'] >= 50 && $out_time_array['minute'] < 55) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 50, 0));
                }
                if ($out_time_array['minute'] >= 55) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 55, 0));
                }
                }
                if ($out_marume_hour === 15) { // 15分まるめ
                if ($out_time_array['minute'] < 15 && $out_time_array['minute'] != 0) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 0, 0));
                }
                if ($out_time_array['minute'] >= 15 && $out_time_array['minute'] < 30) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 15, 0));
                }
                if ($out_time_array['minute'] >= 30 && $out_time_array['minute'] < 45) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 30, 0));
                }
                if ($out_time_array['minute'] >= 45) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 45, 0));
                }
                }
                if ($out_marume_hour === 30) { // 30分まるめ
                if ($out_time_array['minute'] < 30 && $out_time_array['minute'] != 0) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 0, 0));
                }
                if ($out_time_array['minute'] >= 30) {
                $out_work_time = strftime('%H:%M:%S', mktime($out_time_array['hour'], 30, 0));
                }
                }
                if ($out_time_array['hour'] >= 24) { //24時以降対策
                $out_time_array = date_parse_from_format('h:i:s', $out_work_time);
                $hour = $out_time_array['hour'] + 24;
                $min = $out_time_array['minute'];
                $sec = $out_time_array['second'];
                $out_work_time = $hour.':'.$min.':'.$sec;
                }
                }
                }
            }

            if ($flag === 'nonstop_out') {
                $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$out_time);
                $out_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
            }

            if ($flag === 'in' || $flag === 'nonstop_in') {
                // // 出勤データがある場合 fix 出勤データがなくてもメモとidを取得する
                // if (isset($time_data[$user->user_id]['meno'])) {
                $memo = @$time_data[$user->user_id]['memo'] ?: "";
                $time_data_id = @(int)$time_data[$user->user_id]['id'] ?: 0;
                // }
                // $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$in_time);
                // $in_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));

                // // 実拘束時間
                // if ($in_time && $out_time) {
                //     if (strtotime($out_time_calc) > strtotime($in_time_calc)) {
                //         $fact_hour = (strtotime($out_time_calc) - strtotime($in_time_calc)) / 60;
                //     }
                // }
            }

            if ($flag === 'out' || $flag === 'nonstop_out') {
                $memo = $time_data[$user->user_id]['memo']; // fix
                $time_data_id = (int)$time_data[$user->user_id]['id']; // fix
                // 出勤データがある場合
                if (isset($time_data[$user->user_id])) {
                    $in_time = $time_data[$user->user_id]['in_time'];
                    $in_work_time = $time_data[$user->user_id]['in_work_time'];
                    $rest = $time_data[$user->user_id]['rest'];
                }
                // $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$in_time);
                // $in_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));

                // // 実拘束時間
                // if ($in_time && $out_time) {
                //     if (strtotime($out_time_calc) > strtotime($in_time_calc)) {
                //         $fact_hour = (strtotime($out_time_calc) - strtotime($in_time_calc)) / 60;
                //     }
                // }
            }
            if ($flag === 'auto' || $flag === 'shift') {
                $in_time = $in_work_time = $out_time = $out_work_time = $rest = $time_data_id = $memo = $area_id = $notice_memo = $revision_user = $revision_datetime = $revision = $revision_in = $revision_out = NULL;
                // 出勤データがある場合
                if (isset($time_data[$user->user_id])) {
                    $in_time = $time_data[$user->user_id]['in_time'];
                    $in_work_time = $time_data[$user->user_id]['in_work_time'];
                    $out_time = $time_data[$user->user_id]['out_time'];
                    $out_work_time = $time_data[$user->user_id]['out_work_time'];
                    $rest = $time_data[$user->user_id]['rest'];
                    $time_data_id = (int)$time_data[$user->user_id]['id'];
                    $memo = $time_data[$user->user_id]['memo'];
                    $area_id = (int)$time_data[$user->user_id]['area_id'];
                    $notice_memo = $time_data[$user->user_id]['notice_memo'];
                    $revision_user = $time_data[$user->user_id]['revision_user'];
                    $revision_datetime = $time_data[$user->user_id]['revision_datetime'];
                    $revision = $time_data[$user->user_id]['revision'];
                    $revision_in = $time_data[$user->user_id]['revision_in'];
                    $revision_out = $time_data[$user->user_id]['revision_out'];
                }
            }
            // edit時
            if ($flag === 'edit') {
                if (isset($time_data[$user->user_id])) {
                    $in_time = $time_data[$user->user_id]['in_time'];
                    $out_time = $time_data[$user->user_id]['out_time'];
                    $time_data_id = (int)$time_data[$user->user_id]['id'];
                    $notice_memo = $time_data[$user->user_id]['notice_memo'];
                }
            }
            // 申請承認時
            if ($flag === 'notice') {
                if (isset($time_data[$user->user_id])) {
                    $in_time = $time_data[$user->user_id]['in_time'];
                    $out_time = $time_data[$user->user_id]['out_time'];
                    $rest = $time_data[$user->user_id]['rest'];
                    $memo = $time_data[$user->user_id]['memo'];
                    $time_data_id = (int)$time_data[$user->user_id]['id'];
                }
                if ($memo && $notice_memo !== NULL) {
                    $memo = $memo."\n".$notice_memo;
                }
                if (!$memo && $notice_memo !== NULL) {
                    $memo = $notice_memo;
                }
            }

            // 実拘束時間
            if ($in_time && $out_time) {
                $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$in_time);
                $in_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$out_time);
                $out_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                if (strtotime($out_time_calc) > strtotime($in_time_calc)) {
                    $fact_hour = (strtotime($out_time_calc) - strtotime($in_time_calc)) / 60;
                    $fact_hour -= $rest;
                }
            } else {
                $fact_hour = 0;
            }

            // 計算用　出退勤時刻
            if ($in_work_time) {
                $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$in_work_time);
                $in_work_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
            }
            if ($out_work_time) {
                $tmp = date_parse_from_format('Y-m-d h:i:s', $dk_date.' '.$out_work_time);
                $out_work_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
            }
            // 労働時間取得
            $fact_work_hour = 0;
            if ($in_work_time && $out_work_time) {
                if (strtotime($out_work_time_calc) > strtotime($in_work_time_calc)) {
                    $fact_work_hour = (strtotime($out_work_time_calc) - strtotime($in_work_time_calc)) / 60;
                    $fact_work_hour -= $rest;
                } else {
                    $out_work_time = $in_work_time;
                    $out_work_time_calc = $in_work_time_calc;
                }
            }
            // 遅刻判定
            $late_hour = 0;
            if ($shift_status === 0 && $shift_in_time && $in_work_time) {
                if (strtotime($in_work_time_calc) > strtotime($dk_date.' '.$shift_in_time)) {
                    $late_hour = (strtotime($in_work_time_calc) - strtotime($dk_date.' '.$shift_in_time)) / 60; // 遅刻時間
                }
            }
            // 早退判定
            $left_hour = 0;
            if ($shift_status === 0 && $shift_out_time && $out_work_time) {
            if (strtotime($out_work_time_calc) < strtotime($dk_date.' '.$shift_out_time)) {
            $left_hour = (strtotime($dk_date.' '.$shift_out_time) - strtotime($out_work_time_calc)) / 60; // 早退時間
            }
            }
            // 休憩時間取得処理
            if ($config_rules_id && $fact_work_hour > 0 && $flag !== 'edit' && $flag !== 'auto' && $flag !== 'shift') {
                $rest_rules = [];
                $this->CI->load->model('model_rest_rules'); // model rest_rules
                $select = 'rest_type, limit_work_hour, rest_time, rest_in_time, rest_out_time';
                $where = ['config_rules_id'=>(int)$config_rules_id];
                $rest_rules = $this->CI->model_rest_rules->find($select, $where, '');
                foreach ((array)$rest_rules as $value) {
                    if ((int)$value->rest_type === 1) {
                        if ((int)$fact_work_hour >= (int)$value->limit_work_hour) {
                            $rest = $value->rest_time;
                            $fact_work_hour -= $rest;
                        }
                    }
                    if ((int)$value->rest_type === 2) {
                        $rest_in_time = $value->rest_in_time;
                        $rest_out_time = $value->rest_out_time;
                        if (strtotime($in_work_time_calc) <= strtotime($dk_date.' '.$rest_in_time) && strtotime($out_work_time_calc) >= strtotime($dk_date.' '.$rest_out_time)) {
                            $rest = $value->rest_time;
                            $fact_work_hour -= $rest;
                        }
                    }
                }
            }
            // 残業
            $over_hour = 0;
            if ($over_limit_hour > 0 && $over_limit_hour < $fact_work_hour) {
            $over_hour = $fact_work_hour - $over_limit_hour;
            } elseif ($fact_work_hour > 480) {
            $over_hour = $fact_work_hour - 480;
            }
            // 深夜
            $night_hour = 0;
            if ($fact_work_hour > 0) {
            $out_time_array = date_parse_from_format('h:i:s', $out_work_time_calc);
            if (strtotime($out_work_time_calc) > strtotime($dk_date.' 22:00:00')) {
            $night_hour = (strtotime($out_work_time_calc) - strtotime($dk_date.' 22:00:00')) / 60;
            }
            }
            // シフトとの誤差取得
            $shift_in_hour = $shift_out_hour = NULL;
            if ($shift_in_time && $in_work_time) {
            $shift_in_hour = (strtotime($in_work_time_calc) - strtotime($dk_date.' '.$shift_in_time)) / 60;
            }
            if ($shift_out_time && $out_work_time) {
            $shift_out_hour = (strtotime($dk_date.' '.$shift_out_time) - strtotime($out_work_time_calc)) / 60;
            }
            // 出退勤フラグ
            $in_flag = $out_flag = 0;
            if ($in_work_time) {
            $in_flag = 1;
            }
            if ($out_work_time) {
            $out_flag = 1;
            }
            // status 分析
            $status = '';
            $status_id = 0;
            if ($date_diff === 0 && $shift_status === '' && $in_flag === 1 && $out_flag === 0) {
            $status = '出勤中';
            $status_id = 1;
            }
            if ($date_diff === 0 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour === 0) {
            $status = '勤務';
            $status_id = 2;
            }
            if ($date_diff === 0 && $shift_status === '' && $in_flag === 0 && $out_flag === 0) {
            $status = '未出勤';
            $status_id = 3;
            }
            if ($date_diff === 0 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour === 0) {
            $status = '勤務 残業';
            $status_id = 4;
            }
            if ($date_diff === 0 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour > 0) {
            $status = '勤務 深夜';
            $status_id = 5;
            }
            if ($date_diff === 0 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour > 0) {
            $status = '勤務 深夜残業';
            $status_id = 6;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 0 && $late_hour === 0) {
            $status = '通常出勤中';
            $status_id = 7;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 0 && $late_hour > 0) {
            $status = '遅刻 出勤中';
            $status_id = 8;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag == 0 && $out_flag === 0 && strtotime($dk_date.' '.$shift_in_time) > strtotime($dk_datetime)) {
            $status = '出勤前';
            $status_id = 9;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag == 0 && $out_flag === 0 && strtotime($dk_date.' '.$shift_in_time) < strtotime($dk_datetime)) {
            $status = '未出勤 出勤予定過';
            $status_id = 10;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 0 && strtotime($dk_date.' '.$shift_out_time) < strtotime($dk_datetime)) {
            $status = '勤務中 シフト超過';
            $status_id = 11;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour === 0 && $over_hour === 0 && $night_hour === 0) {
            $status = '遅刻 勤務';
            $status_id = 12;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour > 0 && $over_hour === 0 && $night_hour === 0) {
            $status = '勤務 早退';
            $status_id = 13;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour > 0 && $over_hour === 0 && $night_hour === 0) {
            $status = '遅刻 勤務 早退';
            $status_id = 14;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour === 0 && $over_hour > 0 && $night_hour === 0) {
            $status = '遅刻 勤務 残業';
            $status_id = 15;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour > 0 && $over_hour > 0 && $night_hour === 0) {
            $status = '勤務 早退 残業';
            $status_id = 16;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour === 0 && $over_hour === 0 && $night_hour === 0) {
            $status = '通常勤務';
            $status_id = 17;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour === 0 && $over_hour > 0 && $night_hour === 0) {
            $status = '勤務 残業';
            $status_id = 18;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour === 0 && $over_hour === 0 && $night_hour > 0) {
            $status = '勤務 深夜';
            $status_id = 19;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour === 0 && $over_hour > 0 && $night_hour > 0) {
            $status = '勤務 深夜残業';
            $status_id = 20;
            }
            if ($date_diff === 0 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $fact_work_hour === 0) {
            $status = '勤務時間 0h';
            $status_id = 21;
            }
            if ($date_diff === 0 && $shift_status === 1 && $in_flag === 0 && $out_flag === 0) {
            $status = '公休';
            $status_id = 22;
            }
            if ($date_diff === 0 && $shift_status === 1 && $in_flag === 1 && $out_flag === 0) {
            $status = '休日出勤中';
            $status_id = 23;
            }
            if ($date_diff === 0 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour === 0) {
            $status = '休日勤務';
            $status_id = 24;
            }
            if ($date_diff === 0 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour === 0) {
            $status = '休日勤務 残業';
            $status_id = 25;
            }
            if ($date_diff === 0 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour > 0) {
            $status = '休日勤務 深夜';
            $status_id = 26;
            }
            if ($date_diff === 0 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour > 0) {
            $status = '休日勤務 深夜残業';
            $status_id = 27;
            }
            if ($date_diff === 0 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $fact_work_hour === 0) {
            $status = '休日勤務時間 0h';
            $status_id = 28;
            }
            if ($date_diff === 0 && $shift_status === 2 && $in_flag === 0 && $out_flag === 0) {
            $status = '有給';
            $status_id = 29;
            }
            if ($date_diff === 0 && $shift_status === 2 && $in_flag === 1 && $out_flag === 0) {
            $status = '有給出勤中';
            $status_id = 30;
            }
            if ($date_diff === 0 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour === 0) {
            $status = '有給勤務';
            $status_id = 31;
            }
            if ($date_diff === 0 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour === 0) {
            $status = '有給勤務 残業';
            $status_id = 32;
            }
            if ($date_diff === 0 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour > 0) {
            $status = '有給勤務 深夜';
            $status_id = 33;
            }
            if ($date_diff === 0 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour > 0) {
            $status = '有給勤務 深夜残業';
            $status_id = 34;
            }
            if ($date_diff === 0 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $fact_work_hour === 0) {
            $status = '有給勤務時間 0h';
            $status_id = 35;
            }
            if ($date_diff === 1 && $shift_status === '' && $in_flag === 1 && $out_flag === 0) {
            $status = '片打刻';
            $status_id = 36;
            }
            if ($date_diff === 1 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour === 0) {
            $status = '勤務';
            $status_id = 37;
            }
            if ($date_diff === 1 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour === 0) {
            $status = '勤務 残業';
            $status_id = 38;
            }
            if ($date_diff === 1 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour > 0) {
            $status = '勤務 深夜';
            $status_id = 39;
            }
            if ($date_diff === 1 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour > 0) {
            $status = '勤務 深夜残業';
            $status_id = 40;
            }
            if ($date_diff === 1 && $shift_status === '' && $in_flag === 1 && $out_flag === 1 && $fact_work_hour === 0) {
            $status = '勤務時間 0h';
            $status_id = 41;
            }
            if ($date_diff === 1 && $shift_status === '' && $in_flag === 0 && $out_flag === 0) {
            $status = '未出勤';
            $status_id = 42;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour === 0 && $over_hour === 0 && $night_hour === 0) {
            $status = '通常勤務';
            $status_id = 43;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 0 && $late_hour === 0) {
            $status = '片打刻';
            $status_id = 44;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 0 && $late_hour > 0) {
            $status = '遅刻 片打刻';
            $status_id = 45;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour === 0 && $over_hour === 0 && $night_hour === 0) {
            $status = '勤務 遅刻';
            $status_id = 46;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour > 0 && $over_hour === 0 && $night_hour === 0) {
            $status = '勤務 早退';
            $status_id = 47;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour > 0 && $over_hour > 0 && $night_hour === 0) {
            $status = '勤務 早退 残業';
            $status_id = 48;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour > 0 && $over_hour === 0 && $night_hour > 0) {
            $status = '勤務 早退 深夜';
            $status_id = 49;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour > 0 && $over_hour > 0 && $night_hour > 0) {
            $status = '勤務 早退 深夜残業';
            $status_id = 50;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour > 0 && $over_hour === 0 && $night_hour === 0) {
            $status = '遅刻 勤務 早退';
            $status_id = 51;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour === 0 && $over_hour > 0 && $night_hour === 0) {
            $status = '遅刻 勤務 残業';
            $status_id = 52;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour > 0 && $over_hour > 0 && $night_hour === 0) {
            $status = '遅刻 勤務 早退 残業';
            $status_id = 53;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour > 0 && $over_hour === 0 && $night_hour > 0) {
            $status = '遅刻 勤務 早退 深夜';
            $status_id = 54;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour > 0 && $over_hour > 0 && $night_hour > 0) {
            $status = '遅刻 勤務 早退 残業残業';
            $status_id = 55;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour === 0 && $over_hour > 0 && $night_hour === 0) {
            $status = '勤務 残業';
            $status_id = 56;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour === 0 && $over_hour > 0 && $night_hour > 0) {
            $status = '遅刻 勤務 深夜残業';
            $status_id = 57;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour > 0 && $left_hour === 0 && $over_hour === 0 && $night_hour > 0) {
            $status = '遅刻 勤務 深夜';
            $status_id = 58;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 0 && $out_flag === 0) {
            $status = '欠勤';
            $status_id = 59;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour === 0 && $over_hour === 0 && $night_hour > 0) {
            $status = '勤務 深夜';
            $status_id = 60;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $late_hour === 0 && $left_hour === 0 && $over_hour > 0 && $night_hour > 0) {
            $status = '勤務 深夜残業';
            $status_id = 61;
            }
            if ($date_diff === 1 && $shift_status === 0 && $in_flag === 1 && $out_flag === 1 && $fact_work_hour === 0) {
            $status = '勤務時間 0h';
            $status_id = 62;
            }
            if ($date_diff === 1 && $shift_status === 1 && $in_flag === 1 && $out_flag === 0) {
            $status = '休日出勤片打刻';
            $status_id = 63;
            }
            if ($date_diff === 1 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour === 0) {
            $status = '休日出勤';
            $status_id = 64;
            }
            if ($date_diff === 1 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour === 0) {
            $status = '休日出勤 残業';
            $status_id = 65;
            }
            if ($date_diff === 1 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour > 0) {
            $status = '休日出勤 深夜';
            $status_id = 66;
            }
            if ($date_diff === 1 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour > 0) {
            $status = '休日出勤 深夜残業';
            $status_id = 67;
            }
            if ($date_diff === 1 && $shift_status === 1 && $in_flag === 0 && $out_flag === 0) {
            $status = '公休';
            $status_id = 68;
            }
            if ($date_diff === 1 && $shift_status === 1 && $in_flag === 1 && $out_flag === 1 && $fact_work_hour === 0) {
            $status = '休日出勤 時間 0h';
            $status_id = 69;
            }
            if ($date_diff === 1 && $shift_status === 2 && $in_flag === 1 && $out_flag === 0) {
            $status = '有給出勤片打刻';
            $status_id = 70;
            }
            if ($date_diff === 1 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour === 0) {
            $status = '有給出勤';
            $status_id = 71;
            }
            if ($date_diff === 1 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour === 0) {
            $status = '有給出勤 残業';
            $status_id = 72;
            }
            if ($date_diff === 1 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $over_hour === 0 && $night_hour > 0) {
            $status = '有給出勤 深夜';
            $status_id = 73;
            }
            if ($date_diff === 1 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $over_hour > 0 && $night_hour > 0) {
            $status = '有給出勤 深夜残業';
            $status_id = 74;
            }
            if ($date_diff === 1 && $shift_status === 2 && $in_flag === 0 && $out_flag === 0) {
            $status = '有給取得';
            $status_id = 75;
            }
            if ($date_diff === 1 && $shift_status === 2 && $in_flag === 1 && $out_flag === 1 && $fact_work_hour === 0) {
            $status = '有給出勤 時間 0h';
            $status_id = 76;
            }

            // 保存処理
            $data['dk_date'] = $dk_date;
            $data['user_id'] = $user->user_id;
            $data['in_time'] = $in_time ? $in_time : NULL;
            $data['out_time'] = $out_time ? $out_time : NULL;
            $data['fact_hour'] = $fact_hour ? $fact_hour : 0;
            $data['in_work_time'] = $in_work_time ? $in_work_time : NULL;
            $data['out_work_time'] = $out_work_time ? $out_work_time : NULL;
            $data['rest'] = $rest ? $rest : 0;
            $data['fact_work_hour'] = $fact_work_hour ? $fact_work_hour : 0;
            $data['status'] = $status ? $status : "";
            $data['status_flag'] = $status_id;
            $data['over_hour'] = $over_hour ? $over_hour : 0;
            $data['night_hour'] = $night_hour ? $night_hour : 0;
            $data['left_hour'] = $left_hour ? $left_hour : 0;
            $data['late_hour'] = $late_hour ? $late_hour : 0;
            $data['shift_in_hour'] = $shift_in_hour ? $shift_in_hour : NULL;
            $data['shift_out_hour'] = $shift_out_hour ? $shift_out_hour : NULL;
            $data['area_id'] = $area_id ? $area_id : NULL;
            $data['in_flag'] = $in_flag ? $in_flag : 0;
            $data['out_flag'] = $out_flag ? $out_flag : 0;
            $data['memo'] = $memo ? $memo : "";
            $data['revision'] = $revision ? $revision : 0;
            $data['revision_user'] = $revision_user ? $revision_user : NULL;
            $data['revision_datetime'] = $revision_datetime ? $revision_datetime : NULL;
            $data['notice_memo'] = $notice_memo ? $notice_memo : NULL;
            $data['revision_in'] = @$revision_in ? $revision_in : 0;
            $data['revision_out'] = @$revision_out ? $revision_out : 0;

            // DB保存
            $message = TRUE;
            if ($time_data_id > 0) {
            $data['id'] = $time_data_id;
            if ($this->CI->model_time->update_data($data)) {
            $message = TRUE;
            } else {
            $message = FALSE;
            }
            } else {
            if ($this->CI->model_time->insert_data($data)) {
            $message = TRUE;
            } else {
            $message = FALSE;
            }
            }
        }

        return $message;
    }
}
