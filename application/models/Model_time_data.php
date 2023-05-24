<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_time_data extends MY_Model
{
  public $dk_date; // date 
  public $user_id; // int 
  public $in_time; // time 
  public $out_time; // time 
  public $in_work_time; // time 
  public $out_work_time; // time 
  public $rest; // int 
  public $revision; // int 
  public $in_flag; // int 
  public $out_flag; // int 
  public $fact_hour; // int 
  public $fact_work_hour; // int 
  public $status; // text 
  public $status_flag; // int 
  public $over_hour; // int 
  public $night_hour; // int 
  public $left_hour; // int 
  public $late_hour; // int 
  public $holiday; // float 
  public $memo; // text 
  public $shift_in_hour; // int 
  public $shift_out_hour; // int 
  public $series_work; // int 
  public $series_holiday; // int 
  public $area_id; // int 
  public $data_overlap_flag; // int 
  public $revision_user; // varchar 
  public $revision_datetime; // datetime 
  public $notice_memo; // text 
  public $revision_in; // int 
  public $revision_out; // int 
  
  public function __construct()
  {
    parent::__construct();
  }
}