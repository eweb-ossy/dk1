<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_list_month2 extends CI_Controller
{
    public function getData()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 月の日数
        $type = $this->input->post('type');

        $this->load->database();
        
        // 設定データ取得
        $result = $this->db->query("SELECT `config_name`, `value` FROM config_values")->result_array();
        $config_data = array_column($result, NULL, 'config_name');
        $end_day = $config_data['end_day']['value'];
        $list_month_view_flag = $type ?: $config_data['list_month_view_flag']['value'];
        unset($result);

        if ($end_day != 0) {
            $pre = new DateTime("{$year}-{$month}-01");
            $pre->sub(DateInterval::createFromDateString('1 month'));
            $pre_year = $pre->format('Y');
            $pre_month = $pre->format('m');
            $pre_month_days = cal_days_in_month(CAL_GREGORIAN, $pre_month, $pre_year);
            $first_date = sprintf('%04d-%02d-%02d', $pre_year, $pre_month, $end_day + 1);
            $end_date = sprintf('%04d-%02d-%02d', $year, $month, $end_day);
            $month_days = (strtotime($end_date) - strtotime($first_date)) / (60 * 60 * 24) + 1;
        } else {
            $first_date = sprintf('%04d-%02d-%02d', $year, $month, 1);
            $end_date = sprintf('%04d-%02d-%02d', $year, $month, $month_days);
        }
        $date_list = range(strtotime($first_date), strtotime($end_date), 60*60*24); // 月間レンジ

        // グループタイトル取得
        $result = $this->db->query('SELECT group_id, title FROM group_title')->result();
        $group_title = array_column($result, 'title', 'group_id');
        unset($result);

        // 日付範囲表示用テキスト

        $data['text'] = date('Y年m月d日', strtotime($first_date)).'から'.date('Y年m月d日', strtotime($end_date)).'までの'.$month_days.'日間';

        // コラムデータ
        $this->load->helper('holiday_date');
        $data['columns'] = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'width'=>60, 'frozen'=>true, 'hozAlign'=>'center'],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'topCalc'=>'count', 'width'=>110, 'frozen'=>true]
        ];
        for ($i=1; $i<=3; $i++) {
            if (isset($group_title[$i])) {
                array_push($data['columns'], ['title'=> $group_title[$i], 'field'=> "group{$i}_name", 'width'=>100, 'headerFilter'=> 'list', 'headerFilterParams'=> ['valuesLookup'=> true, 'clearable'=> false], 'visible'=> false]);
            }
        }
        $now = new DateTime();
        $week = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        foreach($date_list as $date) {
            $holiday_datetime = new HolidayDateTime(date('Y-m-d', $date));
            $week_day = $holiday_datetime->holiday();
            $class = $week_day ? 'holiday' : $week[date('w', $date)];
            if ($now->format('Y-m-d') === date('Y-m-d', $date)) {
                $class = 'today';
            }
            if ($list_month_view_flag == 3) {
               array_push($data['columns'],
               ['title'=>  date('j', $date), 'headerHozAlign'=>'center', 'columns'=> [
                    ['title'=> '出勤', 'field'=> 'in_work_time_'.date('j', $date), 'hozAlign'=>'center'],
                    ['title'=> '退勤', 'field'=> 'out_work_time_'.date('j', $date), 'hozAlign'=>'center'],
                    ['title'=> '休憩', 'field'=> 'rest_'.date('j', $date), 'hozAlign'=>'center'],
                    ['title'=> '実労', 'field'=> 'fact_work_time_'.date('j', $date), 'hozAlign'=>'center']
               ]]
               );
            } else {
                array_push($data['columns'], ['title'=> date('j', $date), 'field'=>'day_'.date('j', $date), 'width'=>50, 'cssClass'=>$class.' week', 'hozAlign'=>'center', 'topCalc'=>'count', 'bottomCalc'=>'sum', 'bottomCalcParams'=>['precision'=>2]]);
            }
            
        }
        array_push($data['columns'], ['title'=>'勤務日数', 'field'=>'sum', 'hozAlign'=>'right', 'width'=>60]);
        array_push($data['columns'], ['title'=>'勤務時間', 'field'=>'time', 'hozAlign'=>'right', 'width'=>60]);

        // グループ項目取得
        for ($i=1; $i<=3; $i++) { 
            $result = $this->db->query("SELECT id, group_name FROM user_groups{$i} ORDER BY group_order ASC")->result_array();
            $group[$i] = array_column($result, 'group_name', 'id');
            unset($result);
        }

        // グループ履歴取得
        $result = $this->db->query("SELECT `user_id`, group1_id, group2_id, group3_id FROM group_history WHERE to_date <= '{$end_date}' ORDER BY to_date ASC")->result_array();
        $group_history = array_column($result, NULL, 'user_id');
        unset($result);

        // 従業員データ取得
        $result = $this->db->query("SELECT CONCAT(name_sei, ' ', name_mei) AS `user_name`,  `user_id` FROM user_data WHERE ( resign_date >= '{$first_date}' OR resign_date IS NULL ) AND ( entry_date <= '{$end_date}' OR entry_date IS NULL ) AND ( management_flag != 1 OR management_flag IS NULL ) ORDER BY `user_id` ASC")->result_array();
        $user_data = array_column($result, NULL, 'user_id');
        unset($result);

        // 勤務データ取得
        $time_data = [];
        // $end_date_next = date('Y-m-d', strtotime($end_date.' 1 day'));
        $result = $this->db->query("SELECT dk_date, `user_id`, substring(in_time, 1, 5) AS in_time, substring(out_time, 1, 5) AS out_time, substring(in_work_time, 1, 5) AS in_work_time, substring(out_work_time, 1, 5) AS out_work_time, rest, fact_hour, fact_work_hour FROM time_data WHERE dk_date BETWEEN '{$first_date}' AND '{$end_date}' AND ( fact_work_hour > 0 OR fact_hour > 0)")->result_array();
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
        $i = 0;
        foreach ($user_data as $key => $value) {
            $group1_id = isset($group_history[$key]['group1_id']) ? $group_history[$key]['group1_id'] : null;
            $group2_id = isset($group_history[$key]['group2_id']) ? $group_history[$key]['group2_id'] : null;
            $group3_id = isset($group_history[$key]['group3_id']) ? $group_history[$key]['group3_id'] : null;
            $data['data'][$i] = [
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
                    $in_work_time = isset($time_data[$key][date('Y-m-d', $date)]) ? $time_data[$key][date('Y-m-d', $date)]['in_work_time'] : '';
                    $out_work_time = isset($time_data[$key][date('Y-m-d', $date)]) ? $time_data[$key][date('Y-m-d', $date)]['out_work_time'] : '';
                    if ($list_month_view_flag == 1) {
                        $time_view = $fact_work_hour;
                    }
                    if ($list_month_view_flag == 2) {
                        $time_view = $in_work_time.'-'.$out_work_time;
                    }
                    if ($list_month_view_flag == 3) {
                        $rest = isset($time_data[$key][date('Y-m-d', $date)]) ? $time_data[$key][date('Y-m-d', $date)]['rest'] : 0;
                        $rest_hour = $rest > 0 ? round($rest/60, 2) : NULL;
                        $data['data'][$i]['in_work_time_'.date('j', $date)] = $in_work_time;
                        $data['data'][$i]['out_work_time_'.date('j', $date)] = $out_work_time;
                        $data['data'][$i]['rest_'.date('j', $date)] = $rest_hour;
                        $data['data'][$i]['fact_work_time_'.date('j', $date)] = $fact_work_hour;
                    }
                    $sum += 1;
                    $time += $fact_work_hour;
                } else {
                    $time_view = '';
                }
                if ($list_month_view_flag == 1 || $list_month_view_flag == 2) {
                    $data['data'][$i]['day_'.date('j', $date)] = $time_view;
                }
                if ($list_month_view_flag == 3 && !$fact_work_hour) {
                    $data['data'][$i]['in_work_time_'.date('j', $date)] = '';
                    $data['data'][$i]['out_work_time_'.date('j', $date)] = '';
                    $data['data'][$i]['rest_'.date('j', $date)] = '';
                    $data['data'][$i]['fact_work_time_'.date('j', $date)] = '';
                }
            }
            $data['data'][$i]['sum'] = $sum > 0 ? $sum.'日' : '';
            $data['data'][$i]['time'] = $time > 0 ? $time.'h' : '';
            $i++;
        }

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}