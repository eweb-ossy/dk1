<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auto_lib
{
    protected $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function index($dk_date) {
        $this->CI->load->database();
        
        // 設定　自動シフト有無
        $row = $this->CI->db->query("SELECT `value` FROM config_values WHERE config_name = 'auto_shift_flag'")->row();
        $auto_shift_flag = $row->value == 1 ? TRUE : FALSE;
        unset($row);
        
        // 従業員データ取得
        $result = $this->CI->db->query("SELECT user_data.user_id, group_history.group1_id as g1, group_history.group2_id as g2, group_history.group3_id as g3 FROM user_data LEFT JOIN group_history ON user_data.user_id = group_history.user_id WHERE user_data.state = 1 ORDER BY group_history.to_date ASC")->result_array();
        $users_data = array_column($result, NULL, 'user_id');
        unset($result);

        // 勤務データ取得
        $result = $this->CI->db->query("SELECT `user_id`, in_flag, out_flag, fact_hour, over_hour, night_hour, left_hour, late_hour FROM time_data WHERE dk_date = '{$dk_date}'")->result_array();
        $times_data = array_column($result, NULL, 'user_id');
        unset($result);

        // シフトデータ取得
        $result = $this->CI->db->query("SELECT `user_id`, in_time, out_time, hour, `status` FROM shift_data WHERE dk_date = '{$dk_date}'")->result_array();
        $shifts_data = array_column($result, NULL, 'user_id');

        if ($auto_shift_flag) { // 自動シフト設定ありの場合
            // ルールを取得
            $rules_data = $this->CI->db->query("SELECT `user_id`, group_id, group_no, all_flag, basic_in_time, basic_out_time, basic_rest_weekday FROM config_rules WHERE basic_in_time IS NOT NULL AND basic_out_time IS NOT NULL AND basic_rest_weekday IS NOT NULL")->result_array();
        }
        // echo '<pre>';
        // print_r($users_data);
        // echo '</pre>';
        // exit;

        $data = [];
        foreach ($users_data as $user) {
            // シフトデータの有無 $shift_status 0=出勤　1=公休 2=有休
            $shift = false;
            if (isset($shifts_data[$user['user_id']])) {
                $shift = true;
                $shift_status = $shifts_data[$user['user_id']]['status'];
                if ($shift_status === 0) {
                    $shift_in_time = $shifts_data[$user['user_id']]['in_time'];
                    $shift_out_time = $shifts_data[$user['user_id']]['out_time'];
                } else {
                    $shift_in_time = $shift_out_time = NULL;
                }
            } else if ($auto_shift_flag) { // 自動シフト設定ありの場合
                if ($rules_data) {
                    // 適応ルールを抽出
                    $index = array_search($user['user_id'], array_column($rules_data, 'user_id'));
                    if ($index === false) {
                        for ($i = 1; $i <= 3 ; $i++) { 
                            $g_id = array_search($i, array_column($rules_data, 'group_id'));
                            $g_no = array_search($user['g'.$i], array_column($rules_data, 'group_no'));
                            if ($g_id === $g_no) $index = $g_id;
                        }
                    }
                    if ($index === false) {
                        $index = array_search(1, array_column($rules_data, 'all_flag'));
                    }
                    // 適応ルールあり
                    if ($index) {
                        $shift = true;
                        $rule = $rules_data[$index];
                        $basic_rest_weekday = str_split($rule['basic_rest_weekday']);
                        
                        $this->CI->load->helper('holiday_date');
                        $holiday_datetime = new HolidayDateTime($dk_date);
                        $holiday_datetime->holiday() ? $w = 7 : $w = $date->format('w');
                        if ($basic_rest_weekday[$w] == 0) {
                            $shift_status = 0;
                            $shift_in_time = $rule['basic_in_time'];
                            $shift_out_time = $rule['basic_out_time'];
                        } else {
                            $shift_status = 1;
                            $shift_in_time = $shift_out_time = NULL;
                        }
                    }
                }
            }
            // 勤務データの有無
            if (isset($times_data[$user['user_id']])) {
                $in_flag = $times_data[$user['user_id']]['in_flag'];
                $out_flag = $times_data[$user['user_id']]['out_flag'];
                $fact_hour = $times_data[$user['user_id']]['fact_hour'];
                $over_hour = $times_data[$user['user_id']]['over_hour'];
                $night_hour = $times_data[$user['user_id']]['night_hour'];
                $left_hour = $times_data[$user['user_id']]['left_hour'];
                $late_hour = $times_data[$user['user_id']]['late_hour'];
            } else {
                $in_flag = $out_flag = $fact_hour = $over_hour = $night_hour = $left_hour = $late_hour = 0;
            }
            // statusチェック
            // status_flag 0=実出勤　1=勤務 2=エラー 3=公休 4=欠勤 5=有休
            if ($in_flag === 1 && $out_flag === 0) {
                $data[$user['user_id']]['status'] = '片打刻';
                $data[$user['user_id']]['status_flag'] = 2;
            }
            if ($in_flag === 1 && $out_flag === 1 && $fact_hour === 0) {
                $data[$user['user_id']]['status'] = '勤務時間 0h';
                $data[$user['user_id']]['status_flag'] = 2;
            }
            if (!$shift) {
                if ($in_flag === 0 && $out_flag === 0) {
                    $data[$user['user_id']]['status'] = '未出勤';
                    $data[$user['user_id']]['status_flag'] = 0;
                }
                if ($in_flag === 1 && $out_flag === 1 && $fact_hour > 0) {
                    $data[$user['user_id']]['status'] = '勤務';
                    $data[$user['user_id']]['status_flag'] = 1;
                }
                if ($night_hour > 0 && $fact_hour > 0) {
                    $data[$user['user_id']]['status'] += ' 深夜';
                }
                if ($over_hour > 0 && $fact_hour > 0) {
                    $data[$user['user_id']]['status'] += ' 残業';
                }
            }
            if ($shift) {
                if ($shift_status === 2) {
                    if ($in_flag === 0 && $out_flag === 0) {
                        $data[$user['user_id']]['status'] = '有給休暇取得';
                        $data[$user['user_id']]['status_flag'] = 5;
                    }
                    if ($in_flag === 1 && $out_flag === 1 && $fact_hour > 0) {
                        $data[$user['user_id']]['status'] = '休日（有休）出勤';
                        $data[$user['user_id']]['status_flag'] = 1;
                    }
                }
                if ($shift_status === 1) {
                    if ($in_flag === 0 && $out_flag === 0) {
                        $data[$user['user_id']]['status'] = '公休';
                        $data[$user['user_id']]['status_flag'] = 3;
                    }
                    if ($in_flag === 1 && $out_flag === 1 && $fact_hour > 0) {
                        $data[$user['user_id']]['status'] = '休日出勤';
                        $data[$user['user_id']]['status_flag'] = 1;
                    }
                }
                if ($shift_status === 0) {
                    if ($in_flag === 0 && $out_flag === 0) {
                        $data[$user['user_id']]['status'] = '欠勤';
                        $data[$user['user_id']]['status_flag'] = 4;
                    }
                }
            }
        }

        return $data;
    }
}