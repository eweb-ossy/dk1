<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_paid_data extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    //
    public function find_day_userid($date, $user_id)
    {
        $sql = 'select * from paid_data where user_id = '.(int)$user_id.' and paid_date = "'.$date.'"';
        $data = $this->db->query($sql)->row();
        if ($data) {
            return $data;
        } else {
            return false;
        }
    }
    
    //
    public function find_day_all($date)
    {
        $sql = 'select * from paid_data where paid_date = "'.$date.'"';
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
        $data = $this->db->select('*')->from('paid_data')
        ->where('date_format(paid_date, "%Y%m") = ', $year.$month)
        ->where('user_id', (int)$user_id)
        ->get();
        if ($data) {
            return $data->result();
        } else {
            return false;
        }
    }
    
    // 日付間 & 従業員ID 指定 all
    public function gets_to_end_date_user_id($first_date, $end_date, $user_id)
    {
        $sql = 'select * from paid_data where paid_date >= "'.$first_date.'" and paid_date <= "'.$end_date.'" and user_id = '.(int)$user_id.' order by paid_date';
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
        $insert_data = [
            'user_id'=> (int)$data['user_id'], // 従業員ID
            'paid_date'=> $data['paid_date'] // 有給日
        ];
        $ret = $this->db->insert('paid_data', $insert_data);
        if ($ret === false) {
            return false;
        }
        return true;
    }
    
    // 削除
    public function del($paid_id)
    {
        $this->db->where('id', $paid_id);
        $ret = $this->db->delete('paid_data');
        if ($ret === false) {
            return false;
        }
        return true;
    }
}
