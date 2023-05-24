<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_user_data extends MY_Model
{
  public $name_sei;
  public $name_mei;
  public $kana_sei;
  public $kana_mei;
  public $user_id;
  public $state;
  public $entry_date;
  public $resign_date;
  public $birth_date;
  public $address;
  public $sex;
  public $memo;
  public $phone_number1;
  public $phone_number2;
  public $email1;
  public $email2;
  public $put_paid_vacation_month;
  public $aporan_flag;
  public $line_id;
  public $line_name;
  public $authority_id;
  public $advance_pay_flag;
  public $notice_mail_flag;
  public $notice_line_flag;
  public $password;
  public $in_time_user;
  public $out_time_user;
  public $start_month;
  public $idm;

  public function __construct()
  {
    parent::__construct();
  }
}
