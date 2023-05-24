<?php 

class Model_shift_default_data extends MY_Model
{
  public $dk_date;
  public $status;
  public $in_time;
  public $out_time;
  public $config_rules_id;
  
  public function __construct()
  {
    parent::__construct();
  }
}