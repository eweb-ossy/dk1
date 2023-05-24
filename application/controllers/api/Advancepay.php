<?php
defined('BASEPATH') or exit('No direct script access alllowed');

header('Access-Control-Allow-Origin: *');

class Advancepay extends CI_Controller
{
  // 前払いできるくん用
  public function maebarai()
  {
    $data = [];
    $dk_date = $this->input->get('dk_date');
    
    // config data取得
    $this->load->model('model_config_values');
    $where = ['config_name'=>'id_size'];
    $id_size = (int)$this->model_config_values->find_row('value', $where)->value; // id size
    
    $this->load->model('model_time');
    // 年月日で検索し、該当する 前払いできるくん用 の勤務状況+従業員情報を返す day all + user_data
    $result = $this->model_time->gets_now_users_maebarai($dk_date);
    
    foreach ($result as $value) {
      if ($value->fact_work_hour > 0) {
        $fact_work_hour = (int)$value->fact_work_hour / 60;
      } else {
        $fact_work_hour = 0;
      }
      $data[] = [
        'user_id' => str_pad($value->user_id, $id_size, '0', STR_PAD_LEFT),
        'user_name' => $value->name_sei.' '.$value->name_mei,
        'fact_work_hour' => $fact_work_hour
      ];
    }
    
    $this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($data));
  }
}