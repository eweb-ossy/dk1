<?php
defined('BASEPATH') OR exit('No direct script access alllowed');

class Model_goaway_data extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // time_data_idで取得 最新
    public function get_timeid($time_id)
    {
        this->db->where('time_data_id', $time_id);
        $this->db->order_by('in_time', 'DESC');
        return $this->db->get('goaway_data')->first_row();
    }

    // 従業員ID＋年月日　最新
    public function get_day_userid($rest_date, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('rest_date', $rest_date);
        $this->db->order_by('in_time', 'DESC');
        return $this->db->get('goaway_data')->first_row();
    }

}
