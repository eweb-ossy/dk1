<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_login extends CI_Model
{
  protected $CI;
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->CI =& get_instance();
  }

  // all login data
  public function gets_data()
  {
    $this->db->where('id !=', 99);
    $this->db->where('id !=', 100);
    return $this->db->get('login_data')->result();
  }

  //
  public function get_id_data($id)
  {
    $data = $this->db->select('id, login_id, area_id, authority, login_id, user_name')->from('login_data')
    ->where('id =', $id)->get();
    if ($data) {
      return $data->row();
    } else {
      return false;
    }
  }

  // login check
  public function check_login()
  {
    $login_id = $this->input->post('login_id');
    $password = $this->input->post('password');

    $latitude = $this->input->post('latitude'); // GPS情報
    $longitude = $this->input->post('longitude'); // GPS情報
    $gps_info = $this->input->post('gps_info'); // GPS情報
    $agent = $this->input->post('agent'); // デバイス情報

    $result = $this->db->get('login_data')->result();
    foreach ($result as $row) {
      if ($login_id === $row->login_id && password_verify($password, $row->password)) {
        session_regenerate_id(true);
        $data = [
          'is_logged_in' => 1,
          'login_id' => $login_id,
          'user_name' => $row->user_name,
          'authority' => $row->authority,
          'area_id' => $row->area_id,
          'latitude' => $latitude,
          'longitude' => $longitude,
          'agent' => $agent
        ];
        $this->session->set_userdata($data); // セッションセット
        return true;
      }
    }
    $this->CI->load->model('model_config_values');
    $where = ['config_name'=>'mypage_flag'];
    $mypage_flag = (int)$this->CI->model_config_values->find_row('value', $where)->value;
    $query = $this->db->get('user_data');
    foreach ($query->result() as $row) {
      if ($this->input->post('login_id') === $row->user_id && password_verify($this->input->post('password'), $row->password) && (int)$row->state === 1 && (int)$mypage_flag === 1) {
        session_regenerate_id(true);
        $data = [
          'is_logged_in' => 2,
          'user_id' => $row->user_id,
          'user_name' => $row->name_sei.' '.$row->name_mei,
          'authority' => 0,
          'agent' => $agent
        ];
        $this->session->set_userdata($data);
        return true;
      }
    }
    return false;
  }

  // get login data by id
  public function get_id_login_data($id)
  {
    $this->db->where('id', $id);
    return $this->db->get('login_data')->row();
  }

  // login_id check
  public function check_login_id($login_id)
  {
    $this->db->where('id !=', 99);
    $this->db->where('id !=', 100);
    $this->db->where('login_id', $login_id);
    return $this->db->get('login_data')->row();
  }

  // insert login user
  public function insert_loginuser($login_data)
  {
    $options = array('cost' => 10);
    $password = password_hash($login_data['password'], PASSWORD_DEFAULT, $options);
    $data = array(
      'login_id'=>$login_data['login_id'],
      'user_name'=>$login_data['user_name'],
      'password'=>$password,
      'authority'=>$login_data['authority'],
      'area_id'=>$login_data['area_id']
    );
    $ret = $this->db->insert('login_data', $data);
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // update login user
  public function update_loginuser($login_data)
  {
    if (isset($login_data['login_id'])) {
      $this->db->set('login_id', $login_data['login_id']);
    }
    if (isset($login_data['user_name'])) {
      $this->db->set('user_name', $login_data['user_name']);
    }
    if (isset($login_data['password'])) {
      $this->db->set('password', $login_data['password']);
    }
    if (isset($login_data['authority'])) {
      $this->db->set('authority', (int)$login_data['authority']);
    }
    if (isset($login_data['area_id'])) {
      $this->db->set('area_id', $login_data['area_id']);
    }
    $this->db->where('id', $login_data['id']);

    $ret = $this->db->update('login_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // delete login_user
  // -> data/Conf.php
  public function del_loginuser($id)
  {
    $this->db->where('id', $id);
    $ret = $this->db->delete('login_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }
}
