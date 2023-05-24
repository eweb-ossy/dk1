<?php
defined('BASEPATH') or exit('No direct script access alllowed');

// header('Access-Control-Allow-Origin: dakoku.work');
header('Access-Control-Allow-Origin: *');

class Status extends CI_Controller
{
    public function index()
    {
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('id, config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        $data = [];
        $now = new DateTime();
        $now_date = $now->format('Y-m-d');
        // 日付またぎあり && 日付またぎ締め時間内
        // format('G') 時 24時間単位　0-23
        if ((int)$config_data['over_day'] > 0 && (int)$now->format('G') >= 0 && (int)$now->format('G') <= (int)$config_data['over_day']) {
            $next_day = $now->sub(DateInterval::createFromDateString('1 day'));
            $now_date = $next_day->format('Y-m-d');
        }
        $this->load->model('model_time');
        // 年月日で検索し、該当する全ての勤務状況+従業員情報を返す day all + user_data
        $result = $this->model_time->gets_now_users($now_date);

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($result));
    }

    public function nowUsers()
    {
        $now = new DateTime();
        $now_date = $now->format('Y-m-d');

        $data = ['in'=>[], 'out'=>[]];
        $this->load->database();
        $result = $this->db->query("SELECT `time_data`.`user_id`, `in_time`, `out_time`, `in_work_time`, `out_work_time`, `in_flag`, `out_flag`, `area_id`, CONCAT(`name_sei`, ' ', `name_mei`) AS `name` FROM `time_data` JOIN `user_data` ON time_data.user_id = user_data.user_id WHERE `dk_date` = '{$now_date}' AND ( `in_flag` != '0' OR `out_flag` != '0' )")->result();
        foreach ($result as $value) {
            $type = $value->in_flag == 1 && $value->out_flag == 0 ? 'in' : 'out';
            $in_time = $value->in_time ?: $value->in_work_time;
            $out_time = $value->out_time ?: $value->out_work_time;
            $data[$type][] = [
                'user_id'=> $value->user_id,
                'name'=> $value->name,
                'time'=> $type === 'in' ? $in_time : $out_time,
                'area_id'=> $value->area_id
            ];
        }
        unset($value);
        $times = array_column($data['in'], 'time');
        array_multisort($times, SORT_DESC, $data['in']);
        $times = array_column($data['out'], 'time');
        array_multisort($times, SORT_DESC, $data['out']);

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}
