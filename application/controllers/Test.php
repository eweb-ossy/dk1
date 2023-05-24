<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Test extends CI_Controller
{

    public function index()
    {
        // $this->load->model('model_get');
        // $data = $this->model_get->group_data(); // グループデータ
        // $option = [
        //     // 'user_id' => '10002',
        //     'state_date' => '2017-4-1'
        // ];
        // $data = $this->model_get->group_history_data($option); // 全グループ履歴を返す

        // $data = $this->model_get->group_history_data('10002'); // グループ履歴データを返す 

        // $option = [
        //     'user_id' => '10002',
        //     // 'state' => 1,
        //     // 'state_date' => '2022-4-1',
        // ];
        // $data = $this->model_get->user_data($option); // 従業員データを返す
        
        // $option = [
        //     'user_id' => '10002',
        //     'start_date' => '2022-4-1',
        //     // 'end_date' => '2022-4-18'
        // ];
        // $data = $this->model_get->times_data($option); // 個人従業員　管理データを返す


        // // test python 呼び出し
        // $database = DATABASE;
        // $command = "echo $database | python3 ../../python/test.py";
        // exec($command, $data);

        // $this->load->database();
        // $result = $this->db->query("SELECT `user_id`, group1_id, group2_id, group3_id, to_date FROM group_history ORDER BY to_date ASC")->result();
        // $data = array_column($result, NULL, 'user_id');

        // $result = $this->db->query("SELECT user_data.user_id, group_history.group1_id as g1, group_history.group2_id as g2, group_history.group3_id as g3, group_history.to_date FROM user_data LEFT JOIN group_history ON user_data.user_id = group_history.user_id WHERE user_data.state = 1 ORDER BY group_history.to_date ASC")->result();
        // $data = array_column($result, NULL, 'user_id');

        // // ルールを取得し抽出
        // $result = $this->db->query("SELECT `user_id`, group_id, group_no, all_flag, basic_in_time, basic_out_time, basic_rest_weekday FROM config_rules WHERE basic_in_time IS NOT NULL AND basic_out_time IS NOT NULL AND basic_rest_weekday IS NOT NULL")->result();

        // $dk_date = '2020-11-1';
        // // 勤務データ取得
        // $result = $this->db->query("SELECT id, `user_id`, in_time, out_time, in_work_time, out_work_time, rest, in_flag, out_flag, fact_hour, fact_work_hour FROM time_data WHERE dk_date = '{$dk_date}'")->result();
        // $times_data = array_column($result, NULL, 'user_id');

        // $index = array_search('10003', array_column($result, 'user_id'));
        // if ($index === false) {
        //     $g_id = array_search(1, array_column($result, 'group_id'));
        //     $g_no = array_search(3, array_column($result, 'group_no'));
        //     $index = $g_id === $g_no ? $g_id : false;
        // }
        // if ($index === false) {
        //     $index = array_search(1, array_column($result, 'all_flag'));
        // }
        // $data = $index !== false ? $result[$index] : [];

        $now = new DateTime();
        $dk_date = $now->format('Y-m-d');
        $dk_date = '2020-11-01';

        $this->load->library('auto_lib');
        $data = $this->auto_lib->index($dk_date);

        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}