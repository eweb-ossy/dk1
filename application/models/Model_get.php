<?php 
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_get extends CI_Model
{
    public function __construct()
    {
        parent:: __construct();
        $this->load->database();
        $this->load->model('model_get');
    }

    // 日付チェック用 
    public function dateCheck($date) {
        if (strptime($date, '%Y-%m-%d')) {
            list($year, $month, $day) = explode('-', $date);
            if (checkdate($month, $day, $year)) return $date;
        }
        return false;
    }

    /**
     * グループタイトルを返す
     */
    public function group_title() {
        $result = $this->db->query('SELECT group_id, title FROM group_title')->result();
        return array_column($result, 'title', 'group_id');
    }

    /**
     * グループデータを返す　
     */
    public function group_data($state = '') {
        $where = "";
        if ($state == 1) $where = "WHERE state = 1";
        if ($state == 2) $where = "WHERE state = 2";
        $group_title = $this->model_get->group_title();
        for ($i = 1; $i <= 3; $i++) {
            if (isset($group_title[$i])) {
                $data[$i] = [
                    'title' => $group_title[$i],
                    'data' => $this->db->query("SELECT id, group_name, state, group_order FROM user_groups{$i} {$where} ORDER BY group_order ASC")->result()
                ];
            }
        }
        return $data;
    }

    /**
     *  グループ履歴データを返す 
     */
    public function group_history_data($user_id = '') {
        $where = $user_id ? "WHERE user_id = {$user_id}" : "";
        return $this->db->query("SELECT * FROM group_history {$where} ORDER BY to_date DESC")->result();
    }

    /**
     *  従業員のグループデータを返す
     *  $option 'user_id', 'state_date'
     */
    public function user_group_id($option = []) {
        $where_user_id = isset($option['user_id']) ? "AND user_id = '{$option['user_id']}'" : "";
        $where_date = "";
        if (isset($option['state_date'])) {
            if ($this->model_get->dateCheck($option['date'])) {
                $date = new DateTime($this->model_get->dateCheck($option['date']));
                $where_date = "WHERE to_date <= '{$date->format('Y-m-d')}'";
            }
        }
        $user_group_data = $this->db->query("SELECT * FROM group_history WHERE id in (SELECT max(id) FROM group_history {$where_date} GROUP BY `user_id` ORDER BY to_date DESC) {$where_user_id} ORDER BY `user_id`")->result();

        $group_data = $this->model_get->group_data(1);
        for ($i=1; $i<=3; $i++) {
            $group[$i] = array_column($group_data[$i]['data'], 'group_name', 'id');
        }

        foreach ($user_group_data as $key => $value) {
            $user_group_data[$key]->group1_name = isset($group[1][$value->group1_id]) ? $group[1][$value->group1_id] : "";
            $user_group_data[$key]->group2_name = isset($group[2][$value->group2_id]) ? $group[2][$value->group2_id] : "";
            $user_group_data[$key]->group3_name = isset($group[3][$value->group3_id]) ? $group[3][$value->group3_id] : "";
        }
        return $user_group_data;
    }

    /**
     *  従業員データを返す
     *  $option 'user_id', 'state', 'state_date' 
     */
    public function user_data($option = []) {
        $where = "";
        $wheres = [];
        $groupOption = [];
        if (isset($option['user_id'])) {
            $wheres[] = "user_id = {$option['user_id']}";
            $groupOption['user_id'] = $option['user_id'];
        }
        if (isset($option['state']) && !isset($option['state_date'])) {
            if ($option['state'] == 1) $wheres[] = "state = 1";
            if ($option['state'] == 2) $wheres[] = "state = 2";
        }
        if (isset($option['state_date'])) {
            $date = new DateTime($this->model_get->dateCheck($option['state_date']));
            $wheres[] = "( entry_date <= '{$date->format('Y-m-d')}' OR entry_date IS NULL ) AND ( resign_date >= '{$date->format('Y-m-d')}' OR resign_date IS NULL )";
            $groupOption['date'] = $date->format('Y-m-d');
        }

        if ($wheres) {
            $where = "WHERE " . implode(" AND ", $wheres);
        }

        $fields = "id, CONCAT(name_sei, ' ', name_mei) AS mame, name_sei, name_mei, CONCAT(kana_sei, ' ', kana_mei) AS kana, kana_sei, kana_mei, `user_id`, `state`, entry_date, resign_date, birth_date, zip_code, address, sex, memo, phone_number1, phone_number2, email1, email2, put_paid_vacation_month, aporan_flag, authority_id, advance_pay_flag, notice_mail_flag, password, in_time_pat, out_time_pat, start_month, idm, shift_alert_flag, management_flag, esna_pay_flag, api_output, mypage_self";
        // $fields = "*";

        $user_data = $this->db->query("SELECT {$fields} FROM user_data {$where}")->result();

        $user_group_id = array_column($this->model_get->user_group_id($groupOption), NULL, 'user_id');
       
        foreach ($user_data as $key => $value) {
            $user_data[$key]->group = isset($user_group_id[$value->user_id]) ? $user_group_id[$value->user_id] : [];
        }
        return $user_data;
    }



    /**
     * 最新の勤怠データを取得
     *   LIMIT 5000
     */
    public function times_data($option = []) {
        $where = "";
        $wheres = [];
        if (isset($option['user_id'])) {
            if (is_array($option['user_id']) && isset($option['user_id'])) {
                $wheres[] = "user_id IN ('".implode(',', $option['user_id'])."')";
            } elseif (isset($option['user_id'])) {
                $wheres[] = "user_id = {$option['user_id']}";
            }
        }
        if (isset($option['start_date'])) {
            $start_date = new DateTime($this->model_get->dateCheck($option['start_date']));
        }
        if (isset($option['end_date'])) {
            $end_date = new DateTime($this->model_get->dateCheck($option['end_date']));
        }
        if (isset($start_date) && isset($end_date)) {
            $wheres[] = "dk_date BETWEEN '{$start_date->format('Y-m-d')}' AND '{$end_date->format('Y-m-d')}'";
        }
        if (isset($start_date) && empty($end_date)) {
            $wheres[] = "dk_date BETWEEN '{$start_date->format('Y-m-d')}' AND '{$start_date->format('Y-m-d')}'";
        }
        if ($wheres) {
            $where = "WHERE " .implode(" AND ", $wheres);
        }
        $fields = "id, dk_date, user_id, in_time, substring(in_time, 1, 5) AS in_time_view, out_time, substring(out_time, 1, 5) AS out_time_view, in_work_time, substring(in_work_time, 1, 5) AS in_work_time_view, out_work_time, substring(out_work_time, 1, 5) AS out_work_time_view, rest, revision, in_flag, out_flag, fact_hour, fact_work_hour, status, status_flag, over_hour, night_hour, left_hour, late_hour, memo, area_id, revision_user, revision_datetime, notice_memo, revision_in, revision_out";
        // $fields = "*";
        return $this->db->query("SELECT {$fields} FROM time_data {$where} ORDER BY dk_date DESC LIMIT 5000")->result();
    }

}