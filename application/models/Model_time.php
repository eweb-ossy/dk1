<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_time extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  // 日付指定
  // by data/Admin_list_day.php
  public function gets_day_all($now_date)
  {
    $sql = "SELECT * FROM time_data WHERE dk_date = '$now_date'";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  public function gets_day_in_userid($now_date, $users)
  {
    $data = $this->db->select('*')->from('time_data')
    ->where('dk_date', $now_date)
    ->where_in('user_id', $users)
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }
  public function gets_day_status($dk_date)
  {
    $data = $this->db->select('id, user_id, in_time, out_time, in_work_time, out_work_time, rest, memo')->from('time_data')
    ->where('dk_date', $dk_date)
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }
  public function gets_day_user_id_status($dk_date, $user_id)
  {
    $data = $this->db->select('*')->from('time_data')
    ->where('dk_date', $dk_date)
    ->where('user_id', $user_id)
    ->get();
    if ($data) {
      return $data->result();
    } else {
      return false;
    }
  }
  public function get_date_user_id_status($dk_date, $user_id)
  {
    $data = $this->db->select('*')->from('time_data')
    ->where('dk_date', $dk_date)
    ->where('user_id', $user_id)
    ->get();
    if ($data) {
      return $data->row();
    } else {
      return false;
    }
  }

  // 従業員IDと年月日で検索し、最新のIDのみを返す day user_id
  // memoは、notice_memo保存用に、仕方なく　次期バージョンでは、こんなことしない
  public function check_day_userid($now_date, $user_id)
  {
    $sql = 'SELECT id, memo FROM time_data WHERE user_id = '.(int)$user_id.' and dk_date = "'.$now_date.'" order by id desc';
    $data = $this->db->query($sql)->first_row();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 年月日で検索し、該当する全ての勤務状況+従業員情報を返す day all + user_data
  public function gets_now_users($now_date)
  {
    $sql = 'SELECT * FROM time_data JOIN user_data ON time_data.user_id = user_data.user_id WHERE dk_date = "'.$now_date.'"';
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // auto notice
  public function gets_now_users_status($now_date)
  {
    $sql = "SELECT time_data.user_id, name_sei, name_mei, status, status_flag, in_time, out_time FROM time_data JOIN user_data ON time_data.user_id = user_data.user_id WHERE dk_date = '$now_date' AND dk_date ORDER BY status_flag";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 年月日で検索し、該当する 前払いできるくん用 の勤務状況+従業員情報を返す day all + user_data
  public function gets_now_users_maebarai($now_date)
  {
    $sql = 'SELECT name_sei, name_mei, user_data.user_id, fact_work_hour FROM time_data JOIN user_data ON time_data.user_id = user_data.user_id WHERE dk_date = "'.$now_date.'" and advance_pay_flag = 1';
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 従業員IDと年月日で検索し、最新の時間情報を返す day user_id
  public function get_day_userid($now_date, $user_id)
  {
    $sql = 'SELECT * FROM time_data WHERE user_id = '.(int)$user_id.' and dk_date = "'.$now_date.'" order by id desc';
    $data = $this->db->query($sql)->first_row();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 従業員IDと年月で検索し、月間の総労働時間を返す month user_id
  public function get_fact_work_hour_sum_time_month_userid($year, $month, $user_id)
  {
    $sql = 'SELECT sum(fact_work_hour) AS fact_work_hour FROM time_data WHERE date_format(dk_date, "%Y%m") = '.$year.$month.' and user_id = '.$user_id;
    $data = $this->db->query($sql)->row();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 従業員IDと年月で検索し、月間の出勤回数を返す month user_id
  public function get_fact_work_hour_count_time_month_userid($year, $month, $user_id)
  {
    $sql = 'SELECT fact_work_hour FROM time_data WHERE date_format(dk_date, "%Y%m") = '.$year.$month.' and fact_work_hour > 0 and user_id = '.(int)$user_id;
    $data = $this->db->query($sql)->num_rows();
    if ($data) {
      return $data;
    } else {
      return 0;
    }
  }

  // 従業員IDと年月で検索し、月間の勤務情報を返す month user_id
  public function gets_status_month_userid($year, $month, $user_id)
  {
    $sql = 'SELECT * FROM time_data WHERE date_format(dk_date, "%Y%m") = '.$year.$month.' and user_id = '.(int)$user_id;
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 年月で検索し、月間の全て勤務情報を返す month all
  public function find_month_listmonth($year, $month)
  {
    $sql = 'SELECT * FROM time_data WHERE date_format(dk_date, "%Y%m") = '.$year.$month;
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // find_month_all
  public function find_month_all($year, $month)
  {
    $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $end_date = $year.'-'.$month.'-'.$month_days;
    $data = $this->db->select('*')->from('time_data')
    ->join('group_history', 'time_data.user_id = group_history.user_id')
    ->group_start()
      ->or_where('to_date <=', $end_date)
      ->or_where('to_date =', null)
    ->group_end()
    ->where('date_format(dk_date, "%Y%m")', $year.$month)
    ->get();
    return $data->result();
  }

  //
  public function find_status_day_userid($year, $month, $day, $user_id)
  {
    $data = $this->db->select('in_date_h, in_date_m, in_work_date_h, in_work_date_m, out_date_h, out_date_m, out_work_date_h, out_work_date_m, memo, fact_work_hour')->from('time_data')
    ->where('date_y', $year)
    ->where('date_m', $month)
    ->where('date_d', $day)
    ->where('user_id', $user_id)
    ->get();
    if ($data) {
      return $data->row();
    } else {
      return false;
    }
  }

  // 日付間指定 all
  public function gets_to_end_date_all($first_date, $end_date)
  {
    $sql = 'SELECT * FROM time_data WHERE dk_date >= "'.$first_date.'" and dk_date <= "'.$end_date.'" order by dk_date';
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 日付間 & 従業員ID 指定 all
  public function gets_to_end_date_user_id($first_date, $end_date, $user_id)
  {
    $sql = 'SELECT * FROM time_data WHERE dk_date >= "'.$first_date.'" and dk_date <= "'.$end_date.'" and user_id = '.(int)$user_id.' order by dk_date';
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // gets all user_id fact_work_hour dk_date
  public function gets_all_fact_work_hour()
  {
    $sql = "SELECT user_id, fact_work_hour, dk_date FROM time_data WHERE fact_work_hour > 0";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  // gets < date (user_id, fact_work_hour)
  public function gets_to_end_date_fact_work_hour($end_date)
  {
    $sql = "SELECT user_id, fact_work_hour FROM time_data WHERE dk_date <= '$end_date' AND fact_work_hour > 0";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }
  // gets first < date (user_id, fact_work_hour)
  public function gets_first_to_end_date_fact_work_hour($first_date, $end_date)
  {
    $sql = "SELECT user_id, fact_work_hour FROM time_data WHERE dk_date >= '$first_date' AND dk_date <= '$end_date' AND fact_work_hour > 0";
    $data = $this->db->query($sql)->result();
    if ($data) {
      return $data;
    } else {
      return false;
    }
  }

  // 新規登録
  public function insert_data($data)
  {
    $now = new DateTime();
    $now_date = $now->format('Y-m-d H:i:s');
    $insert_data = [
      'user_id'=> (int)$data['user_id'], // 従業員ID
      'dk_date'=> $data['dk_date'], // 入力日
      'in_time'=> isset($data['in_time']) ? $data['in_time'] : null, // 実出勤時刻
      'out_time'=> isset($data['out_time']) ? $data['out_time'] : null, // 実退勤時刻
      'in_work_time'=> isset($data['in_work_time']) ? $data['in_work_time'] : null, // まるめ出勤時刻
      'out_work_time'=> isset($data['out_work_time']) ? $data['out_work_time'] : null, // まるめ出勤時刻
      'revision'=> isset($data['revision']) ? (int)$data['revision'] : 0,
      'rest'=> isset($data['rest']) ? (int)$data['rest'] : 0,
      'in_flag'=> isset($data['in_flag']) ? (int)$data['in_flag'] : 0,
      'out_flag'=> isset($data['out_flag']) ? (int)$data['out_flag'] : 0,
      'fact_hour'=> isset($data['fact_hour']) ? (int)$data['fact_hour'] : 0,
      'fact_work_hour'=> isset($data['fact_work_hour']) ? (int)$data['fact_work_hour'] : 0,
      'status'=> isset($data['status']) ? $data['status'] : '',
      'status_flag'=> isset($data['status_flag']) ? (int)$data['status_flag'] : 0,
      'over_hour'=> isset($data['over_hour']) ? (int)$data['over_hour'] : 0,
      'night_hour'=> isset($data['night_hour']) ? (int)$data['night_hour'] : 0,
      'left_hour'=> isset($data['left_hour']) ? (int)$data['left_hour'] : 0,
      'late_hour'=> isset($data['late_hour']) ? (int)$data['late_hour'] : 0,
      'holiday'=> isset($data['holiday']) ? (int)$data['holiday'] : 0,
      'memo'=> isset($data['memo']) ? $data['memo'] : '',
      'shift_in_hour'=> isset($data['shift_in_hour']) ? (int)$data['shift_in_hour'] : null, // シフトとの出勤時のずれ
      'shift_out_hour'=> isset($data['shift_out_hour']) ? (int)$data['shift_out_hour'] : null, // シフトとの出勤時のずれ
      'series_work'=> isset($data['series_work']) ? (int)$data['series_work'] : 0, // 連続労働日数
      'series_holiday'=> isset($data['series_holiday']) ? (int)$data['series_holiday'] : 0, // 連続休暇日数
      'area_id'=> isset($data['area_id']) ? (int)$data['area_id'] : null, // エリアID
      'data_overlap_flag'=> isset($data['data_overlap_flag']) ? (int)$data['data_overlap_flag'] : null, //
      'revision_user'=> isset($data['revision_user']) ? $data['revision_user'] : null,
      'revision_datetime'=> isset($data['revision_datetime']) ? $data['revision_datetime'] : null,
      'notice_memo'=> isset($data['notice_memo']) ? $data['notice_memo'] : "",
      'revision_in'=> isset($data['revision_in']) ? $data['revision_in'] : 0,
      'revision_out'=> isset($data['revision_out']) ? $data['revision_out'] : 0,
      'created_at'=> $now_date,
      'updated_at'=> $now_date
    ];
    $ret = $this->db->insert('time_data', $insert_data);
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // アップデート
  public function update_data($data)
  {
    if (isset($data['in_time'])) {
      $this->db->set('in_time', $data['in_time']);
    }
    if (isset($data['out_time'])) {
      $this->db->set('out_time', $data['out_time']);
    }
    if (isset($data['in_work_time'])) {
      $this->db->set('in_work_time', $data['in_work_time']);
    } else {
      $this->db->set('in_work_time', null);
    }
    if (isset($data['out_work_time'])) {
      $this->db->set('out_work_time', $data['out_work_time']);
    } else {
      $this->db->set('out_work_time', null);
    }
    if (isset($data['revision'])) {
      $this->db->set('revision', $data['revision']);
    } else {
      $this->db->set('revision', 0);
    }
    if (isset($data['rest'])) {
      $this->db->set('rest', $data['rest']);
    } else {
      $this->db->set('rest', 0);
    }
    if (isset($data['in_flag'])) {
      $this->db->set('in_flag', $data['in_flag']);
    } else {
      $this->db->set('in_flag', 0);
    }
    if (isset($data['out_flag'])) {
      $this->db->set('out_flag', $data['out_flag']);
    } else {
      $this->db->set('out_flag', 0);
    }
    if (isset($data['fact_hour'])) {
      $this->db->set('fact_hour', $data['fact_hour']);
    } else {
      $this->db->set('fact_hour', 0);
    }
    if (isset($data['fact_work_hour'])) {
      $this->db->set('fact_work_hour', $data['fact_work_hour']);
    } else {
      $this->db->set('fact_work_hour', 0);
    }
    if (isset($data['status'])) {
      $this->db->set('status', $data['status']);
    } else {
      $this->db->set('status', "");
    }
    if (isset($data['status_flag'])) {
      $this->db->set('status_flag', $data['status_flag']);
    } else {
      $this->db->set('status_flag', 0);
    }
    if (isset($data['over_hour'])) {
      $this->db->set('over_hour', $data['over_hour']);
    } else {
      $this->db->set('over_hour', 0);
    }
    if (isset($data['night_hour'])) {
      $this->db->set('night_hour', $data['night_hour']);
    } else {
      $this->db->set('night_hour', 0);
    }
    if (isset($data['left_hour'])) {
      $this->db->set('left_hour', $data['left_hour']);
    } else {
      $this->db->set('left_hour', 0);
    }
    if (isset($data['late_hour'])) {
      $this->db->set('late_hour', $data['late_hour']);
    } else {
      $this->db->set('late_hour', 0);
    }
    if (isset($data['holiday'])) {
      $this->db->set('holiday', $data['holiday']);
    } else {
      $this->db->set('holiday', 0);
    }
    if (isset($data['memo'])) {
      $this->db->set('memo', $data['memo']);
    } else {
      $this->db->set('memo', "");
    }
    if (isset($data['shift_in_hour'])) {
      $this->db->set('shift_in_hour', $data['shift_in_hour']);
    } else {
      $this->db->set('shift_in_hour', NULL);
    }
    if (isset($data['shift_out_hour'])) {
      $this->db->set('shift_out_hour', $data['shift_out_hour']);
    } else {
      $this->db->set('shift_out_hour', NULL);
    }
    if (isset($data['area_id'])) {
      $this->db->set('area_id', $data['area_id']);
    } else {
      $this->db->set('area_id', NULL);
    }
    if (isset($data['revision_user'])) {
      $this->db->set('revision_user', $data['revision_user']);
    } else {
      $this->db->set('revision_user', NULL);
    }
    if (isset($data['revision_datetime'])) {
      $this->db->set('revision_datetime', $data['revision_datetime']);
    } else {
      $this->db->set('revision_datetime', NULL);
    }
    if (isset($data['notice_memo'])) {
      $this->db->set('notice_memo', $data['notice_memo']);
    } else {
      $this->db->set('notice_memo', NULL);
    }
    if (isset($data['revision_in'])) {
      $this->db->set('revision_in', $data['revision_in']);
    } else {
      $this->db->set('revision_in', 0);
    }
    if (isset($data['revision_out'])) {
      $this->db->set('revision_out', $data['revision_out']);
    } else {
      $this->db->set('revision_out', 0);
    }
    $now = new DateTime();
    $now_date = $now->format('Y-m-d H:i:s');
    $this->db->set('updated_at', $now_date);
    $this->db->where('id', $data['id']);
    $ret = $this->db->update('time_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }

  // アップデート 申請時
  public function update_notice_data($data) {
    $this->db->set('revision_datetime', $data['revision_datetime']);
    $this->db->set('revision_user', $data['revision_user']);
    $this->db->set('notice_memo', $data['notice_memo']);
    $this->db->set('memo', $data['memo']);
    $this->db->where('id', $data['id']);
    $ret = $this->db->update('time_data');
    if ($ret === false) {
      return false;
    }
    return true;
  }

}
