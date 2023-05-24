<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Main extends CI_Controller
{
    public function index()
    {

        // セッションチェック
        if ($this->session->is_logged_id) {
            redirect('/');
        }

        // ログインチェック
        $this->load->library('form_validation'); // バリデーションライブラリ
        $this->form_validation->set_rules('login_id', 'ログインID', ['required', 'trim']);
        $this->form_validation->set_rules('password', 'パスワード', ['required', 'trim', 'md5', [
            'login_cheak',
            function () {
                $this->load->model('model_login');
                if ($this->model_login->check_login()) {
                    return true;
                } else {
                    return false;
                }
            }
        ]]);

        if ($this->form_validation->run()) {
            redirect('/');
        } else {
            /**
             * login page 表示
             */
            
            $this->load->helper('cookie');
            delete_cookie('dk_session');
            // config data取得
            $this->load->model('model_config_values');
            $where = [];
            $result = $this->model_config_values->find('id, config_name, value', $where, '');
            $data = array_column($result, null, 'config_name');
            
            // layout data取得
            $this->load->model('model_layout');
            $data['site_title'] = $this->model_layout->get_data()->site_title; // サイトのタイトル名
            $data['logo_uri_login'] = $this->model_layout->get_data()->logo_uri_login; // ロゴURI
            
            // ユーザエージェント
            $this->load->library('user_agent');
            $data['browser'] = $this->agent->browser();
            $data['version'] = $this->agent->version();
            $data['mobile'] = $this->agent->mobile();
            $data['platform'] = $this->agent->platform();
            
            // 表示用データ
            $data['page_id'] = 'login';
            $data['page_title'] = 'ログイン';
            
            // view
            $this->output->set_header('X-FRAME-OPTIONS: DENY');
            $this->output->set_header('X-Content-Type-Options: nosniff');
            $this->load->view('login_view', $data);
        }
    }
}
