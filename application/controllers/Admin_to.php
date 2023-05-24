<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_to extends CI_Controller
{
    public function index()
    {
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

            // 表示用データ
            $data['page_id'] = strtolower(get_class($this)); // class名を、ページIDにする（小文字）
            $data['page_title'] = '管理画面 - 配信管理';

            // view
            $this->load->view('admin/to_view', $data);
        } else {
            redirect('/');
        }
    }
}