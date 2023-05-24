<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_group extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // グループIDで検索し、対象のグループデータ返す
    public function get_group1_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('user_groups1')->row();
    }
    public function get_group2_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('user_groups2')->row();
    }
    public function get_group3_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('user_groups3')->row();
    }

    // 表示用　state = 1のみ取得
    // -> Admin.php
    public function find_group1_all()
    {
        $this->db->where('state', 1);
        $this->db->order_by('group_order', 'ASC');
        return $this->db->get('user_groups1')->result();
    }
    public function find_group2_all()
    {
        $this->db->where('state', 1);
        $this->db->order_by('group_order', 'ASC');
        return $this->db->get('user_groups2')->result();
    }
    public function find_group3_all()
    {
        $this->db->where('state', 1);
        $this->db->order_by('group_order', 'ASC');
        return $this->db->get('user_groups3')->result();
    }

    public function find_group1_fullall()
    {
        $this->db->order_by('group_order', 'ASC');
        return $this->db->get('user_groups1')->result();
    }
    public function find_group2_fullall()
    {
        $this->db->order_by('group_order', 'ASC');
        return $this->db->get('user_groups2')->result();
    }
    public function find_group3_fullall()
    {
        $this->db->order_by('group_order', 'ASC');
        return $this->db->get('user_groups3')->result();
    }

    // 登録　update
    public function update_group1($data)
    {
        if (isset($data['group_name'])) {
            $this->db->set('group_name', $data['group_name']);
        }
        if (isset($data['state'])) {
            $this->db->set('state', $data['state']);
        }
        if (isset($data['group_order'])) {
            $this->db->set('group_order', $data['group_order']);
        }
        $this->db->where('id', $data['id']);
        $ret = $this->db->update('user_groups1');
        if ($ret === false) {
            return false;
        }
        return true;
    }
    public function update_group2($data)
    {
        if (isset($data['group_name'])) {
            $this->db->set('group_name', $data['group_name']);
        }
        if (isset($data['state'])) {
            $this->db->set('state', $data['state']);
        }
        if (isset($data['group_order'])) {
            $this->db->set('group_order', $data['group_order']);
        }
        $this->db->where('id', $data['id']);
        $ret = $this->db->update('user_groups2');
        if ($ret === false) {
            return false;
        }
        return true;
    }
    public function update_group3($data)
    {
        if (isset($data['group_name'])) {
            $this->db->set('group_name', $data['group_name']);
        }
        if (isset($data['state'])) {
            $this->db->set('state', $data['state']);
        }
        if (isset($data['group_order'])) {
            $this->db->set('group_order', $data['group_order']);
        }
        $this->db->where('id', $data['id']);
        $ret = $this->db->update('user_groups3');
        if ($ret === false) {
            return false;
        }
        return true;
    }

    // 新規登録
    public function insert_group1($data)
    {
        $insert_data = [
      'group_name'=> isset($data['group_name']) ? $data['group_name'] : null,
      'state'=> isset($data['state']) ? $data['state'] : 1,
      'group_order'=> isset($data['group_order']) ? $data['group_order'] : ''
    ];
        $ret = $this->db->insert('user_groups1', $insert_data);
        if ($ret === false) {
            return false;
        }
        return true;
    }
    public function insert_group2($data)
    {
        $insert_data = [
      'group_name'=> isset($data['group_name']) ? $data['group_name'] : null,
      'state'=> isset($data['state']) ? $data['state'] : 1,
      'group_order'=> isset($data['group_order']) ? $data['group_order'] : ''
    ];
        $ret = $this->db->insert('user_groups2', $insert_data);
        if ($ret === false) {
            return false;
        }
        return true;
    }
    public function insert_group3($data)
    {
        $insert_data = [
      'group_name'=> isset($data['group_name']) ? $data['group_name'] : null,
      'state'=> isset($data['state']) ? $data['state'] : 1,
      'group_order'=> isset($data['group_order']) ? $data['group_order'] : ''
    ];
        $ret = $this->db->insert('user_groups3', $insert_data);
        if ($ret === false) {
            return false;
        }
        return true;
    }
}
