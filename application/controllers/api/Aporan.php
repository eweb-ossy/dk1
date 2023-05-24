<?php
defined('BASEPATH') or exit('No direct script access alllowed');

$reqHeaders = apache_request_headers();
$allowedOrigin = [
    'https://dakoku.work',
    'https://4k1hx.cybozu.com'
]; 
if (in_array($reqHeaders['Origin'], $allowedOrigin)) header("Access-Control-Allow-Origin: {$reqHeaders['Origin']}");

ini_set("display_errors", "On");
error_reporting(-1);

class Aporan extends MY_Controller
{
    //アポラン用従業員データ
    public function index()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $end_date = sprintf('%04d-%02d-%02d', $year, $month, $month_days);

        // group name
        for ($i = 1; $i <= 3; $i++) { 
            $result = $this->db->query("SELECT `id`, `group_name`, state, `group_order` FROM user_groups{$i}")->result();
            $group_name[$i] = array_column($result, 'group_name', 'id');
        }

        // group history
        $result = $this->db->query("SELECT `user_id`, `group1_id`, `group2_id`, `group3_id`, `to_date` FROM `group_history` ORDER BY `to_date` ASC")->result();
        foreach ($result as $value) {
            $group_id[$value->user_id][1] = $value->group1_id;
            $group_id[$value->user_id][2] = $value->group2_id;
            $group_id[$value->user_id][3] = $value->group3_id;
        }

        // time data
        $result = $this->db->query("SELECT `user_id`, `dk_date`, `fact_work_hour` FROM `time_data` WHERE DATE_FORMAT(`dk_date`, '%Y%m') = {$year}{$month}")->result();
        foreach ($result as $value) {
            if (! $value->fact_work_hour) {
                continue;
            }
            if (! isset($time_data[$value->user_id])) {
                $time_data[$value->user_id] = [];
            }
            $time_data[$value->user_id][] = [
                'dk_date' => $value->dk_date,
                'time' => $value->fact_work_hour
            ];
        }

        // users data
        $output_data = [];
        $result = $this->db->query("SELECT `user_id`, `name_sei`, `name_mei`, `state`, `entry_date` FROM `user_data` WHERE `aporan_flag` = 1 AND `state` = 1")->result();
        foreach ($result as $value) {
            $name_sei = str_replace(array(" ", "　"), "", $value->name_sei);
            $name_mei = str_replace(array(" ", "　"), "", $value->name_mei);
            $minute = @$time_data[$value->user_id] ? array_sum(array_column($time_data[$value->user_id], 'time')) : 0;
            $entry_date = $value->entry_date;
            if ($entry_date) {
                $date = new DateTime($entry_date);
                $entry_date_view = $date->format('Y年m月d日');
            }
            $output_data[] = [
                'user_id' => str_pad($value->user_id, (int)$this->data['configs']['id_size']->value, '0', STR_PAD_LEFT),
                'user_name' => $name_sei.' '.$name_mei,
                'time' => $minute,
                'hour' => $minute > 0 ? sprintf('%.2f', $minute / 60) : sprintf('%.2f', 0),
                'count' => @$time_data[$value->user_id] ? count($time_data[$value->user_id]) : 0,
                'group1' => @$group_name[1][$group_id[$value->user_id][1]] ?: '',
                'group2' => @$group_name[2][$group_id[$value->user_id][2]] ?: '',
                'group3' => @$group_name[3][$group_id[$value->user_id][3]] ?: '',
                'company' => $this->data['configs']['company_name']->value,
                'user_name2' => $name_sei.$name_mei,
                'entry_date' => $entry_date,
                'entry_date_view' => @$entry_date_view ?: ''
            ];
        }

        // 出力
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($output_data));
    }
}
