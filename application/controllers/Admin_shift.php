<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_shift extends MY_Controller
{
    public function index()
    {
        // セッションチェック
        if ((int)$this->session->authority > 1) {

            //config data
            $data = $this->data['configs'];

            // layout data取得
            $query = $this->db->query('SELECT site_title, logo_uri_header FROM layout');
            $row = $query->row();
            $data['site_title'] = $row->site_title;
            $data['logo_uri_header'] = $row->logo_uri_header;

            // ログインユーザー名
            $data['login_name'] = $this->session->user_name;

            // グループタイトル
            $data['group_title'] = $this->db->query('SELECT id, group_id, title FROM group_title')->result();

            // group
            for ($i = 1; $i <= 3; $i++) { 
                $data['group'][$i] = $this->db->query("SELECT id, group_name, state, group_order FROM user_groups{$i} ORDER BY group_order ASC")->result();
                $data['group_max'][$i] = $data['group'][$i] ? max(array_column($data['group'][$i], 'id')) : '';
            }

            // 表示用データ
            $data['page_id'] = strtolower(get_class($this)); // class名を、ページIDにする（小文字）
            $data['page_title'] = '管理画面 - シフト管理';

            // view
            $this->load->view('admin/shift_view', $data);
        } else {
            redirect('/');
        }
    }
}
