<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_users extends MY_Controller
{
    public function getData() // 出力するデータを、テーブルカラム＋従業員データとする
    {
        $this->load->database();

        $result = $this->db->query('SELECT group_id, title FROM group_title')->result();
        $group_title = array_column($result, 'title', 'group_id');
        unset($result);

        $columns = [
            ['title'=> 'ID', 'field'=> 'user_id', 'headerFilter'=> 'input', 'frozen'=> true],
            ['title'=> '名前', 'field'=> 'user_name', 'headerFilter'=> 'input', 'frozen'=> true],
            ['title'=> 'フリガナ', 'field'=> 'user_kana', 'headerFilter'=> 'input', 'frozen'=> true],
            ['title'=> '性別', 'field'=> 'sex', 'hozAlign'=> 'center', 'headerFilter'=> 'list', 'headerFilterParams'=> ['valuesLookup'=> true, 'clearable'=> true]],
        ];
        for ($i=1; $i<=3; $i++) {
            if (isset($group_title[$i])) {
                array_push($columns, ['title'=> $group_title[$i], 'field'=> "group{$i}_name", 'headerFilter'=> 'list', 'headerFilterParams'=> ['valuesLookup'=> true, 'clearable'=> false]]);
            }
        }
        unset($i);
        if ($this->data['configs']['mypage_flag']->value === '1') {
            array_push($columns, ['title'=> '通知', 'field'=> 'notice', 'hozAlign'=> 'center']);
            $notice = $this->db->query("SELECT `user_id`, low_user_id FROM notice_auth")->result();
        }
        if ($this->data['configs']['mypage_self_edit_flag']->value === '1') {
            array_push($columns, ['title'=> '自己修正', 'field'=> 'mypage_self', 'hozAlign'=> 'center', 'editor'=> 'tickCross', 'editorParams'=> ['trueValue'=> "可能", 'falseValue'=>""]]);
        }
        if ($this->data['configs']['mypage_shift_alert']->value === '1') {
            array_push($columns, ['title'=> 'シフト警告', 'field'=> 'shift_alert_flag', 'hozAlign'=> 'center', 'editor'=> 'tickCross', 'editorParams'=> ['trueValue'=> "する", 'falseValue'=>""]]);
        }
        array_push($columns, ['title'=> '入社日', 'field'=> 'entry_date_view']);
        array_push($columns, ['title'=> '退職日', 'field'=> 'resign_date_view', 'visible'=> false]);
        array_push($columns, ['title'=> '勤務年月', 'field'=> 'interval', 'hozAlign'=> 'right']);
        array_push($columns, ['title'=> '誕生日', 'field'=> 'birth_date_view']);
        array_push($columns, ['title'=> '年齢', 'field'=> 'old', 'hozAlign'=> 'right']);
        array_push($columns, ['title'=> '電話番号1', 'field'=> 'phone_number1']);
        array_push($columns, ['title'=> '電話番号2', 'field'=> 'phone_number2']);
        array_push($columns, ['title'=> 'メールアドレス1', 'field'=> 'email1']);
        array_push($columns, ['title'=> 'メールアドレス2', 'field'=> 'email2']);
        array_push($columns, ['title'=> '〒', 'field'=> 'zip_code']);
        array_push($columns, ['title'=> '住所', 'field'=> 'address']);
        array_push($columns, ['title'=> 'メモ', 'field'=> 'memo']);
        array_push($columns, ['title'=> 'タイプ', 'field'=> 'management_flag', 'visible'=> false]);
        
        for ($i=1; $i<=3; $i++) { 
            $result = $this->db->query("SELECT id, group_name FROM user_groups{$i} ORDER BY group_order ASC")->result();
            $group[$i] = array_column($result, 'group_name', 'id');
            unset($result);
        }

        $result = $this->db->query("SELECT `user_id`, group1_id as `1`, group2_id as `2`, group3_id as `3` FROM group_history ORDER BY to_date ASC")->result_array();
        $group_history = array_column($result, NULL, 'user_id');
        unset($result);

        $users = [];
        $result = $this->db->query("SELECT CONCAT(name_sei, ' ', name_mei) AS `user_name`, CONCAT(kana_sei, ' ', kana_mei) AS user_kana, `user_id`, CASE WHEN sex=1 THEN '男' WHEN sex=2 THEN '女' ELSE '' END AS sex, `state`, entry_date, DATE_FORMAT(entry_date, '%Y年%m月%d日') AS entry_date_view, resign_date, DATE_FORMAT(resign_date, '%Y年%m月%d日') AS resign_date_view, birth_date, DATE_FORMAT(birth_date, '%Y年%m月%d日') AS birth_date_view, zip_code, `address`, memo, phone_number1, phone_number2, email1, email2, CASE WHEN shift_alert_flag=0 THEN 'する' ELSE '' END AS shift_alert_flag, CASE WHEN management_flag=1 THEN '管理のみ' ELSE '' END AS management_flag, CASE WHEN mypage_self=1 THEN '可能' ELSE '' END AS mypage_self FROM user_data ORDER BY `user_id` ASC")->result_array();
        foreach ($result as $key => $value) {
            if ($value['entry_date']) {
                $entry = new Datetime($value['entry_date']);
                $resign = new Datetime($value['resign_date']);
                $diff = $entry->diff($resign);
                $diff_year = $diff->format('%y') ? $diff->format('%y').'年' : '';
                $diff_month = $diff->format('%m') ? $diff->format('%m').'ヶ月' : '';
                $diff_view = $diff_year.$diff_month;
                unset($entry, $resign, $diff, $diff_year, $diff_month);
            }
            if ($value['birth_date']) {
                $birth_date = new Datetime($value['birth_date']);
                $old = floor((date('Ymd') - $birth_date->format('Ymd'))/10000);
                unset($birth_date);
            }
            $users[$key] = $result[$key] + [
                "group1_name"=> @$group_history[$value['user_id']][1] ? $group[1][$group_history[$value['user_id']][1]] : '',
                "group2_name"=> @$group_history[$value['user_id']][2] ? $group[2][$group_history[$value['user_id']][2]] : '',
                "group3_name"=> @$group_history[$value['user_id']][3] ? $group[3][$group_history[$value['user_id']][3]] : '',
                'interval'=> @$diff_view ?: '',
                'old'=> @$old ?: '',
                'notice'=> @array_count_values(array_column($notice, 'user_id'))[$value['user_id']] ?: ''
            ];
            unset($diff_view, $old);
        }
        unset($result);

        $data = [
            'columns'=> $columns,
            'users'=> $users ?: []
        ];

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    // 
    public function dataSave()
    {
        $this->load->database();
        $user_id = $this->input->post('user_id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        if ($field === 'mypage_self') {
            $value = $value === '可能' ? 1 : 0;
            $message = '自己修正変更';
        }
        if ($field === 'shift_alert_flag') {
            $value = $value === 'する' ? 0 : 1;
            $message = 'シフト警告変更';
        }
        $this->db->set($field, $value);
        $this->db->where('user_id', (int)$user_id);
        if (!$this->db->update('user_data')) {
            $message = 'error';
        }
        $this->output
        ->set_content_type('application/text')
        ->set_output($message);
    }
}
