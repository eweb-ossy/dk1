<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_slack_lib
{
  protected $CI;
  public function __construct()
  {
    $this->CI =& get_instance();
  }
  
  public function slack_message($message)
  {
    $this->CI->load->model('model_config_values');
    $where = ['config_name'=>'slack_webhook_url'];
    $slack_webhook_url = $this->CI->model_config_values->find_row('value', $where)->value; // Webhook URL
    if (!$slack_webhook_url) {
      return;
    }

    // メッセージをjson化
    $message_json = json_encode($message);
 
    // payloadの値としてURLエンコード
    $message_post = "payload=".urlencode($message_json);
 
    try {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $slack_webhook_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $message_post);
      $result = curl_exec($ch);
      curl_close($ch);
    } catch (Exception $e) {
      echo "Exception-".$e->getMessage();
    }
    return;
  }
}