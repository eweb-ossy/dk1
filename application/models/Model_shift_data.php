<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_shift_data extends MY_Model
{
  public $user_id;
  public $dk_date;
  public $in_time;
  public $out_time;
  public $hour;
  public $rest;
  public $status;
  public $paid_hour;
  
  public function __construct()
  {
    parent::__construct();
  }
}