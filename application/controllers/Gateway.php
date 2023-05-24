<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gateway extends CI_Controller
{
    public function index()
    {
        if ($this->session->authority == 1) {

            $this->load->database();

            $data = [];

            // config data取得
            $result = $this->db->query('SELECT id, config_name, value FROM config_values')->result();
            $data = array_column($result, null, 'config_name');

            // message data取得
            $result = $this->db->query('SELECT id, type, flag, title, detail FROM message_title_data')->result();
            foreach ($result as $value) {
                $data['message'][$value->type] = [
                    'id' => $value->id,
                    'flag' => $value->flag,
                    'title' => $value->title,
                    'detail' => $value->detail
                ];
            }

            // layout data取得
            $row = $this->db->query('SELECT site_title, logo_uri_header FROM layout')->row();
            $data['site_title'] = $row->site_title;
            $data['logo_uri_header'] = $row->logo_uri_header;

            // 申請タイトルdata取得 notice_status_data
            $data['notice_status_data'] = $this->db->query('SELECT * FROM notice_status_data')->result();

            
            $data['login_name'] = $this->session->user_name; // ログインユーザー名
            $data['agent'] = $this->session->agent; // agent 

            // エリア
            if ($data['area_flag']->value == 1) {
                $data['area_name'] = $this->db->query('SELECT area_name FROM area_data WHERE id = '.(int)$this->session->area_id)->row()->area_name;
            }

            // 表示用データ
            $data['page_id'] = 'gateway';
            $data['page_title'] = '出退勤入力';

            // view
            $this->load->view('gateway/main_view', $data);
            // $this->load->view('gateway/main_view2', $data); // 開発中

        } else {
            redirect('/');
        }
    }
}
