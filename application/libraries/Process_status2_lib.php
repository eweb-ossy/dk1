<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_status2_lib
{
    protected $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function status($status_data) {

        $DATETIME = new DateTime($status_data['dk_datetime']);
        $dk_date = $DATETIME->format('Y-m-d');
        $dk_time = $DATETIME->format('H:i:s');

        $this->CI->load->helper('holiday_date');
        $HOLIDAY_DATETIME = new HolidayDateTime($dk_date);
        $w = $HOLIDAY_DATETIME->holiday() ? 7 : $DATETIME->format('w');

        $this->CI->load->database();

        // 設定データ取得
        $result = $this->CI->db->query("SELECT `config_name`, `value` FROM config_values")->result();
        $config = array_column($result, 'value', 'config_name');
        unset($result);
        
        // シフトデータ取得
        $shift_data = $this->CI->db->query("SELECT `in_time`, `out_time`, `status` FROM `shift_data` WHERE `user_id` = {$status_data['user_id']} AND `dk_date` = '{$dk_date}' LIMIT 1")->row();
        
        // ルール取得
        $this->CI->load->library('process_rules_lib');
        $rules = $this->CI->process_rules_lib->get_rule($status_data['user_id']);

        // AUTOシフトの場合 ルールより定時を取得し、シフトデータへ代入
        if ($config['auto_shift_flag'] === '1' && !$shift_data && $rules) {
            $basic_rest_weekday = str_split($rules->basic_rest_weekday);
            if ($basic_rest_weekday[$w] === 0) {
                $shift_data['in_time'] = $rules->basic_in_time;
                $shift_data['out_time'] = $rules->basic_out_time;
                $shift_data['status'] = 0;
            }
            if ($basic_rest_weekday[$w] === 1) {
                $shift_data['status'] = 1;
            }
        }

        // ルールがある場合はルールを適応
        if ($rules) {
            if ($status_data['flag'] === 'in') {
                $data['in_work_time'] = $dk_time;
                $in_marume_flag = (int)$rules->in_marume_flag;
                if ($in_marume_flag === 1 || $in_marume_flag === 3 || $in_marume_flag === 5) {
                    $in_marume_hour = $rules->in_marume_hour ? (int)$rules->in_marume_hour : 1;
                    $this->CI->load->helper('work_input');
                    $data['in_work_time'] = timeUp($status_data['dk_datetime'], $in_marume_hour)->format('H:i:s');
                }
                if ($in_marume_flag === 2 || $in_marume_flag === 3) {
                    $in_marume_time = $rules->in_marume_time ?: $dk_time;
                    if (strtotime($status_data['dk_datetime']) <= strtotime($dk_date.' '.$in_marume_time)) {
                        $data['in_work_time'] = $in_marume_time;
                    }
                }
                if ($in_marume_flag === 4 || $in_marume_flag === 5) {
                    $in_marume_time = @$shift_data->in_time ?: $dk_time;
                    if (strtotime($status_data['dk_datetime']) <= strtotime($dk_date.' '.$in_marume_time)) {
                        $data['in_work_time'] = $in_marume_time;
                    }
                }
            }
            if ($status_data['flag'] === 'out') {
                $data['out_work_time'] = $dk_time;
                $out_marume_flag = (int)$rules->out_marume_flag;
                if ($out_marume_flag === 1 || $out_marume_flag === 3 || $out_marume_flag === 5) {
                    $out_marume_hour = $rules->out_marume_hour ? (int)$rules->out_marume_hour : 1;
                    $this->CI->load->helper('work_input');
                    $data['out_work_time'] = timeDown($status_data['dk_datetime'], $out_marume_hour)->format('H:i:s');
                }
                if ($out_marume_flag === 2 || $out_marume_flag === 3) {
                    $out_marume_time = $rules->out_marume_time ?: $dk_time;
                    if (strtotime($status_data['dk_datetime']) >= strtotime($dk_date.' '.$out_marume_time)) {
                        $data['out_work_time'] = $out_marume_time;
                    }
                }
                if ($out_marume_flag === 4 || $out_marume_flag === 5) {
                    $out_marume_time = @$shift_data->out_time ?: $dk_time;
                    if (strtotime($status_data['dk_datetime']) >= strtotime($dk_date.' '.$out_marume_time)) {
                        $data['out_work_time'] = $out_marume_time;
                    }
                }

                if ($rules->rest_rule_flag == 1) {
                    $rest_rule = $this->CI->db->query("SELECT `rest_time`, `rest_type`, `limit_work_hour`, `rest_in_time`, `rest_out_time` FROM `rest_rules` WHERE `config_rules_id` = {$rules->id} LIMIT 1")->row();
                }

                $over_limit_hour = $rules->over_limit_hour ? $rules->over_limit_hour : null;
            }
        } else { // ルールがない場合は、入力日時を work_time に代入
            if ($status_data['flag'] === 'in') {
                $data['in_work_time'] = $status_data['dk_datetime'];
            }
            if ($status_data['flag'] === 'out') {
                $data['out_work_time'] = $status_data['dk_datetime'];
                $over_limit_hour = 480; // 残業発生時間　初期 8h
            }
        }

        // 時間計算function 0000-00-00 00:00:00 x 2 -> minute
        function calc($intime, $outtime) {
            $in_datetime = new DateTime($intime);
            $out_datetime = new DateTime($outtime);
            $time_diff = $in_datetime->diff($out_datetime);
            $day = (int)$time_diff->format('%a');
            $h = (int)$time_diff->format('%h');
            $i = (int)$time_diff->format('%i');
            return $day*60*24+$h*60+$i;
        }

        // 出勤時
        if ($status_data['flag'] === 'in') {
            $data['dk_date'] = $dk_date;
            $data['user_id'] = $status_data['user_id'];
            $data['in_time'] = $dk_time;
            $data['in_flag'] = 1;
            $data['status'] = '出勤中';
            $data['status_flag'] = 1;

            if ($shift_data) { // シフトデータ有 
                if ($shift_data->in_time) { // 遅刻
                    $data['late_hour'] = strtotime($dk_date.' '.$shift_data->in_time) < strtotime($data['in_work_time']) ? calc($dk_date.' '.$shift_data->in_time, $data['in_work_time']) : 0;
                    $data['status'] = $data['late_hour'] > 0 ? $data['status'] .= ' 遅刻' : $data['status'] .= '';
                }
            }

            $data['area_in_id'] = isset($status_data['area_id']) ? $status_data['area_id'] : 0;

            $now = new DateTime();
            $data['created_at'] = $now->format('Y-m-d H:i:s');
            $data['updated_at'] = $now->format('Y-m-d H:i:s');
            return $this->CI->db->insert('time_data', $data);
        }

        // 退勤時
        if ($status_data['flag'] === 'out') {
            $time_data = $this->CI->db->query("SELECT `id`, `in_time`, `in_work_time`, `late_hour` FROM `time_data` WHERE `user_id` = {$status_data['user_id']} AND `dk_date` = '{$dk_date}' LIMIT 1")->row();
            $data['out_time'] = $dk_time;
            $data['out_flag'] = 1;
            $data['status'] = '勤務';

            // 実労働時間
            $data['fact_hour'] = calc($dk_date.' '.$time_data->in_time, $status_data['dk_datetime']);

            // 労働時間
            $data['fact_work_hour'] = calc($dk_date.' '.$time_data->in_work_time, $dk_date.' '.$data['out_work_time']);

            // 休憩時間取得
            $data['rest'] = 0;
            if (isset($rest_rule)) { // ルールがある場合
                if ($rest_rule->rest_type == 1) {
                    if ($data['fact_work_hour'] >= $rest_rule->limit_work_hour) {
                        $data['rest'] = (int)$rest_rule->rest_time;
                    }
                }
                if ($rest_rule->rest_type == 2) {
                    if (strtotime($dk_date.' '.$time_data->in_work_time) <= strtotime($dk_date.' '.$rest_rule->rest_in_time) && strtotime($dk_date.' '.$data['out_work_time']) >= strtotime($dk_date.' '.$rest_rule->rest_out_time)) {
                        $data['rest'] = (int)$rest_rule->rest_time;
                    }
                }
            }
            if ($config['rest_input_flag'] == 1) { // 休憩入力がある場合
                $data['rest'] = (int)$this->CI->db->query("SELECT SUM(`rest_hour`) AS rest FROM rest_data WHERE time_data_id = {$time_data->id}")->row()->rest;
            }

            // 労働時間 = 労働時間 - 休憩時間
            $data['fact_work_hour'] = $data['fact_work_hour'] - $data['rest'];

            if ($shift_data) { // シフトデータ有 
                $data['status'] = $time_data->late_hour > 0 ? $data['status'] .= ' 遅刻' : $data['status'] .= ''; //遅刻
                if ($shift_data->out_time) { // 早退
                    $data['left_hour'] = strtotime($dk_date.' '.$shift_data->out_time) > strtotime($data['out_work_time']) ? calc($dk_date.' '.$shift_data->out_time, $data['out_work_time']) : 0;
                    $data['status'] = $data['out_work_time'] > 0 ? $data['status'] .= ' 早退' : $data['status'] .= '';
                }
            }

            // 残業
            if ($over_limit_hour) {
                $data['over_hour'] =  $data['fact_work_hour'] > $over_limit_hour ? $data['fact_work_hour'] - $over_limit_hour : 0;
                $data['status'] = $data['over_hour'] > 0 ? $data['status'] .= ' 残業' : $data['status'] .= '';
            }

            // 深夜 22時以降
            $data['night_hour'] = strtotime($dk_date.' '.$data['out_work_time']) > strtotime($dk_date.' 22:00:00') ? calc($dk_date.' 22:00:00', $dk_date.' '.$data['out_work_time']) : 0;
            $data['status'] = $data['night_hour'] > 0 ? $data['status'] .= ' 深夜' : $data['status'] .= '';

            if ($data['fact_work_hour'] <= 0) {
                $data['status'] = '勤務時間 0h';
                $data['status_flag'] = 9;
            }

            $data['area_out_id'] = isset($status_data['area_id']) ? $status_data['area_id'] : 0;

            $now = new DateTime();
            $data['updated_at'] = $now->format('Y-m-d H:i:s');
            $this->CI->db->where('id', $time_data->id);
            return $this->CI->db->update('time_data', $data);
        }
    }
}
