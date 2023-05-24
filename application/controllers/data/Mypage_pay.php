<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Mypage_pay extends CI_Controller
{
    // 給与データ取得（個人）
    public function getPayData()
    {
        $user_id = $this->input->post('user_id');

        $this->load->database();
         // 給与タイトルデータ取得
        $result = $this->db->query("SELECT `field`, `title`, `status`, `hozAlign`, `type`, `formatter` FROM `payment_title` ORDER BY `order` ASC")->result();
        $data['title'] = array_column($result, NULL, 'field');
        unset($result);
        $data['data'] = $this->db->query("SELECT payment_data.id, payment_data.user_id, `year`, `month`, work1, work2, work3, work4, work5, work6, work7, work8, work9, work10, work11, work12, work13, work14, pay1, pay2, pay3, pay4, pay5, pay6, pay7, pay8, pay9, pay10, pay11, pay12, pay13, pay14, deduct1, deduct2, deduct3, deduct4, deduct5, deduct6, deduct7, deduct8, deduct9, deduct10, deduct11, deduct12, deduct13, deduct14, total1, total2, total3, total4, total5, total6, total7, payment_data.memo, CONCAT(name_sei, ' ', name_mei) AS `name` FROM `payment_data` JOIN `user_data` ON payment_data.user_id = user_data.user_id WHERE payment_data.user_id = '{$user_id}' AND `open` = 1")->result();

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    // config data取得 pay_password_flag
    public function getConfigData()
    {
        $this->load->database();
        $result = $this->db->query("SELECT `config_name`, `value` FROM config_values WHERE config_name = 'pay_password_flag'")->result();
        $data = array_column($result, 'value', 'config_name');
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    // payment_password取得
    public function checkSetPassword()
    {
        $user_id = $this->input->post('user_id');

        $this->load->database();
        $row = $this->db->query("SELECT `id` FROM payment_password WHERE `user_id` = '{$user_id}' AND `password` IS NOT NULL")->row();
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($row));
    }

    // payment_password 保存
    public function setPassword()
    {
        $user_id = $this->input->post('user_id');
        $password = $this->input->post('password');
        $id = $this->input->post('id');
        
        $this->load->database();
        $data = [
            'user_id'=> $user_id,
            'password'=> password_hash($password, PASSWORD_DEFAULT, ['cost'=> 10])
        ];
        $result = $this->db->insert('payment_password', $data);

        $this->output
        ->set_content_type('application/text')
        ->set_output($result);
    }

    // payment password チェック
    public function checkPassword()
    {
        $user_id = $this->input->post('user_id');
        $password = $this->input->post('password');
        // echo $user_id;
        // echo $password;

        $this->load->database();
        $row = $this->db->query("SELECT `password` FROM payment_password WHERE `user_id` = '{$user_id}'")->row();
        $resp = password_verify($password, $row->password) ? 'ok' : 'ng';

        $this->output->set_output($resp);
    }
}