<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_layout extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 通常データ取得用
    public function get_data()
    {
        return $this->db->get('layout')->row();
    }
}
