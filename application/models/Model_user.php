<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_user extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  // 従業員IDで検索し、現在在籍中であれば返す
  // by data/Gateway.php
  // by data/Admin_shift.php
  public function get_now_state_userid($user_id)
  {
    $now = new DateTime();
    $now_date = $now->format('Y-m-d');
    $data = $this->db->select('*')->from('user_data')
    ->where('user_id', $user_id)
    ->group_start()
      ->or_where('entry_date <=', $now_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('resign_date >=', $now_date)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('user_id')
    ->get();
    if ($data) {
      return $data->row();
    } else {
      return false;
    }
  }

  // by data/Admin_users.php
  public function gets_all()
  {
    $data = $this->db->select('*')->from('user_data')
    ->order_by('user_id')
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }

  //
  public function gets_state_all()
  {
    $data = $this->db->select('*')->from('user_data')
    ->where('state', 1)
    ->order_by('user_id')
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }


  // by data/Admin_list_day.php
  // by data/Admin_shift.php
  public function find_exist_all($now_date)
  {
    $data = $this->db->select('user_id, name_sei, name_mei')->from('user_data')
    ->group_start()
      ->or_where('management_flag =', null)
      ->or_where('management_flag =', 0)
    ->group_end()
    ->group_start()
      ->or_where('entry_date <=', $now_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('resign_date >=', $now_date)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('user_id')
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }

  //
  public function find_exist_all_to($now_date)
  {
    $data = $this->db->select('user_id, name_sei, name_mei, aporan_flag, advance_pay_flag')->from('user_data')
    ->group_start()
      ->or_where('entry_date <=', $now_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('resign_date >=', $now_date)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('user_id')
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }

  // gets users select $users array
  public function gets_exist_users($now_date, $users)
  {
    $data = $this->db->select('user_id, name_sei, name_mei')->from('user_data')
    ->where_in('user_id', $users)
    ->group_start()
      ->or_where('entry_date <=', $now_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('resign_date >=', $now_date)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('user_id')
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }

  // by data/Admin_list_month.php
  // by data/Admin_list_user.php
  public function find_exist_month_listmonth($to_month, $end_date)
  {
    $data = $this->db->select('*')->from('user_data')
    ->join('group_history', 'user_data.user_id = group_history.user_id')
    ->group_start()
      ->or_where('management_flag =', null)
      ->or_where('management_flag =', 0)
    ->group_end()
    ->group_start()
      ->or_where('to_date <=', $end_date)
      ->or_where('to_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('entry_date <=', $end_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('DATE_FORMAT(resign_date, "%Y%m") >=', $to_month)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('user_data.user_id')
    ->order_by('to_date', 'DESC')
    ->get();
    return $data->result();
  }
  // $users array
  public function find_exist_month_listmonth_users($to_month, $end_date, $users)
  {
    $data = $this->db->select('*')->from('user_data')
    ->join('group_history', 'user_data.user_id = group_history.user_id')
    ->where_in('user_data.user_id', $users)
    ->group_start()
      ->or_where('to_date <=', $end_date)
      ->or_where('to_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('entry_date <=', $end_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('DATE_FORMAT(resign_date, "%Y%m") >=', $to_month)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('user_data.user_id')
    ->order_by('to_date', 'DESC')
    ->get();
    return $data->result();
  }

  // find_exist_num_month
  public function find_exist_month_num($to_month, $end_date)
  {
    $data = $this->db->select('*')->from('user_data')
    ->group_start()
      ->or_where('entry_date <=', $end_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('DATE_FORMAT(resign_date, "%Y%m") >=', $to_month)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('user_id')
    ->get();
    return $data->num_rows();
  }

  // -> List_user_detail.php
  public function find_exist_month_userid($to_month, $end_date, $user_id)
  {
    $data = $this->db->select('name_sei, name_mei, kana_mei, kana_sei, group1_id, group2_id, group3_id')->from('user_data')
    ->join('group_history', 'user_data.user_id = group_history.user_id')
    ->where('user_data.user_id =', $user_id)
    ->group_start()
      ->or_where('to_date <=', $end_date)
      ->or_where('to_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('entry_date <=', $end_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('DATE_FORMAT(resign_date, "%Y%m") >=', $to_month)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('to_date', 'DESC')
    ->get();
    return $data->row();
  }

  // find_exist_userid
  public function find_exist_userid($get_date, $user_id)
  {
    $data = $this->db->select('*')->from('user_data')
    ->where('user_id', $user_id)
    ->group_start()
      ->or_where('entry_date <=', $get_date)
      ->or_where('entry_date =', null)
    ->group_end()
    ->group_start()
      ->or_where('resign_date >=', $get_date)
      ->or_where('resign_date =', null)
    ->group_end()
    ->order_by('user_id')
    ->get();
    return $data->row();
  }

  // find_all_userid
  public function find_all_userid($user_id)
  {
    $data = $this->db->select('*')->from('user_data')
    ->where('user_id', $user_id)
    ->get();
    return $data->row();
  }

  // find_all_id
  public function find_all_id($id)
  {
    $data = $this->db->select('*')->from('user_data')
    ->where('id', $id)
    ->get();
    return $data->row();
  }

  // find_line_id
  public function find_all_line_id($line_id)
  {
    $data = $this->db->select('id, name_sei, name_mei, user_id')->from('user_data')
    ->where('line_id', $line_id)
    ->get();
    return $data->row();
  }

  // gets_all_line_id
  public function get_line_id_all()
  {
    $data = $this->db->select('user_id, line_id')->from('user_data')
    ->where('state', 1)
    ->where('line_id !=', null)
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return null;
    }
  }

  // find_id
  public function find_id($user_id)
  {
    $data = $this->db->select('user_id')->from('user_data')
    ->where('user_id', $user_id)
    ->get();
    return $data->row();
  }

  // aporan
  public function aporan_user() {
    $data = $this->db->select('user_data.user_id, name_sei, name_mei, group1_id, group2_id, group3_id')->from('user_data')
      ->join('group_history', 'user_data.user_id = group_history.user_id')
      ->where('aporan_flag', 1)
    ->get();
    return $data->result();
  }

  // アップデート
  public function update_data($data)
  {
    if (isset($data['name_sei'])) {
      $this->db->set('name_sei', $data['name_sei']);
    }
    if (isset($data['name_mei'])) {
      $this->db->set('name_mei', $data['name_mei']);
    }
    if (isset($data['kana_sei'])) {
      $this->db->set('kana_sei', $data['kana_sei']);
    }
    if (isset($data['kana_mei'])) {
      $this->db->set('kana_mei', $data['kana_mei']);
    }
    if (isset($data['state'])) {
      $this->db->set('state', $data['state']);
    }
    if (isset($data['entry_date'])) {
      $this->db->set('entry_date', $data['entry_date']);
      if ($data['entry_date'] === 'none') {
        $this->db->set('entry_date', null);
      }
    }
    if (isset($data['resign_date'])) {
      $this->db->set('resign_date', $data['resign_date']);
      if ($data['resign_date'] === 'none') {
        $this->db->set('resign_date', null);
      }
    }
    if (isset($data['birth_date'])) {
      $this->db->set('birth_date', $data['birth_date']);
      if ($data['birth_date'] === 'none') {
        $this->db->set('birth_date', null);
      }
    }
    if (isset($data['zip_code'])) {
      $this->db->set('zip_code', $data['zip_code']);
    }
    if (isset($data['address'])) {
      $this->db->set('address', $data['address']);
    }
    if (isset($data['sex'])) {
      $this->db->set('sex', $data['sex']);
    }
    if (isset($data['memo'])) {
      $this->db->set('memo', $data['memo']);
    }
    if (isset($data['phone_number1'])) {
      $this->db->set('phone_number1', $data['phone_number1']);
    }
    if (isset($data['phone_number2'])) {
      $this->db->set('phone_number2', $data['phone_number2']);
    }
    if (isset($data['email1'])) {
      $this->db->set('email1', $data['email1']);
    }
    if (isset($data['email2'])) {
      $this->db->set('email2', $data['email2']);
    }
    if (isset($data['put_paid_vacation_month'])) {
      $this->db->set('put_paid_vacation_month', $data['put_paid_vacation_month']);
    }
    if (isset($data['aporan_flag'])) {
      $this->db->set('aporan_flag', $data['aporan_flag']);
    }
    if (isset($data['line_id'])) {
      $this->db->set('line_id', $data['line_id']);
    }
    if (isset($data['line_name'])) {
      $this->db->set('line_name', $data['line_name']);
    }
    if (isset($data['authority_id'])) {
      $this->db->set('authority_id', $data['authority_id']);
    }
    if (isset($data['advance_pay_flag'])) {
      $this->db->set('advance_pay_flag', $data['advance_pay_flag']);
    }
    if (isset($data['notice_mail_flag'])) {
      $this->db->set('notice_mail_flag', $data['notice_mail_flag']);
    }
    if (isset($data['notice_line_flag'])) {
      $this->db->set('notice_line_flag', $data['notice_line_flag']);
    }
    if (isset($data['password'])) {
      $this->db->set('password', $data['password']);
    }
    if (isset($data['in_time_user'])) {
      $this->db->set('in_time_user', $data['in_time_user']);
    }
    if (isset($data['out_time_user'])) {
      $this->db->set('out_time_user', $data['out_time_user']);
    }
    if (isset($data['shift_alert_flag'])) {
      $this->db->set('shift_alert_flag', $data['shift_alert_flag']);
    }
    if (isset($data['management_flag'])) {
      $this->db->set('management_flag', $data['management_flag']);
    }
    if (isset($data['input_confirm_flag'])) {
      $this->db->set('input_confirm_flag', $data['input_confirm_flag']);
    }
    if (isset($data['esna_pay_flag'])) {
      $this->db->set('esna_pay_flag', $data['esna_pay_flag']);
    }
    if (isset($data['api_output'])) {
      $this->db->set('api_output', $data['api_output']);
    }
    if (isset($data['password_change'])) {
      $this->db->set('password_change', $data['password_change']);
    }
    $this->db->where('id', $data['id']);
    $ret = $this->db->update('user_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // insert user
  public function insert_user($user_data)
  {
    $data = array(
      'name_sei'=>$user_data['name_sei'],
      'name_mei'=>$user_data['name_mei'],
      'kana_sei'=>$user_data['kana_sei'],
      'kana_mei'=>$user_data['kana_mei'],
      'user_id'=>$user_data['user_id'],
      'entry_date'=>$user_data['entry_date'],
      'birth_date'=>$user_data['birth_date'],
      'phone_number1'=>$user_data['phone1'],
      'phone_number2'=>$user_data['phone2'],
      'zip_code'=>$user_data['zip_code'],
      'address'=>$user_data['address'],
      'sex'=>$user_data['sex'],
      'memo'=>$user_data['memo'],
      'email1'=>$user_data['email1'],
      'email2'=>$user_data['email2'],
      'put_paid_vacation_month'=>$user_data['paid_vacation_month'],
      'password'=>$user_data['password'],
      'shift_alert_flag'=>$user_data['shift_alert_flag'],
      'management_flag'=>$user_data['management_flag']
    );
    $ret = $this->db->insert('user_data', $data);
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // delete
  public function delete_user($user_id)
  {
    $tables = ['user_data', 'group_history', 'config_rules', 'goaway_data', 'gps_data', 'nonstop_data', 'notice_auth', 'paid_data', 'pay_data', 'rest_data', 'shift_data', 'time_data'];
    $this->db->where('user_id', $user_id);
    $ret = $this->db->delete($tables);
    if ($ret === false) {
      return false;
    }
    return true;
  }
  public function delete_low_user($user_id)
  {
    $this->db->where('low_user_id', $user_id);
    if ($this->db->delete('notice_auth')) {
      return true;
    } else {
      return false;
    }
  }
}
