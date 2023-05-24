<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_list_month extends MY_Controller
{
    public function index()
    {
        // セッションチェック
        if ((int)$this->session->authority > 1) {

            // config data取得
            $data = $this->data['configs'];

            // layout data取得
            $query = $this->db->query('SELECT site_title, logo_uri_header FROM layout');
            $row = $query->row();
            $data['site_title'] = $row->site_title;
            $data['logo_uri_header'] = $row->logo_uri_header;

            // ログインユーザー名
            $data['login_name'] = $this->session->user_name;

            // 表示用データ
            $data['page_id'] = strtolower(get_class($this)); // class名を、ページIDにする（小文字）
            $data['page_title'] = '管理画面 - 月別集計';

            // view
            $this->load->view('admin/list_month_view', $data);
        } else {
            redirect('/');
        }
    }
}
