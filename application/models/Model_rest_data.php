<?php
defined('BASEPATH') OR exit('No direct script access alllowed');

class Model_rest_data extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  // time_data_idで取得 最新
  public function get_timeid($time_id)
  {
    $this->db->where('time_data_id', $time_id);
    $this->db->order_by('in_time', 'DESC');
    return $this->db->get('rest_data')->first_row();
  }
  
  // time_data_idで抽出し、全ての休憩時間を返す
  public function get_timeid_all_reathour($time_id)
  {
    $this->db->select('rest_hour');
    $this->db->where('time_data_id', $time_id);
    return $this->db->get('rest_data')->result();
  }

  // 従業員ID＋年月日　最新
  public function get_day_userid($rest_date, $user_id)
  {
    $this->db->where('user_id', $user_id);
    $this->db->where('rest_date', $rest_date);
    $this->db->order_by('in_time', 'DESC');
    return $this->db->get('rest_data')->first_row();
  }
  
  // 新規登録
  public function insert($data)
  {
    $insert_data = [
      'user_id' => $data['user_id'],
      'flag' => $data['flag'],
      'rest_date' => $data['rest_date'],
      'in_time' => $data['in_time'],
      'out_time' => NULL,
      'rest_hour' => 0,
      'time_data_id' => $data['time_data_id']
    ];
    if ($this->db->insert('rest_data', $insert_data)) {
      return true;
    }
    return false;
  }
  
  // アップデータ
  public function update($data)
  {
    $this->db->set('out_time', $data['out_time']);
    $this->db->set('rest_hour', $data['rest_hour']);
    $this->db->set('flag', $data['flag']);
    $this->db->where('id', $data['id']);
    if ($this->db->update('rest_data')) {
      return true;
    }
    return false;
  }

}
