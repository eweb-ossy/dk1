<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_list_weekly extends CI_Controller
{
    public function index()
    {
        // セッションチェック
        if ((int)$this->session->authority > 1) {
            // config data取得
            $this->load->model('model_config_values');
            $where = [];
            $result = $this->model_config_values->find('id, config_name, value', $where, '');
            $data = array_column($result, null, 'config_name');

            // layout data取得
            $this->load->model('model_layout');
            $data['site_title'] = $this->model_layout->get_data()->site_title; // サイトのタイトル名
            $data['logo_uri_header'] = $this->model_layout->get_data()->logo_uri_header; // ヘッダー部ロゴURI

            // ログインユーザー名
            $data['login_name'] = $this->session->user_name;

            // company_rules data取得
            $this->load->model('model_company_rules');
            $where = [];
            $result = $this->model_company_rules->find('id, rule, value', $where, '');
            $data['company_data'] = array_column($result, null, 'rule');

            // 表示用データ
            $data['page_id'] = strtolower(get_class($this)); // class名を、ページIDにする（小文字）
            $data['page_title'] = '管理画面 - 週別集計';

            // view
            $this->load->view('admin/list_weekly_view', $data);
        } else {
            redirect('/');
        }
    }
}