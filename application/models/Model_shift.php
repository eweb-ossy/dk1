<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_shift extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  // check
  public function check_day_userid($date, $user_id)
  {
    $sql = 'SELECT id, paid_hour FROM shift_data WHERE user_id = '.(int)$user_id.' and dk_date = "'.$date.'" order by id desc';
    $data = $this->db->query($sql)->first_row();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 従業員IDと年月日で検索
  public function find_day_userid($now_date, $user_id)
  {
    $sql = 'SELECT * FROM shift_data WHERE user_id = '.(int)$user_id.' and dk_date = "'.$now_date.'" order by id desc';
    $data = $this->db->query($sql)->first_row();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  //
  public function find_day_all($now_date)
  {
    $sql = 'SELECT * FROM shift_data WHERE dk_date = "'.$now_date.'"';
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 従業員IDと年月で検索し、月間のシフト情報を返す
  public function gets_status_month_userid($year, $month, $user_id)
  {
    $data = $this->db->select('*')->from('shift_data')
    ->where('date_format(dk_date, "%Y%m") = ', $year.$month)
    ->where('user_id', (int)$user_id)
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }

  // 従業員IDと年月で検索し、月間のシフト登録回数を返す month user_id
  public function get_count_shift_month_userid($year, $month, $user_id)
  {
    $sql = 'SELECT id FROM shift_data WHERE date_format(dk_date, "%Y%m") = '.$year.$month.' and user_id = '.(int)$user_id;
    $data = $this->db->query($sql)->num_rows();
    if ($data) {
      return $data;
    } else {
      return 0;
    }
  }
  // 月間のシフトのuser_idを全て返す
  public function gets_all_month($year, $month)
  {
    $sql ='SELECT user_id FROM shift_data WHERE date_format(dk_date, "%Y%m") = '.$year.$month;
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  // 月間のシフト esna pay 用　月間のシフト労働時間があるものだけ返す
  public function gets_all_hour_month($year, $month)
  {
    $sql ='SELECT user_id, hour FROM shift_data WHERE hour > 0 AND date_format(dk_date, "%Y%m") = '.$year.$month;
    $data = $this->db->query($sql)->result();
    return $data;
  }

  // 日付間 & 従業員ID 指定 all
  public function gets_to_end_date_user_id($first_date, $end_date, $user_id)
  {
    $sql = 'SELECT * FROM shift_data WHERE dk_date >= "'.$first_date.'" and dk_date <= "'.$end_date.'" and user_id = '.(int)$user_id.' order by dk_date';
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // gets < date (user_id, status)
  public function gets_to_end_date_status($end_date)
  {
    $sql = "SELECT user_id, status FROM shift_data WHERE dk_date <= '$end_date' AND status = 2";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  // gets first < date (user_id, status)
  public function gets_first_to_end_date_status($first_date, $end_date)
  {
    $sql = "SELECT user_id, status FROM shift_data WHERE dk_date >= '$first_date' AND dk_date <= '$end_date' AND status = 2";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // insert shift
  public function insert_shift($shift_data)
  {
    $data = array(
      'dk_date'=>$shift_data['dk_date'],
      'user_id'=>$shift_data['user_id'],
      'in_time'=>$shift_data['in_time'],
      'out_time'=>$shift_data['out_time'],
      'rest'=>$shift_data['rest'],
      'hour'=>$shift_data['hour'],
      'status'=>$shift_data['status'],
      'paid_hour'=>$shift_data['paid_hour']
    );
    $ret = $this->db->insert('shift_data', $data);
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // update shift
  public function update_shift($shift_data)
  {
    $this->db->set('in_time', $shift_data['in_time']);
    $this->db->set('out_time', $shift_data['out_time']);
    $this->db->set('rest', $shift_data['rest']);
    $this->db->set('hour', $shift_data['hour']);
    $this->db->set('status', $shift_data['status']);
    $this->db->set('dk_date', $shift_data['dk_date']);
    $this->db->set('user_id', $shift_data['user_id']);
    $this->db->set('paid_hour', $shift_data['paid_hour']);
    $this->db->where('id', $shift_data['id']);

    $ret = $this->db->update('shift_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }

  ////////
  // shift_register_data

  // check
  public function register_check_day_userid($date, $user_id)
  {
    $sql = 'SELECT id FROM shift_register_data WHERE user_id = '.(int)$user_id.' and dk_date = "'.$date.'" order by id desc';
    $data = $this->db->query($sql)->first_row();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // get register shift data by month & user_id
  public function gets_register_month_userid($now_date, $user_id)
  {
    $sql = 'SELECT * FROM shift_register_data WHERE dk_date > "'.$now_date.'" AND user_id = '.(int)$user_id.' AND flag = 1';
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  // get register shift data by all & user_id
  public function gets_register_all_userid($user_id)
  {
    $sql = 'SELECT * FROM shift_register_data WHERE user_id = '.(int)$user_id.' AND flag = 1';
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  //
  public function gets_register_first_to_end_date_user_id($first_date, $end_date, $user_id)
  {
    $sql = "SELECT * FROM shift_register_data WHERE dk_date >= '$first_date' AND dk_date <= '$end_date' AND user_id = '$user_id' AND flag = 1";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  //
  public function gets_register_all_first_to_end_date_user_id($first_date, $end_date, $user_id)
  {
    $sql = "SELECT * FROM shift_register_data WHERE dk_date >= '$first_date' AND dk_date <= '$end_date' AND user_id = '$user_id'";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  //
  public function del_register_all_first_to_end_date_user_id($first_date, $end_data, $user_id)
  {
    $this->db->where('user_id', $user_id);
    $this->db->where('dk_date >=', $first_date);
    $this->db->where('dk_date <=', $end_data);
    $ret = $this->db->delete('shift_register_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // insert register shift
  public function insert_register_shift($data)
  {
    $now = new DateTime();
    $data = array(
      'dk_date'=>$data['dk_date'],
      'user_id'=>$data['user_id'],
      'shift_status'=>$data['shift_status'],
      'in_time'=>$data['in_time'],
      'out_time'=>$data['out_time'],
      'up_datetime'=>$now->format('Y-m-d H:i:s'),
      'flag'=>1
    );
    $ret = $this->db->insert('shift_register_data', $data);
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // update register shift
  public function update_register_shift($data)
  {
    $now = new DateTime();
    if (isset($data['shift_status'])) {
      $this->db->set('shift_status', $data['shift_status']);
    }
    if (isset($data['in_time'])) {
      $this->db->set('in_time', $data['in_time']);
    } else {
      $this->db->set('in_time', NULL);
    }
    if (isset($data['out_time'])) {
      $this->db->set('out_time', $data['out_time']);
    } else {
      $this->db->set('out_time', NULL);
    }
    $this->db->set('up_datetime', $now->format('Y-m-d H:i:s'));
    $this->db->set('flag', $data['flag']);
    $this->db->where('id', $data['id']);

    $ret = $this->db->update('shift_register_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // del register shift
  public function delete_register_shift($data)
  {
    $this->db->where('id', $data['id']);
    $ret = $this->db->delete('shift_register_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }

}
