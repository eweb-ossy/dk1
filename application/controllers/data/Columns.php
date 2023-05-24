<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Columns extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('model_get');
        $this->_group_title = $this->model_get->group_title();

        $this->load->model('model_group');
        $result = $this->model_group->find_group1_all();
        foreach ($result as $row) {
            $this->_group1_name[$row->group_name] = [$row->group_name];
        }
        $this->_group1_name[''] = ['ALL'];
        $result = $this->model_group->find_group2_all();
        foreach ($result as $row) {
            $this->_group2_name[$row->group_name] = [$row->group_name];
        }
        $this->_group2_name[''] = ['ALL'];
        $result = $this->model_group->find_group3_all();
        foreach ($result as $row) {
            $this->_group3_name[$row->group_name] = [$row->group_name];
        }
        $this->_group3_name[''] = ['ALL'];

        // get area name
        $this->load->model('model_area_data');
        $where = [];
        $result = $this->model_area_data->find('area_name', $where, '');
        foreach($result as $row) {
            $this->_area_name[$row->area_name] = [$row->area_name];
        }
        $this->_area_name[''] = ['ALL'];
    }

    // list day 用　
    public function list_day()
    {
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        // message 
        $message['in'] = $message['out'] = 0;
        $this->load->database();
        $query = $this->db->query('SELECT * FROM message_title_data')->result();
        foreach ($query as $value) {
            $message[$value->type] = $value->flag;
        }

        $columns_data = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'frozen'=>true, 'hozAlign'=>'center', 'output'=>true],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110, 'frozen'=>true, 'output'=>true]
        ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if ((int)$config_data['area_flag'] === 1) {
            array_push($columns_data, ['title'=>'出勤エリア', 'field'=>'area', 'headerFilterPlaceholder'=>'場所', 'headerFilterParams'=>['values'=>@$this->_area_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        array_push($columns_data, ['title'=>'シフト', 'output'=>true, 'columns'=>[
            ['title'=>'予定', 'field'=>'shift_status', 'width'=>80, 'hozAlign'=>'center', 'output'=>true],
            ['title'=>'出勤予定', 'field'=>'shift_in_time', 'width'=>50, 'hozAlign'=>'center', 'output'=>true, 'topCalc'=>'count'],
            ['title'=>'退勤予定', 'field'=>'shift_out_time', 'width'=>50, 'hozAlign'=>'center', 'output'=>true, 'topCalc'=>'count'],
            ['title'=>'予定時間', 'field'=>'shift_hour', 'width'=>50, 'hozAlign'=>'center', 'output'=>true]
            ]]
        );
        array_push($columns_data, ['title'=>'状況', 'field'=>'status', 'width'=>100, 'hozAlign' => 'center', 'output'=>true]);
        array_push($columns_data, ['title'=>'実出時刻', 'field'=>'in_time', 'width'=>50, 'hozAlign'=>'center', 'output'=>true]);
        array_push($columns_data, ['title'=>'実退時刻', 'field'=>'out_time', 'width'=>50, 'hozAlign'=>'center', 'output'=>true]);
        array_push($columns_data, ['title'=>'出勤時刻', 'field'=>'in_work_time', 'topCalc'=>'count', 'width'=>50, 'hozAlign'=>'center', 'output'=>true]);
        array_push($columns_data, ['title'=>'退勤時刻', 'field'=>'out_work_time', 'topCalc'=>'count', 'width'=>50, 'hozAlign'=>'center', 'output'=>true]);

        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'労働時間', 'field'=>'work_hour', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'work_minute', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'労働時間 分', 'field'=>'work_minute', 'width'=>65, 'hozAlign'=>'right', 'output'=>true]);
        }

        if ((int)$config_data['normal_time_flag'] === 1) {
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'通常時間', 'field'=>'normal_hour', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'normal_minute', 'width'=>50, 'align'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'通常時間 分', 'field'=>'normal_minute', 'width'=>65, 'hozAlign'=>'right', 'output'=>true]);
            }
        }

        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'休憩時間', 'field'=>'rest_hour', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'rest_minute', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'休憩時間 分', 'field'=>'rest_minute', 'width'=>65, 'hozAlign'=>'right', 'output'=>true]);
        }

        if ((int)$config_data['over_time_view_flag'] === 1) {
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'残業時間', 'field'=>'over_hour', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'over_minute', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'残業時間 分', 'field'=>'over_minute', 'topCalc'=>'count', 'width'=>65, 'hozAlign'=>'right', 'output'=>true]);
            }
        }

        if ((int)$config_data['night_time_view_flag'] === 1) {
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'深夜時間', 'field'=>'night_hour', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'night_minute', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'深夜時間 分', 'field'=>'night_minute', 'width'=>65, 'hozAlign'=>'right', 'output'=>true]);
            }
        }

        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'遅刻時間', 'field'=>'late_hour', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'late_minute', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'遅刻時間 分', 'field'=>'late_minute', 'width'=>65, 'hozAlign'=>'right', 'output'=>true]);
        }

        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'早退時間', 'field'=>'left_hour', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'left_minute', 'width'=>50, 'hozAlign'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'早退時間 分', 'field'=>'left_minute', 'width'=>65, 'hozAlign'=>'right', 'output'=>true]);
        }

        array_push($columns_data, ['title'=>'メモ', 'field'=>'memo', 'tooltip'=>true, 'output'=>true]);

        if ($message['in'] || $message['out']) {
            array_push($columns_data, ['title'=>'メッセージ', 'output'=>true, 'columns'=>[
            ['title'=>'出勤時', 'field'=>'message_in', 'tooltip'=>true, 'output'=>true],
            ['title'=>'退勤時', 'field'=>'message_out', 'tooltip'=>true, 'output'=>true]
            ]]);
        }
        // 非表示
        array_push($columns_data, ['field'=>'work_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'rest_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'over_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'night_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'group1_order', 'output'=>false]);
        array_push($columns_data, ['field'=>'group2_order', 'output'=>false]);
        array_push($columns_data, ['field'=>'group3_order', 'output'=>false]);
        array_push($columns_data, ['field'=>'in_latitude', 'output'=>false]);
        array_push($columns_data, ['field'=>'in_longitude', 'output'=>false]);
        array_push($columns_data, ['field'=>'out_latitude', 'output'=>false]);
        array_push($columns_data, ['field'=>'out_longitude', 'output'=>false]);
        array_push($columns_data, ['field'=>'area_id', 'output'=>false]);
        array_push($columns_data, ['field'=>'normal_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'shift_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'shift_rest', 'output'=>false]);

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    public function list_month()
    {
        function create_date($year, $month, $first_day, $end_day) { // 日付作成用　関数
            $now = new DateTime(); // 現在年月日
            $now_year = $now->format('Y');
            $now_month = $now->format('m');
            $now_day = $now->format('d');
            $week = array("sun", "mon", "tue", "wed", "thu", "fri", "sat");
            for ($day = $first_day; $day <= $end_day; $day++) {
                $datetime = new DateTime();
                $datetime->setDate($year, $month, $day);
                $holiday_datetime = new HolidayDateTime($year.'-'.$month.'-'.$day);
                $week_day = $holiday_datetime->holiday();
                if (!$week_day) {
                    $class = $week[(int)$datetime->format('w')];
                } else {
                    $class = 'holiday';
                }
                if ($year == $now_year && $month == $now_month && $day == $now_day) {
                    $class = 'today';
                }
                $date[] = ['title'=>(string)$day, 'field'=>'day_'.$day, 'width'=>50, 'cssClass'=>$class.' week', 'align'=>'center', 'topCalc'=>'count', 'bottomCalc'=>'sum', 'bottomCalcParams'=>['precision'=>2], 'output'=>true];
            }
            return $date;
        }
        $this->load->helper('holiday_date');
        $end_day = (int)$this->input->post('end_day'); // 締め日
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        if ($end_day !== 0) { // 月末締め日以外の場合
            $pre = new DateTime($year.'-'.$month.'-01');
            $pre->sub(DateInterval::createFromDateString('1 month')); // １ヶ月前
            $pre_year = $pre->format('Y');
            $pre_month = $pre->format('m');
            $pre_month_days = cal_days_in_month(CAL_GREGORIAN, $pre_month, $pre_year);
            $first_day1 = $end_day + 1;
            $end_day1 = $pre_month_days;
            $date1 = create_date($pre_year, $pre_month, $first_day1, $end_day1);
            $first_day2 = 1;
            $end_day2 = $end_day;
            $date2 = create_date($year, $month, $first_day2, $end_day2);
            $date = array_merge($date1, $date2);
        } else { // 月末締めの場合
            $date = create_date($year, $month, 1, $month_days);
        }
        $columns_data = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'frozen'=>true, 'align'=>'center', 'output'=>true],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110, 'frozen'=>true, 'output'=>true]
        ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        $columns_data2 = [
            ['title'=>'勤務日数', 'field'=>'sum', 'align'=>'right', 'width'=>60, 'output'=>true],
            ['title'=>'勤務時間', 'field'=>'time', 'align'=>'right', 'width'=>60, 'output'=>true]
        ];
        $columns_data = array_merge($columns_data, $date);
        $columns_data = array_merge($columns_data, $columns_data2);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    public function list_user()
    {
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        $columns_data = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'frozen'=>true, 'align'=>'center', 'output'=>true],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110, 'frozen'=>true, 'output'=>true]
        ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        array_push($columns_data, ['title'=>'勤務日数', 'field'=>'work_count', 'align'=>'right', 'width'=>50, 'output'=>true]);
        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'労働時間', 'field'=>'work_hour', 'align'=>'right', 'width'=>60, 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'work_minute', 'align'=>'right', 'width'=>60, 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'労働時間 分', 'field'=>'work_minute', 'align'=>'right', 'width'=>75, 'output'=>true]);
        }
        if ((int)$config_data['normal_time_flag'] === 1) {
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'通常時間', 'field'=>'normal_hour', 'align'=>'right', 'width'=>60, 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'normal_minute', 'align'=>'right', 'width'=>60, 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'通常時間 分', 'field'=>'normal_minute', 'align'=>'right', 'width'=>75, 'output'=>true]);
            }
        }
        if ((int)$config_data['over_time_view_flag'] === 1) {
            array_push($columns_data, ['title'=>'残業日数', 'field'=>'over_count', 'align'=>'right', 'width'=>50, 'output'=>true]);
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'残業時間', 'field'=>'over_hour', 'align'=>'right', 'width'=>60, 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'over_minute', 'align'=>'right', 'width'=>60, 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'残業時間 分', 'field'=>'over_minute', 'align'=>'right', 'width'=>75, 'output'=>true]);
            }
        }
        if ((int)$config_data['night_time_view_flag'] === 1) {
            array_push($columns_data, ['title'=>'深夜日数', 'field'=>'night_count', 'align'=>'right', 'width'=>50, 'output'=>true]);
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'深夜時間', 'field'=>'night_hour', 'align'=>'right', 'width'=>60, 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'night_minute', 'align'=>'right', 'width'=>60, 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'深夜時間 分', 'field'=>'night_minute', 'align'=>'right', 'width'=>75, 'output'=>true]);
            }
        }
        array_push($columns_data, ['title'=>'遅刻日数', 'field'=>'late_count', 'align'=>'right', 'width'=>50, 'output'=>true]);
        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'遅刻時間', 'field'=>'late_hour', 'align'=>'right', 'width'=>60, 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'late_minute', 'align'=>'right', 'width'=>60, 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'遅刻時間 分', 'field'=>'late_minute', 'align'=>'right', 'width'=>75, 'output'=>true]);
        }
        array_push($columns_data, ['title'=>'早退日数', 'field'=>'left_count', 'align'=>'right', 'width'=>50, 'output'=>true]);
        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'早退時間', 'field'=>'left_hour', 'align'=>'right', 'width'=>60, 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'left_minute', 'align'=>'right', 'width'=>60, 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'早退時間 分', 'field'=>'left_minute', 'align'=>'right', 'width'=>75, 'output'=>true]);
        }
        array_push($columns_data, ['title'=>'有給日数', 'field'=>'paid_num', 'align'=>'right', 'width'=>50, 'output'=>true]);
        array_push($columns_data, ['title'=>'欠勤日数', 'field'=>'absence_num', 'align'=>'right', 'width'=>50, 'output'=>true]);
        array_push($columns_data, ['title'=>'片打刻', 'field'=>'oneside_num', 'align'=>'right', 'width'=>50, 'output'=>true]);
        array_push($columns_data, ['title'=>'同時刻 0H', 'field'=>'zero_num', 'align'=>'right', 'width'=>50, 'output'=>true]);
        array_push($columns_data, ['field'=>'work_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'over_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'night_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'late_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'left_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'normal_hour2', 'output'=>false]);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    public function lists()
    {
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        $columns_data = [
            ['title'=>'日', 'field'=>'day', 'width'=>30, 'align'=>'center', 'frozen'=>true, 'output'=>true],
            ['title'=>'曜', 'field'=>'week', 'width'=>30, 'align'=>'center', 'frozen'=>true, 'output'=>true],
            ['title'=>'シフト', 'output'=>true, 'columns'=>[
                    ['title'=>'予定', 'field'=>'shift_status', 'width'=>80, 'align'=>'center', 'output'=>true],
                    ['title'=>'出勤予定', 'field'=>'shift_in_time', 'width'=>50, 'align'=>'center', 'output'=>true, 'topCalc'=>'count'],
                    ['title'=>'退勤予定', 'field'=>'shift_out_time', 'width'=>50, 'align'=>'center', 'output'=>true, 'topCalc'=>'count'],
                    ['title'=>'予定時間', 'field'=>'shift_hour', 'width'=>50, 'align'=>'center', 'output'=>true]
                ]
            ]
        ];
        if ((int)$config_data['area_flag'] === 1) {
            array_push($columns_data, ['title'=>'出勤エリア', 'field'=>'area', 'headerFilterPlaceholder'=>'場所', 'headerFilterParams'=>['values'=>@$this->_area_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        array_push($columns_data, ['title'=>'実出時刻', 'field'=>'in_time', 'width'=>50, 'align'=>'center', 'output'=>true]);
        array_push($columns_data, ['title'=>'実退時刻', 'field'=>'out_time', 'width'=>50, 'align'=>'center', 'output'=>true]);
        array_push($columns_data, ['title'=>'出勤時刻', 'field'=>'in_work_time', 'topCalc'=>'count', 'width'=>50, 'align'=>'center', 'output'=>true]);
        array_push($columns_data, ['title'=>'退勤時刻', 'field'=>'out_work_time', 'topCalc'=>'count', 'width'=>50, 'align'=>'center', 'output'=>true]);
        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'総労働時間', 'field'=>'work_hour', 'width'=>60, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'work_minute', 'width'=>50, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'総労働時間 分', 'field'=>'work_minute', 'width'=>75, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['normal_time_flag'] === 1) {
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'通常時間', 'field'=>'normal_hour', 'width'=>60, 'align'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'normal_minute', 'width'=>50, 'align'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'通常時間 分', 'field'=>'normal_minute', 'width'=>75, 'align'=>'right', 'output'=>true]);
            }
        }
        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'休憩時間', 'field'=>'rest_hour', 'width'=>50, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'rest_minute', 'width'=>50, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'休憩時間 分', 'field'=>'rest_minute', 'width'=>65, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['over_time_view_flag'] === 1) {
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'残業時間', 'field'=>'over_hour', 'width'=>50, 'align'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'over_minute', 'width'=>50, 'align'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'残業時間 分', 'field'=>'over_minute', 'width'=>65, 'align'=>'right', 'output'=>true]);
            }
        }
        if ((int)$config_data['night_time_view_flag'] === 1) {
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'深夜時間', 'field'=>'night_hour', 'width'=>50, 'align'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 1) {
                array_push($columns_data, ['title'=>'分', 'field'=>'night_minute', 'width'=>50, 'align'=>'right', 'output'=>true]);
            }
            if ((int)$config_data['minute_time_flag'] === 2) {
                array_push($columns_data, ['title'=>'深夜時間 分', 'field'=>'night_minute', 'width'=>65, 'align'=>'right', 'output'=>true]);
            }
        }
        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'遅刻時間', 'field'=>'late_hour', 'width'=>50, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'late_minute', 'width'=>50, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'遅刻時間 分', 'field'=>'late_minute', 'width'=>65, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'早退時間', 'field'=>'left_hour', 'width'=>50, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 1) {
            array_push($columns_data, ['title'=>'分', 'field'=>'left_minute', 'width'=>50, 'align'=>'right', 'output'=>true]);
        }
        if ((int)$config_data['minute_time_flag'] === 2) {
            array_push($columns_data, ['title'=>'早退時間 分', 'field'=>'left_minute', 'width'=>65, 'align'=>'right', 'output'=>true]);
        }
        array_push($columns_data, ['title'=>'状況', 'field'=>'status', 'width'=>100, 'output'=>true]);
        array_push($columns_data, ['title'=>'メモ', 'field'=>'memo', 'output'=>true]);
        array_push($columns_data, ['field'=>'work_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'rest_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'over_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'night_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'late_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'left_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'today_flag', 'output'=>false]);
        array_push($columns_data, ['field'=>'in_latitude', 'output'=>false]);
        array_push($columns_data, ['field'=>'in_longitude', 'output'=>false]);
        array_push($columns_data, ['field'=>'out_latitude', 'output'=>false]);
        array_push($columns_data, ['field'=>'out_longitude', 'output'=>false]);
        array_push($columns_data, ['field'=>'normal_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'shift_hour2', 'output'=>false]);
        array_push($columns_data, ['field'=>'shift_rest', 'output'=>false]);
        array_push($columns_data, ['field'=>'area_id', 'output'=>false]);
        array_push($columns_data, ['field'=>'date', 'output'=>false]);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    /*
    *  Table Columns 従業員一覧
    */
    public function users()
    {
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        $columns_data = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'frozen'=>true, 'align'=>'center', 'output'=>true],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110, 'frozen'=>true, 'output'=>true],
            ['title'=>'フリガナ', 'field'=>'user_kana', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'フリガナ検索', 'width'=>110, 'frozen'=>true, 'output'=>true],
            ['title'=>'性別', 'field'=>'sex', 'width'=>1, 'align'=>'center', 'frozen'=>true, 'output'=>true]
        ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100, 'output'=>true]);
        }
        if ((int)$config_data['mypage_flag'] !== 0) {
            array_push($columns_data, ['title'=>'通知', 'field'=>'notice', 'align'=>'center', 'width'=>50, 'output'=>true]);
        }
        if ($config_data['mypage_self_edit_flag'] == 1) {
            array_push($columns_data, ['title'=>'自己修正', 'field'=>'mypage_self', 'formatter'=>'tickCross', 'align'=>'center', 'width'=>50, 'output'=>true, 'editor'=>true, 'topCalc'=>'count']);
        }
        if ((int)$config_data['mypage_shift_alert'] !== 0) {
            array_push($columns_data, ['title'=>'シフト警告', 'field'=>'mypage_shift_alert', 'align'=>'center', 'width'=>60, 'output'=>true]);
        }
        // if ($config_data['area_flag'] == 1) {
        //     $this->load->database();
        //     $result = $this->db->query("SELECT id, area_name FROM area_data")->result();
        //     $area_name = array_column($result, 'area_name');
        //     array_unshift($area_name, '');
        //     array_push($columns_data, ['title'=>'エリア', 'field'=>'area_id', 'align'=>'center', 'width'=>100, 'editor'=>'select', 'editorParams'=>['values'=>$area_name]]);
        // }
        array_push($columns_data, ['title'=>'入社日', 'field'=>'entry_date', 'align'=>'center', 'width'=>100, 'output'=>true]);
        array_push($columns_data, ['title'=>'退職日', 'field'=>'resign_date', 'align'=>'center', 'width'=>100, 'output'=>true]);
        array_push($columns_data, ['title'=>'勤務年月', 'field'=>'interval', 'align'=>'right', 'width'=>80, 'output'=>true]);
        array_push($columns_data, ['title'=>'誕生日', 'field'=>'birth_day', 'align'=>'center', 'width'=>100, 'output'=>true]);
        array_push($columns_data, ['title'=>'年齢', 'field'=>'old', 'align'=>'center', 'width'=>1, 'output'=>true]);
        array_push($columns_data, ['title'=>'電話番号1', 'field'=>'phone_number1', 'width'=>90, 'output'=>true]);
        array_push($columns_data, ['title'=>'電話番号2', 'field'=>'phone_number2', 'width'=>90, 'output'=>true]);
        array_push($columns_data, ['title'=>'メールアドレス1', 'field'=>'email1', 'width'=>130, 'output'=>true]);
        array_push($columns_data, ['title'=>'メールアドレス2', 'field'=>'email2', 'width'=>130, 'output'=>true]);
        array_push($columns_data, ['title'=>'〒', 'field'=>'zip_code', 'width'=>60, 'output'=>true]);
        array_push($columns_data, ['title'=>'住所', 'field'=>'address', 'width'=>300, 'output'=>true]);
        array_push($columns_data, ['title'=>'メモ', 'field'=>'memo', 'output'=>true]);
        array_push($columns_data, ['title'=>'タイプ', 'field'=>'management_flag', 'output'=>true]);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    /*
    *  Table Columns 給与管理
    */
    public function pay()
    {
        $columns_data = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'frozen'=>true, 'align'=>'center'],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110, 'frozen'=>true]
        ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        array_push($columns_data, ['title'=>'入社日', 'field'=>'entry_date', 'align'=>'right', 'width'=>100]);
        array_push($columns_data, ['title'=>'勤務年月', 'field'=>'interval', 'align'=>'right', 'width'=>80]);
        array_push($columns_data, ['title'=>'起算年月', 'field'=>'start_month', 'align'=>'center', 'width'=>80]);
        array_push($columns_data, ['title'=>'有給付与月', 'field'=>'paid_month', 'align'=>'center', 'width'=>80]);
        array_push($columns_data, ['title'=>'有給付与', 'field'=>'put_paid', 'align'=>'center', 'width'=>80]);
        array_push($columns_data, ['title'=>'当月有給消化', 'field'=>'paid', 'align'=>'center', 'width'=>80]);
        array_push($columns_data, ['title'=>'有給残日数', 'field'=>'total_paid', 'align'=>'center', 'width'=>80]);
        array_push($columns_data, ['title'=>'勤務数', 'field'=>'work_num', 'align'=>'right', 'width'=>80]);
        array_push($columns_data, ['title'=>'累計勤務数', 'field'=>'total_work_num', 'align'=>'right', 'width'=>80]);
        array_push($columns_data, ['title'=>'勤務時間', 'field'=>'work_time', 'align'=>'right', 'width'=>80]);
        array_push($columns_data, ['title'=>'累計勤務時間', 'field'=>'total_work_time', 'align'=>'right', 'width'=>80]);
        array_push($columns_data, ['field'=>'work_hour', 'output'=>false]);
        array_push($columns_data, ['field'=>'total_work_hour', 'output'=>false]);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    public function user_detail_notice()
    {
        $columns_data = [
            ['title'=>'通知', 'field'=>'notice', 'formatter'=>'tickCross', 'width'=>50, 'align'=>'center', 'editor'=>true, 'topCalc'=>'count'],
            ['title'=>'承認権限', 'field'=>'permit', 'formatter'=>'tickCross', 'align'=>'center', 'width'=>50, 'editor'=>true, 'topCalc'=>'count'],
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'align'=>'center'],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110]
        ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    public function shift_users()
    {
        $columns_data = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'align'=>'center'],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110]
        ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        array_push($columns_data, ['field'=>'shift', 'output'=>false]);
        array_push($columns_data, ['field'=>'register', 'output'=>false]);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    public function shift_main()
    {
        $columns_data = [
            ['title'=>'日', 'field'=>'day', 'align'=>'center', 'frozen'=>true, 'width'=>25, 'minWidth'=>25],
            ['title'=>'曜', 'field'=>'week', 'align'=>'center', 'frozen'=>true, 'width'=>25, 'minWidth'=>25],
            ['title'=>'予定', 'field'=>'status', 'align'=>'center', 'width'=>50],
            ['title'=>'出勤時刻', 'field'=>'in_time', 'align'=>'center', 'width'=>50],
            ['title'=>'退勤時刻', 'field'=>'out_time', 'align'=>'center', 'width'=>50],
            ['title'=>'休憩', 'field'=>'rest', 'align'=>'center', 'width'=>50],
            ['title'=>'時間', 'field'=>'hour', 'align'=>'center', 'width'=>50],
            // ['title'=>'勤務時間', 'field'=>'hour', 'align'=>'center', 'width'=>50],
            // ['title'=>'休憩時間', 'field'=>'rest', 'align'=>'center', 'width'=>50]
        ];

        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        for ($hour = (int)$config_data['shift_first_view_hour']; $hour <= (int)$config_data['shift_end_view_hour']; $hour++) {
            array_push($columns_data, [
                'title'=>$hour.'時', 'width'=>40, 'columns'=>[
                    ['title'=>'', 'field'=>$hour.'_0', 'formatter'=>'color', 'width'=>20, 'minWidth'=>20],
                    ['title'=>'', 'field'=>$hour.'_30', 'formatter'=>'color', 'width'=>20, 'minWidth'=>20]
                ]
            ]);
        }
        array_push($columns_data, ['field'=>'date', 'output'=>false]);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    public function to()
    {
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        $columns_data = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'align'=>'center'],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110]
            ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if ((int)$config_data['aporan_flag'] !== 0) {
            array_push($columns_data, ['title'=>'アポラン配信', 'field'=>'aporan', 'formatter'=>'tickCross', 'align'=>'center', 'width'=>80, 'editor'=>true, 'topCalc'=>'count']);
        }
        if ((int)$config_data['advance_pay_flag'] !== 0) {
            array_push($columns_data, ['title'=>'前払い配信', 'field'=>'advance_pay', 'formatter'=>'tickCross', 'align'=>'center', 'width'=>80, 'editor'=>true, 'topCalc'=>'count']);
        }
        if ((int)$config_data['esna_pay_flag'] !== 0) {
            array_push($columns_data, ['title'=>'時給システム', 'field'=>'esna_pay_flag', 'formatter'=>'tickCross', 'align'=>'center', 'width'=>80, 'editor'=>true, 'topCalc'=>'count']);
        }
        if ((int)$config_data['user_api_output_flag'] !== 0) {
            array_push($columns_data, ['title'=>'API連動', 'field'=>'user_api_output_flag', 'formatter'=>'tickCross', 'align'=>'center', 'width'=>80, 'editor'=>true, 'topCalc'=>'count']);
        }
        array_push($columns_data, ['field'=>'state', 'output'=>false]);

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }

    public function list_weekly()
    {
        $columns_data = [
            ['title'=>'ID', 'field'=>'user_id', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'IDを検索', 'width'=>60, 'align'=>'center'],
            ['title'=>'名前', 'field'=>'user_name', 'headerFilter'=>'input', 'headerFilterPlaceholder'=>'名前を検索', 'topCalc'=>'count', 'width'=>110]
        ];
        if (isset($this->_group_title[1])) {
            array_push($columns_data, ['title'=>@$this->_group_title[1] ?: '', 'field'=>'group1_name', 'headerFilterPlaceholder'=>@$this->_group_title[1] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group1_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[2])) {
            array_push($columns_data, ['title'=>@$this->_group_title[2] ?: '', 'field'=>'group2_name', 'headerFilterPlaceholder'=>@$this->_group_title[2] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group2_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        if (isset($this->_group_title[3])) {
            array_push($columns_data, ['title'=>@$this->_group_title[3] ?: '', 'field'=>'group3_name', 'headerFilterPlaceholder'=>@$this->_group_title[3] ?: ''.'を検索', 'headerFilterParams'=>['values'=>@$this->_group3_name ?: ''], 'headerFilter'=>'select', 'width'=>100]);
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($columns_data));
    }
}
