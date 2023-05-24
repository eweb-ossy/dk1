<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_notice_data extends MY_Model
{
  public $notice_id; // varchar 
  public $notice_datetime; // datetime 
  public $to_user_id; // int 
  public $to_date; // date 
  public $notice_flag; // int 
  public $notice_in_time; // time 
  public $notice_out_time; // time 
  public $notice_status; // int 
  public $from_user_id; // int 
  public $before_in_time; // time 
  public $before_out_time; // time 
  public $end_date; // date 
  
  public function __construct()
  {
    parent::__construct();
  }
}