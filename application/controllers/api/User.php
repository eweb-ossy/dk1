<?php defined('BASEPATH') or exit('No direct script access alllowed');

header('Access-Control-Allow-Origin: *');

class User extends CI_Controller
{
    public function login()
    {
        $login_id = $this->input->get('login_id');
        $password = $this->input->get('password');

        $data['is_logged'] = 'no user';

        if (isset($login_id) && isset($password)) {
            $this->load->database();
            // user check 
            $row = $this->db->query("SELECT user_id, name_sei, name_mei, kana_sei, kana_mei, `password` FROM user_data WHERE user_id = '{$login_id}' AND `state` = 1")->row();
            if (isset($row)) {
                if (password_verify($password, $row->password)) {
                    $result = $this->db->query("SELECT config_name, `value` FROM config_values WHERE config_name IN('company_name', 'system_id')")->result();
                    $conf_data = array_column($result, 'value', 'config_name');
                    $data = [
                        'is_logged' => 'is login',
                        'user_id' => $row->user_id,
                        'name_sei' => $row->name_sei,
                        'name_mei' => $row->name_mei,
                        'user_name' => $row->name_sei.' '.$row->name_mei,
                        'kana_sei' => $row->kana_sei,
                        'kana_mei' => $row->kana_mei,
                        'user_kaka' => $row->kana_sei.' '.$row->kana_mei,
                        'company_name' => $conf_data['company_name'],
                        'system_id' => $conf_data['system_id']
                    ];
                }
            }
        }

        // 出力 json
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function statusNow()
    {
        $user_id = $this->input->get('user_id');

        $this->load->database();

        $row = $this->db->query("SELECT in_time, out_time, in_flag, out_flag FROM time_data WHERE user_id = '{$user_id}' AND dk_date = CURDATE()")->row();

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($row));
    }
}