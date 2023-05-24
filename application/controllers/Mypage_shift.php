<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Mypage_shift extends CI_Controller
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

            // 下位ユーザ有無
            $this->load->model('model_notice_data_bk');
            $auth_data = $this->model_notice_data_bk->gets_auth($data['user_id']);
            if ($auth_data) {
                $data['low_user'] = 1;
            } else {
                $data['low_user'] = 0;
            }

            // ログインユーザー名
            $data['login_name'] = 'マイページ';
            $data['mypage_title'] = 'シフト管理';

            // 表示用データ
            $data['page_id'] = 'mypage_shift';
            $data['page_title'] = $this->session->user_name;
            $data['company_name'] = $this->session->user_name;

            // view 
            $this->load->view('mypage/shift_view', $data);
        } else {
            redirect('/');
        }
    }
}