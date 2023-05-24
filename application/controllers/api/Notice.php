<?php
defined('BASEPATH') or exit('No direct script access alllowed');

header('Access-Control-Allow-Origin: dakoku.work');

class Notice extends CI_Controller
{
  public function get_data()
  {
    $notice_data = [];
    $user_id = $this->input->get('user_id');
    $this->load->model('model_notice_data_bk');
    $notice_data = $this->model_notice_data_bk->gets_to_all();
    $this->load->model('model_user');
    foreach ($notice_data as $value) {
      $user_name = $this->model_user->get_now_state_userid($value->to_user_id);
      $notice_in_time = $value->notice_in_time ? substr($value->notice_in_time, 0, 5) : '';
      $notice_out_time = $value->notice_out_time ? substr($value->notice_out_time, 0, 5) : '';
      $before_in_time = $value->before_in_time ? substr($value->before_in_time, 0, 5) : '未出勤';
      $before_out_time = $value->before_out_time ? substr($value->before_out_time, 0, 5) : '未退勤';
      if ($value->from_user_id) {
        $from_user_name = $this->model_user->get_now_state_userid($value->from_user_id)->name_sei.' '.$this->model_user->get_now_state_userid($value->from_user_id)->name_mei;
        $from_user_id = $value->from_user_id;
      } else {
        $from_user_name = '';
        $from_user_id = '';
      }
      $end_date = $value->end_date;
      if ($end_date === '0000-00-00') {
        $end_date = '';
      }
      $notict_id = $value->notice_id;
      $notice_text_id = [];
      $notice_text_id = $this->model_notice_data_bk->get_text_notice_id_id($notict_id);
      $notice_text_id = array_column($notice_text_id, 'id');
      
      // 未読-既読は一旦やめる
      // $read_users = [];
      // foreach ($notice_text_id as $val) {
      //   $read_user = $this->model_notice_data_bk->gets_notice_text_users($val);
      //   $read_user = array_column($read_user, 'user_id');
      //   $read_users[$val] = $read_user;
      // }
      
      $high_user_id = [];
      $high_user_data = $this->model_notice_data_bk->gets_high_user_auth($value->to_user_id);
      $high_user_id = array_column($high_user_data, 'user_id');
      $data[] = [
        'user_id'=> $value->to_user_id,
        'user_name'=> $user_name->name_sei.' '.$user_name->name_mei,
        'notice_flag'=> $value->notice_flag,
        'to_date'=> $value->to_date,
        'notice_datetime'=> $value->notice_datetime,
        'notice_id'=> $notict_id,
        'notice_status'=>$value->notice_status,
        'notice_in_time'=>$notice_in_time,
        'notice_out_time'=>$notice_out_time,
        'from_user_id'=> $from_user_id,
        'from_user_name'=> $from_user_name,
        'before_in_time'=> $before_in_time,
        'before_out_time'=> $before_out_time,
        'end_date'=> $end_date,
        'notice_text_id'=> $notice_text_id,
        'high_user_id'=> $high_user_id,
        // 'read_users'=>$read_users
      ];
    }
    // foreach ((array)$data as $key => $value) {
    //   $sort[$key] = $value['non_read_flag'];
    // }
    // array_multisort($sort, SORT_DESC, $data);
    
    $this->output // 出力 json
    ->set_content_type('application/json')
    ->set_output(json_encode($data));
  }

  public function get_id_data()
  {
    $notice_data = [];
    $notice_id = $this->input->get('notice_id');
    $this->load->model('model_notice_data_bk');
    $this->load->model('model_user');
    $notice_data_data = $this->model_notice_data_bk->get_notice_id($notice_id);
    $notice_in_time = $notice_data_data->notice_in_time ? substr($notice_data_data->notice_in_time, 0, 5) : '';
    $notice_out_time = $notice_data_data->notice_out_time ? substr($notice_data_data->notice_out_time, 0, 5) : '';
    $before_in_time = $notice_data_data->before_in_time ? substr($notice_data_data->before_in_time, 0, 5) : '未出勤';
    $before_out_time = $notice_data_data->before_out_time ? substr($notice_data_data->before_out_time, 0, 5) : '未退勤';
    if ($notice_data_data->from_user_id) {
      $from_user_name = $this->model_user->get_now_state_userid($notice_data_data->from_user_id)->name_sei.' '.$this->model_user->get_now_state_userid($notice_data_data->from_user_id)->name_mei;
      $from_user_id = $notice_data_data->from_user_id;
    } else {
      $from_user_name = '';
      $from_user_id = '';
    }
    $end_date = $notice_data_data->end_date;
    if ($end_date === '0000-00-00') {
      $end_date = '';
    }
    $high_user_id = [];
    $high_user_data = $this->model_notice_data_bk->gets_permit_high_user_auth($notice_data_data->to_user_id);
    $high_user_id = array_column($high_user_data, 'user_id');
    $notice_data = [
      'to_user_id'=> $notice_data_data->to_user_id,
      'notice_flag'=> $notice_data_data->notice_flag,
      'to_date'=> $notice_data_data->to_date,
      'notice_datetime'=> $notice_data_data->notice_datetime,
      'notice_id'=> $notice_data_data->notice_id,
      'notice_status'=>$notice_data_data->notice_status,
      'notice_in_time'=>$notice_in_time,
      'notice_out_time'=>$notice_out_time,
      'from_user_id'=> $from_user_id,
      'from_user_name'=> $from_user_name,
      'before_in_time'=> $before_in_time,
      'before_out_time'=> $before_out_time,
      'end_date'=> $end_date,
      'permit_high_user_auth'=> $high_user_id
    ];
    $user_name = $this->model_user->get_now_state_userid($notice_data_data->to_user_id);
    $user_data = ['user_name' =>$user_name->name_sei.' '.$user_name->name_mei];
    $notice_text_data = $this->model_notice_data_bk->get_text_notice_id($notice_id);
    
    // 未読-既読は一旦やめる
    // $read_users = [];
    foreach ($notice_text_data as $val) {
      $user_name = $this->model_user->get_now_state_userid($val->user_id);
      // $read_users = $this->model_notice_data_bk->gets_notice_text_users($val->id);
      // $read_users = array_column($read_users, 'user_id');
      $message_data[] = [
        'text_datetime' => $val->text_datetime,
        'user_name' => $user_name->name_sei.' '.$user_name->name_mei,
        'message_text' => $val->notice_text,
        'user_id' => $val->user_id,
        // 'read_users' => $read_users,
        'notice_status' => $val->notice_status
      ];
    }
    $massage = ['massage' => $message_data];
    $data = (array)$notice_data + (array)$user_data + (array)$massage;
    $this->output // 出力 json
      ->set_content_type('application/json')
      ->set_output(json_encode($data));
  }
}
