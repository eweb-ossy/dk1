<?php
defined('BASEPATH') or exit('No direct script access alllowed');

header('Access-Control-Allow-Origin: *');

ini_set("display_errors", "On");
error_reporting(-1);

class Esnapenalty extends MY_Controller
{
    public function index()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $result = $this->db->query('SELECT group_id, title FROM group_title')->result();
        $group_title = array_column($result, 'title', 'group_id');
        for ($i = 1; $i <= 3; $i++) {
            if (isset($group_title[$i])) {
                $result = $this->db->query("SELECT id, group_name, state, group_order FROM user_groups{$i} ORDER BY group_order ASC")->result_array();
                $groupData = array_column($result, 'group_name', 'id');
                $group[$i] = [
                    'title' => $group_title[$i],
                    'data' => $groupData
                ];
            }
        }
        unset($result);

        $group_history = [];
        $result = $this->db->query("SELECT `user_id`, `group1_id`, `group2_id`, `group3_id` FROM `group_history` WHERE DATE_FORMAT(`to_date`, '%Y%m') <= '{$year}{$month}' ORDER BY `user_id` ASC, `to_date` ASC")->result();
        foreach ($result as $key => $value) {
            $group_history[$value->user_id] = [
                $group['1']['title'] => isset($group['1']['data'][$value->group1_id]) ? $group['1']['data'][$value->group1_id] : '',
                $group['2']['title'] => isset($group['2']['data'][$value->group2_id]) ? $group['2']['data'][$value->group2_id] : '',
                $group['3']['title'] => isset($group['3']['data'][$value->group3_id]) ? $group['3']['data'][$value->group3_id] : '',
            ];
        }
        unset($result);

        $result = $this->db->query("SELECT dk_date, `user_id`, in_time, out_time, in_work_time, out_work_time, rest, in_flag, out_flag, fact_hour, fact_work_hour, `status`, status_flag, over_hour, left_hour, late_hour, memo FROM `time_data` WHERE DATE_FORMAT(`dk_date`, '%Y%m') = {$year}{$month} ORDER BY `user_id` ASC, `dk_date` ASC")->result();
        $time_data = [];
        foreach ($result as $key => $value) {
            $time_data[$value->user_id][$value->dk_date] = $result[$key];
        }
        unset($result);

        $result = $this->db->query("SELECT `dk_date`, `user_id`, `in_time`, `out_time`, `status`, `rest`, `hour` FROM `shift_data` WHERE DATE_FORMAT(`dk_date`, '%Y%m') = {$year}{$month} ORDER BY `user_id` ASC, `dk_date` ASC")->result();
        $shift_data = [];
        foreach ($result as $key => $value) {
            $shift_data[$value->user_id][$value->dk_date] = $result[$key];
        }
        unset($result);

        $result = $this->db->query("SELECT CONCAT(`name_sei`, ' ', `name_mei`) AS `name`, `user_id` FROM user_data WHERE DATE_FORMAT(`resign_date`, '%Y%m') >= '{$year}{$month}' OR `resign_date` IS NULL ORDER BY `user_id` ASC")->result();
        foreach ($result as $key => $value) {
            $user_data[$value->user_id] = [
                'user_id' => $value->user_id,
                'name' => $value->name,
                'group' => isset($group_history[$value->user_id]) ? $group_history[$value->user_id] : [],
                'time' => isset($time_data[$value->user_id]) ? $time_data[$value->user_id] : [],
                'shift' => isset($shift_data[$value->user_id]) ? $shift_data[$value->user_id] : []
            ];
        }
        unset($result);

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($user_data));
    }
}