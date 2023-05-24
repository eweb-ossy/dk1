<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_conf extends MY_Controller
{
    public function index()
    {
        // セッションチェック
        if ((int)$this->session->authority === 4) {
            // config data取得
            $data = $this->data['configs'];

            // message data
            $result = $this->db->query('SELECT id, `type`, flag, title, detail FROM message_title_data')->result();
            $data['message'] = $result ? array_column($result, NULL, 'type') : [];

            // login data取得
            $data['login_data'] = $this->db->query('SELECT id, login_id, user_name, authority, area_id FROM login_data WHERE id NOT IN(99, 100)')->result();
            $authority = ['', '一般', '回覧者', '編集者', '管理者', 'シフト管理者'];
            foreach ($data['login_data'] as $key => $value) {
                $data['login_data'][$key]->authority_name = $authority[$value->authority];
            }

            // group title 取得
            $data['group_title'] = $this->db->query('SELECT id, group_id, title FROM group_title')->result();

            // group
            for ($i = 1; $i <= 3; $i++) { 
                $data['group'][$i] = $this->db->query("SELECT id, group_name, state, group_order FROM user_groups{$i} ORDER BY group_order ASC")->result();
                $data['group_max'][$i] = $data['group'][$i] ? max(array_column($data['group'][$i], 'id')) : '';
            }

            // area data
            $data['area_data'] = $this->db->query('SELECT id, area_name, host_ip FROM area_data')->result();

            // ログインユーザー名
            $data['login_name'] = $this->session->user_name;
            $data['login_id'] = $this->session->login_id;

            // layout data取得
            $row = $this->db->query('SELECT site_title, logo_uri_header FROM layout')->row();
            $data['site_title'] = $row->site_title;
            $data['logo_uri_header'] = $row->logo_uri_header;

            // notice data取得
            $data['notice_status_data'] = $this->db->query('SELECT id, notice_status_id, notice_status_title, `status`, `group`, `order` FROM notice_status_data ORDER BY `order` ASC')->result();

            // config_rules data取得
            $data['rules_data'] = $this->db->query('SELECT id, `user_id`, group_id, group_no, all_flag, in_marume_flag, in_marume_hour, in_marume_time, out_marume_flag, out_marume_hour, out_marume_time, basic_in_time, basic_out_time, basic_rest_weekday, rest_rule_flag, over_limit_hour, title, `status`, `order`, summary FROM config_rules ORDER BY `order` ASC')->result();

            // rest_rules data取得
            $result = $this->db->query('SELECT id, config_rules_id, rest_time, rest_type, limit_work_hour, rest_in_time, rest_out_time FROM rest_rules')->result();
            $data['rest_rules_data'] = array_column($result, null, "config_rules_id");

            // user name list data 取得
            $data['user_name_list'] = $this->db->query('SELECT id, `user_id`, name_sei, name_mei FROM user_data WHERE state = 1')->result();

            // company_rules data取得
            $result = $this->db->query('SELECT id, rule, `value` FROM company_rules')->result();
            $data['company_data'] = array_column($result, null, 'rule');

            // 表示用データ
            $data['page_id'] = strtolower(get_class($this)); // class名を、ページIDにする（小文字）
            $data['page_title'] = '管理画面 - 各種設定';

            // view
            $this->load->view('admin/conf_view', $data);
        } else {
            redirect('/');
        }
    }
}
