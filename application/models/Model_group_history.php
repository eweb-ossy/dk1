<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_group_history extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 従業員IDで検索し、最新のグループID情報を返す
    public function get_last_userid($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('to_date', 'DESC');
        return $this->db->get('group_history')->first_row();
    }

    // find_all
    public function find_all()
    {
        $this->db->order_by('to_date', 'ASC');
        return $this->db->get('group_history')->result();
    }

    // find_all user_id
    public function find_all_userid($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('to_date', 'DESC');
        return $this->db->get('group_history')->result();
    }
    
    // update
    public function update($data)
    {
        if (isset($data['group1_id'])) {
            $this->db->set('group1_id', $data['group1_id']);
        }
        if (isset($data['group2_id'])) {
            $this->db->set('group2_id', $data['group2_id']);
        }
        if (isset($data['group3_id'])) {
            $this->db->set('group3_id', $data['group3_id']);
        }
        if (isset($data['to_date'])) {
            $this->db->set('to_date', $data['to_date']);
        }
        $this->db->where('id', $data['id']);
        $ret = $this->db->update('group_history');
        if ($ret === false) {
            return false;
        }
        return true;
    }
        
    // insert
    public function insert_group($group_data)
    {
        $data = array(
            'user_id'=>$group_data['user_id'],
            'group1_id'=>$group_data['group1_id'],
            'group2_id'=>$group_data['group2_id'],
            'group3_id'=>$group_data['group3_id'],
            'to_date'=>$group_data['to_date']
        );
        $ret = $this->db->insert('group_history', $data);
        if ($ret === false) {
            return false;
        }
        return true;
    }
    
    // del 
    public function del($id)
    {
      $this->db->where('id', $id);
      $ret = $this->db->delete('group_history');
      if ($ret === false) {
        return false;
      }
      return true;
    }
}
