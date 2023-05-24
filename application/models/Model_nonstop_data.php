<?php
defined('BASEPATH') OR exit('No direct script access alllowed');

class Model_nonstop_data extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
  
    public function gets_day_userid($date, $user_id)
    {
        $data = $this->db->select('*')->from('nonstop_data')
            ->where('nonstop_date', $date)
            ->where('user_id', $user_id)
            ->get();
        return $data->result();
    }
    
    public function insert($nonstop_data)
    {
        $data = array(
        'user_id'=>$nonstop_data['user_id'],
        'flag'=>$nonstop_data['flag'],
        'nonstop_date'=>$nonstop_data['nonstop_date']
        );
        $ret = $this->db->insert('nonstop_data', $data);
        if ($ret === false) {
            return false;
        }
        return;
    }
}