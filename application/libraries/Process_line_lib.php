<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_line_lib
{
  protected $CI;
  public function __construct()
  {
    $this->CI =& get_instance();
  }

  public function line_message($message, $user_id)
  {
    // config data取得
    $this->CI->load->model('model_config_values');
    $where = ['config_name'=>'line_token'];
    $accessToken = $this->CI->model_config_values->find_row('value', $where)->value; // LINE token

    // $to[] = 'U6850546a324814e30dc1410acb2c076a'; // ossy LINE ID
    // $to[] = 'Ubb50564b439e3df9b33d1a29a002fe49'; // koi LINE ID

    // $this->CI->load->model('model_notice_data_bk');

    $this->CI->load->model('model_user');
    $user_data = $this->CI->model_user->get_line_id_all();
    foreach ($user_data as $key) {
      $to[] = $key->line_id;
    }

    $headers = array('Content-Type: application/json','Authorization: Bearer ' . $accessToken);
    $body = json_encode(array('to' => $to, 'messages' => array($message)));
    $options = array(CURLOPT_URL            => 'https://api.line.me/v2/bot/message/multicast',
                     CURLOPT_CUSTOMREQUEST  => 'POST',
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_HTTPHEADER     => $headers,
                     CURLOPT_POSTFIELDS     => $body);
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    curl_exec($curl);
    curl_close($curl);
    return;
  }
}
