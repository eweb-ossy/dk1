<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_status_edit_lib
{
    protected $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function edit($status_data) {
        
        $dk_date = $status_data['dk_date'];
        $user_id = $status_data['user_id'];

        $this->CI->load->database();

        $time_data = $this->CI->db->query("SELECT `id`, `in_work_time`, `out_work_time`, `revision` FROM `time_data` WHERE `user_id` = {$status_data['user_id']} AND `dk_date` = '{$dk_date}' LIMIT 1")->row();

        if ($status_data['in_work_time']) {
            
        }
    }
}