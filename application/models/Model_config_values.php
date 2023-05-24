<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_config_values extends MY_Model
{
  public $config_name;
  public $type;
  public $value;
  
  public function __construct()
  {
    parent::__construct();
  }
}