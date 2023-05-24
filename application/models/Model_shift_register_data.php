<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_shift_register_data extends MY_Model
{
  public $user_id;
  public $dk_date;
  public $shift_status;
  public $in_time;
  public $out_time;
  public $hour;
  public $rest;
  public $up_datetime;
  public $flag;
  
  public function __construct()
  {
    parent::__construct();
  }
}