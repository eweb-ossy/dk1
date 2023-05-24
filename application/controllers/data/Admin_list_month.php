<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_list_month extends CI_Controller
{
    public function table_data()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 月の日数

        $this->load->database();
        
        // 設定データ取得
        $result = $this->db->query("SELECT `config_name`, `value` FROM config_values")->result_array();
        $config_data = array_column($result, NULL, 'config_name');
        $end_day = $config_data['end_day']['value'];
        $list_month_view_flag = $config_data['list_month_view_flag']['value'];
        unset($result);

        if ($end_day != 0) {
            $pre = new DateTime("{$year}-{$month}-01");
            $pre->sub(DateInterval::createFromDateString('1 month'));
            $pre_year = $pre->format('Y');
            $pre_month = $pre->format('m');
            $pre_month_days = cal_days_in_month(CAL_GREGORIAN, $pre_month, $pre_year);
            $first_date = sprintf('%04d-%02d-%02d', $pre_year, $pre_month, $end_day + 1);
            $end_date = sprintf('%04d-%02d-%02d', $year, $month, $end_day);
        } else {
            $first_date = sprintf('%04d-%02d-%02d', $year, $month, 1);
            $end_date = sprintf('%04d-%02d-%02d', $year, $month, $month_days);
        }
        $date_list = range(strtotime($first_date), strtotime($end_date), 60*60*24); // 月間レンジ

        // グループ項目取得
        for ($i=1; $i<=3; $i++) { 
            $result = $this->db->query("SELECT id, group_name FROM user_groups{$i} ORDER BY group_order ASC")->result_array();
            $group[$i] = array_column($result, 'group_name', 'id');
            unset($result);
        }

        // グループ履歴取得
        $result = $this->db->query("SELECT `user_id`, group1_id, group2_id, group3_id FROM group_history ORDER BY to_date DESC")->result_array();
        $group_history = array_column($result, NULL, 'user_id');
        unset($result);

        // 従業員データ取得
        $result = $this->db->query("SELECT CONCAT(name_sei, ' ', name_mei) AS `user_name`,  `user_id` FROM user_data WHERE ( DATE_FORMAT(resign_date, '%Y%m') >= '{$year}{$month}' OR resign_date IS NULL ) AND ( entry_date <= '{$end_date}' OR entry_date IS NULL ) AND ( management_flag != 1 OR management_flag IS NULL ) ORDER BY `user_id` ASC")->result_array();
        $user_data = array_column($result, NULL, 'user_id');
        unset($result);

        // 勤務データ取得
        $time_data = [];
        $result = $this->db->query("SELECT dk_date, `user_id`, substring(in_time, 1, 5) AS in_time, substring(out_time, 1, 5) AS out_time, substring(in_work_time, 1, 5) AS in_work_time, substring(out_work_time, 1, 5) AS out_work_time, rest, fact_hour, fact_work_hour FROM time_data WHERE DATE_FORMAT(dk_date, '%Y%m') = '{$year}{$month}' AND ( fact_work_hour > 0 OR fact_hour > 0)")->result_array();
        foreach ($result as $value) {
           $time_data[$value['user_id']][$value['dk_date']] = [
            'in_time'=> $value['in_time'],
            'out_time'=> $value['out_time'],
            'in_work_time'=> $value['in_work_time'],
            'out_work_time'=> $value['out_work_time'],
            'rest'=> $value['rest'],
            'fact_hour'=> $value['fact_hour'],
            'fact_work_hour'=> $value['fact_work_hour']
           ];
        }
        unset($result);
        unset($value);

        // 出力データ作成
        $data = [];
        $i = 0;
        foreach ($user_data as $key => $value) {
            $group1_id = isset($group_history[$key]['group1_id']) ? $group_history[$key]['group1_id'] : null;
            $group2_id = isset($group_history[$key]['group2_id']) ? $group_history[$key]['group2_id'] : null;
            $group3_id = isset($group_history[$key]['group3_id']) ? $group_history[$key]['group3_id'] : null;
            $data[$i] = [
                'user_id'=> $key,
                'user_name'=> $value['user_name'],
                'group1_name'=> $group1_id > 0 ? $group[1][$group1_id] : '',
                'group2_name'=> $group2_id > 0 ? $group[2][$group2_id] : '',
                'group3_name'=> $group3_id > 0 ? $group[3][$group3_id] : ''
            ];
            $sum = $time = 0;
            foreach($date_list as $date) {
                $fact_work_hour = isset($time_data[$key][date('Y-m-d', $date)]) ? $time_data[$key][date('Y-m-d', $date)]['fact_work_hour'] : 0;
                $fact_work_hour = $fact_work_hour > 0 ? round($fact_work_hour/60, 2) : NULL;
                if ($fact_work_hour) {
                    if ($list_month_view_flag == 1) {
                        $time_view = $fact_work_hour;
                    }
                    if ($list_month_view_flag == 2) {
                        $in_work_time = isset($time_data[$key][date('Y-m-d', $date)]) ? $time_data[$key][date('Y-m-d', $date)]['in_work_time'] : '';
                        $out_work_time = isset($time_data[$key][date('Y-m-d', $date)]) ? $time_data[$key][date('Y-m-d', $date)]['out_work_time'] : '';
                        $time_view = $in_work_time.'-'.$out_work_time;
                    }
                    $sum += 1;
                    $time += $fact_work_hour;
                } else {
                    $time_view = '';
                }
                $data[$i]['day_'.date('j', $date)] = $time_view;
            }
            $data[$i]['sum'] = $sum > 0 ? $sum.'日' : '';
            $data[$i]['time'] = $time > 0 ? $time.'h' : '';
            $i++;
        }

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function graph_data()
    {
        function filter_group($var)
        {
            return ($var > 0) ? true : false;
        }
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $end_date = $year.'-'.$month.'-'.$month_days;
        $to_month = $year.$month;
        $this->load->model('model_user');
        $all_user_sum = $this->model_user->find_exist_month_num($to_month, $end_date);
        $this->load->model('model_time');
        $result = $this->model_time->find_month_all($year, $month);
        $data = [];
        // 登録人数(user_id)を抽出し、重複を削除(array_unique)した数(count)
        $all_user = count(array_unique(array_column($result, 'user_id')));
        $fact_work_hour_data = array_column($result, 'fact_work_hour');
        // $fact_work_hour_group1 = array_column(array_filter($result, 'filter_group'), 'fact_work_hour');
        // $fact_work_hour_group1 = array_filter($result, 'filter_group');
        $all_hour = array_sum($fact_work_hour_data);
        // $group1_hour = array_sum($fact_work_hour_group1);
        $all_sum = count($fact_work_hour_data);
        $data = [
            'month_days'=>$month_days,
            'all_hour'=>$all_hour,
            'all_sum'=>$all_sum,
            'all_user_sum'=>$all_user_sum,
            'all_user'=>$all_user,
            'fact_work_hour_data'=>$fact_work_hour_data
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}
