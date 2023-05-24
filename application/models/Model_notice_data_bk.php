<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_notice_data_bk extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  ////// notice_auth
  //
  // gets low_user notice auth
  public function gets_auth($user_id)
  {
    $data = $this->db->select('low_user_id')->from('notice_auth')
      ->where('user_id', $user_id)
      ->get();
    return $data->result();
  }
  // gets low_user permit
  public function get_permit($user_id, $low_user_id)
  {
    $data = $this->db->select('permit')->from('notice_auth')
      ->where('user_id', $user_id)
      ->where('low_user_id', $low_user_id)
      ->get();
    return $data->row();
  }
  // gets high_user notice auth
  public function gets_high_user_auth($row_user_id)
  {
    $data = $this->db->select('user_id')->from('notice_auth')
      ->where('low_user_id', $row_user_id)
      ->get();
    return $data->result();
  }
  // gets high_user notice auth
  public function gets_permit_high_user_auth($row_user_id)
  {
    $data = $this->db->select('user_id')->from('notice_auth')
      ->where('low_user_id', $row_user_id)
      ->where('permit', 1)
      ->get();
    return $data->result();
  }
  // insert notice_auth
  public function insert_auth($data)
  {
    $data = array(
      'user_id'=>$data['user_id'],
      'low_user_id'=>$data['low_user_id'],
      'permit'=>$data['permit']
    );
    $ret = $this->db->insert('notice_auth', $data);
    if ($ret === false) {
      return false;
    }
    return true;
  }
  // update notice auth
  public function update_auth_permit($data)
  {
    $this->db->set('permit', $data['permit']);
    $this->db->where('user_id', $data['user_id']);
    $this->db->where('low_user_id', $data['low_user_id']);
    $ret = $this->db->update('notice_auth');
    if ($ret === false) {
      return false;
    }
    return true;
  }
  // del notice_auth
  public function del_auth($user_id)
  {
    return $this->db->delete('notice_auth', array('user_id' => $user_id));
  }
  // gets notice auth all
  public function gets_auth_all()
  {
    $data = $this->db->select('*')->from('notice_auth')
      ->get();
    return $data->result();
  }


  ////// notice_data
  //
  // find_all
  public function find_all()
  {
    return $this->db->get('notice_data')->result();
  }
  // find_id
  public function find_id($id)
  {
    $ret = $this->db->where(array('id' => $id))->get('notice_data')->row();
    return $ret;
  }
  // gets all
  public function gets_to_all()
  {
    $now = new DateTime();
    $now->sub(DateInterval::createFromDateString('3 month'));
    $where_date = $now->format('Y-m-d');
    $data = $this->db->select('*')->from('notice_data')
      ->order_by('notice_datetime', 'DESC')
      ->where('notice_datetime >=', $where_date.' 00:00:00')
      ->limit(500)
      ->get();
    return $data->result();
  }
  // get notice_id
  public function get_notice_id($notice_id)
  {
    $data = $this->db->select('*')->from('notice_data')
      ->where('notice_id', $notice_id)
      ->get();
    return $data->row();
  }
  // insert notice_data
  public function insert_data($notice_data)
  {
    $data = array(
      'notice_id'=>$notice_data['notice_id'],
      'notice_datetime'=>$notice_data['notice_datetime'],
      'to_user_id'=>$notice_data['to_user_id'],
      'notice_flag'=>$notice_data['notice_flag'],
      'to_date'=>$notice_data['to_date'],
      'notice_in_time'=>$notice_data['notice_in_time'],
      'notice_out_time'=>$notice_data['notice_out_time'],
      'notice_status'=>$notice_data['notice_status'],
      'before_in_time'=>$notice_data['before_in_time'],
      'before_out_time'=>$notice_data['before_out_time'],
      'end_date'=>$notice_data['end_date']
    );
    $ret = $this->db->insert('notice_data', $data);
    if ($ret === false) {
      return false;
    }
    return true;
  }
  // update notice_data
  public function update_data($data)
  {
    $this->db->set('notice_status', $data['notice_status']);
    $this->db->set('from_user_id', $data['to_user_id']);
    $this->db->set('notice_datetime', $data['notice_datetime']);
    $this->db->where('notice_id', $data['notice_id']);
    $ret = $this->db->update('notice_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }


  ////// notice_text_data
  //
  // get notice_id text
  public function get_text_notice_id($notice_id)
  {
    $data = $this->db->select('*')->from('notice_text_data')
      ->where('notice_id', $notice_id)
      ->order_by('text_datetime', 'ASC')
      ->get();
    return $data->result();
  }
  // 最後の通知テキストデータを返す
  public function get_text_notice_id_last($notice_id)
  {
    $data = $this->db->select('*')->from('notice_text_data')
      ->where('notice_id', $notice_id)
      ->order_by('text_datetime', 'ASC')
      ->get();
    return $data->last_row();
  }
  //
  public function get_text_notice_id_id($notice_id)
  {
    $data = $this->db->select('id')->from('notice_text_data')
      ->where('notice_id', $notice_id)
      ->get();
    return $data->result();
  }
  //
  public function get_text_notice_id_user_id($notice_id, $user_id)
  {
    $data = $this->db->select('id')->from('notice_text_data')
      ->where('notice_id', $notice_id)
      ->where('user_id', $user_id)
      ->get();
    return $data->row();
  }
  // insert notice_text
  public function insert_text($notice_data)
  {
    $data = array(
      'notice_id'=>$notice_data['notice_id'],
      'text_datetime'=>$notice_data['notice_datetime'],
      'user_id'=>$notice_data['to_user_id'],
      'notice_text'=>$notice_data['notice_text'],
      'notice_status'=>$notice_data['notice_status']
    );
    $ret = $this->db->insert('notice_text_data', $data);
    if ($ret === false) {
      return false;
    }
    return true;
  }


  ////// notice_text_users
  //
  // get notice_id users
  public function get_text_users_notice_id($notice_text_id)
  {
    $data = $this->db->select('*')->from('notice_text_users')
      ->where('notice_text_id', $notice_text_id)
      ->get();
    return $data->row();
  }
  // gets notice_id
  public function gets_notice_text_users($notice_text_id)
  {
    $data = $this->db->select('*')->from('notice_text_users')
      ->where('notice_text_id', $notice_text_id)
      ->get();
    return $data->result();
  }
  // check
  public function get_text_users_notice_id_check($notice_text_id, $user_id)
  {
    $data = $this->db->select('id')->from('notice_text_users')
      ->where('notice_text_id', $notice_text_id)
      ->where('user_id', $user_id)
      ->get();
    return $data->row();
  }
  // check notice_id & user_id
  public function get_text_users_notice_id_user_id_check($notice_text_id, $user_id)
  {
    $data = $this->db->select('id')->from('notice_text_users')
      ->where('notice_text_id', $notice_text_id)
      ->where('user_id', $user_id)
      ->get();
    return $data->row();
  }
  // insert notice_user
  public function insert_notice_text_user($user_id, $notice_text_id)
  {
    $data = array(
      'notice_text_id'=>$notice_text_id,
      'user_id'=>$user_id
    );
    $ret = $this->db->insert('notice_text_users', $data);
    if ($ret === false) {
      return false;
    }
    return true;
  }


  ////// notice_status_data
  //
  // gets notice_status data
  public function gets_notice_status_data()
  {
    $data = $this->db->select('*')->from('notice_status_data')
    ->order_by('order', 'ASC')
    ->get();
    return $data->result();
  }
  // get notice_status title
  public function get_notice_status_title($notice_status_id)
  {
    $data = $this->db->select('notice_status_title')->from('notice_status_data')
      ->where('notice_status_id', $notice_status_id)
      ->get();
    return $data->row();
  }
  // 登録　update
  public function update_notice_status($data)
  {
    if (isset($data['status'])) {
      $this->db->set('status', (int)$data['status']);
    }
    if (isset($data['group'])) {
      $this->db->set('group', (int)$data['group']);
    }
    if (isset($data['order'])) {
      $this->db->set('order', (int)$data['order']);
    }
    $this->db->where('id', $data['id']);
    if ($this->db->update('notice_status_data')) {
        return true;
    } else {
      return false;
    }
  }

}
