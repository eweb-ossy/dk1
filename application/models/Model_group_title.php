<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_group_title extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 通常データ取得用
    public function gets_data()
    {
        return $this->db->get('group_title')->result();
    }

    // 登録　update
    public function update_data($data)
    {
        $this->db->set('title', $data['title']);
        $this->db->where('group_id', $data['id']);
        $ret = $this->db->update('group_title');
        if ($ret === false) {
            return false;
        }
        return true;
    }
}
