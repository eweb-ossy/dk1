<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Mypage_pay extends CI_Controller
{
    public function index()
    {
        if ($this->session->authority === 0) {
            // config data取得
            $this->load->database();
            $result = $this->db->query("SELECT `id`, `config_name`, `type`, `value` FROM `config_values`")->result();
            $data = array_column($result, null, 'config_name');

            // セッションデータ
            $data['user_id'] = $this->session->user_id;
            $data['user_name'] = $this->session->user_name;

            // layout data取得
            $row = $this->db->query("SELECT `site_title`, `logo_uri_header` FROM `layout`")->row();
            $data['site_title'] = $row->site_title;
            $data['logo_uri_header'] = $row->logo_uri_header;

            // 下位ユーザ有無
            // $this->load->model('model_notice_data_bk');
            // $auth_data = $this->model_notice_data_bk->gets_auth($data['user_id']);
            // if ($auth_data) {
            //     $data['low_user'] = 1;
            // } else {
            //     $data['low_user'] = 0;
            // }

            $result = $this->db->query("SELECT `low_user_id` FROM `notice_auth` WHERE `user_id` = {$data['user_id']}")->result();
            $data['low_user'] = $result;

            // ログインユーザー名
            $data['login_name'] = 'マイページ';
            $data['mypage_title'] = '給与明細';

            // 表示用データ
            $data['page_id'] = 'mypage_pay';
            $data['page_title'] = $this->session->user_name;
            $data['company_name'] = $this->session->user_name;

            // view 
            $this->load->view('mypage/pay_view', $data);
        } else {
            redirect('/');
        }
    }
}