<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_rest_rules extends MY_Model
{
  public $config_rules_id; // int 
  public $rest_time; // int 
  public $rest_type; // int 
  public $limit_work_hour; // int 
  public $rest_in_time; // time 
  public $rest_out_time; // time 
  
  public function __construct()
  {
    parent::__construct();
  }
} 