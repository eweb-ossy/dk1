<?php
defined('BASEPATH') or exit('No direct script access alllowed');

// header('Access-Control-Allow-Origin: dakoku.work');
header('Access-Control-Allow-Origin: *');

class V2 extends CI_Controller
{
    public function users()
    {
        $user_id = $this->input->get('user_id');

        $this->load->database();
        $row = $this->db->query("SELECT CONCAT(name_sei, ' ', name_mei) AS `user_name` FROM user_data WHERE `user_id` = {$user_id}")->row();

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($row));
    }
}