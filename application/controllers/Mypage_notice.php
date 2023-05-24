<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Mypage_notice extends CI_Controller
{
    public function index()
    {
        if ($this->session->authority === 0) {
            // config data取得
            $this->load->model('model_config_values');
            $where = [];
            $result = $this->model_config_values->find('id, config_name, value', $where, '');
            $data = array_column($result, null, 'config_name');

            // セッションデータ
            $data['user_id'] = $this->session->user_id;
            $data['user_name'] = $this->session->user_name;

            // layout data取得
            $this->load->model('model_layout');
            $data['site_title'] = $this->model_layout->get_data()->site_title; // サイトのタイトル名
            $data['logo_uri_header'] = $this->model_layout->get_data()->logo_uri_header; // ヘッダー部ロゴURI

            // ログインユーザー名
            $data['login_name'] = 'マイページ';
            $data['mypage_title'] = '通知';

            // 表示用データ
            $data['page_id'] = 'mypage_notice';
            $data['page_title'] = $this->session->user_name;
            $data['company_name'] = $this->session->user_name;

            // view 
            $this->load->view('mypage/notice_view', $data);
        } else {
            redirect('/');
        }
    }
}
