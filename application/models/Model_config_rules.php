<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_config_rules extends MY_Model
{
  public $user_id; // int 
  public $group_id; // int 
  public $group_no; // int 
  public $all_flag; // int 
  public $in_marume_flag; // int 
  public $in_marume_hour; // int 
  public $in_marume_time; // time 
  public $out_marume_flag; // int 
  public $out_marume_hour; // int 
  public $out_marume_time; // time 
  public $basic_in_time; // time 
  public $basic_out_time; // time 
  public $basic_rest_weekday; // varchar 
  public $rest_rule_flag; // int 
  public $over_limit_hour; // int 
  public $title; // varchar 
  public $status; // int 
  public $order; // int 
  public $summary; // varchar 
  
  public function __construct()
  {
    parent::__construct();
  }
}
