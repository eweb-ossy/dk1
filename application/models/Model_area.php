<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_area extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // area data 取得
    // -> Admin.php
    public function get_area_data()
    {
        return $this->db->get('area_data')->result();
    }

    // get area name id
    // -> Gateway.php
    public function get_area_name_id($id)
    {
        $this->db->select('*');
        $this->db->where('id', $id);
        return $this->db->get('area_data')->row();
    }

    // get area name all
    // -> data/Columns.php
    public function get_area_name_all()
    {
        $this->db->select('area_name');
        return $this->db->get('area_data')->result();
    }

    // area_name check
    // -> data/Conf.php
    public function check_name($area_name)
    {
        $this->db->where('area_name', $area_name);
        return $this->db->get('area_data')->row();
    }

    // area_name update check
    // -> data/Conf.php
    public function check_name_update($area_name, $id)
    {
        $this->db->where('area_name', $area_name);
        $this->db->where('id !=', $id);
        return $this->db->get('area_data')->row();
    }

    // insert area
    // -> data/Conf.php
    public function insert($area_data)
    {
        $data = array(
        'area_name'=>$area_data['area_name'],
        'host_ip'=>$area_data['host_ip'],
        'memo'=>$area_data['memo']
        );
        $ret = $this->db->insert('area_data', $data);
        if ($ret === false) {
            return false;
        }
        return true;
    }

    // update area
    // -> data/Conf.php
    public function area_update($area_data)
    {
        if (isset($area_data['area_name'])) {
            $this->db->set('area_name', $area_data['area_name']);
        }
        if (isset($area_data['host_ip'])) {
            $this->db->set('host_ip', $area_data['host_ip']);
        }
        if (isset($area_data['memo'])) {
            $this->db->set('memo', $area_data['memo']);
        }
        $this->db->where('id', $area_data['id']);

        $ret = $this->db->update('area_data');
        if ($ret === false) {
            return false;
        }
        return true;
    }

    // delete area
    // -> data/Conf.php
    public function area_del($id)
    {
        $this->db->where('id', $id);
        $ret = $this->db->delete('area_data');
        if ($ret === false) {
            return false;
        }
        return true;
    }
}
