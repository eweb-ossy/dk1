<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_mail_lib {

  protected $CI;

  public function __construct() {
    $this->CI =& get_instance();
  }

  public function mail_send($subject, $message) {
    // config data取得
    $this->CI->load->model('model_config_values');
    $where = [];
    $result = $this->CI->model_config_values->find('config_name, value', $where, '');
    $data = array_column($result, 'value', 'config_name');

    $to_mail = [
      $data['notice_mailaddress1'],
      $data['notice_mailaddress2'],
      $data['notice_mailaddress3'],
      $data['notice_mailaddress4'],
      $data['notice_mailaddress5']
    ];
    $cc_mail = '';
    $bcc_mail = 'oshizawa.shinichi@eweb.co.jp';

    $this->CI->load->library('email');
    $this->CI->email->from('auto_mail@dakoku-keeper.com', '打刻keeperシステムメール');
    $this->CI->email->to($to_mail);
    $this->CI->email->cc($cc_mail);
    $this->CI->email->bcc($bcc_mail);
    $this->CI->email->subject($subject);
    $this->CI->email->message($message);
    if (!$this->CI->email->send()) {
      return false;
    }
    return true;
  }
}
