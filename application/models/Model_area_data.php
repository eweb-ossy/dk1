<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_area_data extends MY_Model
{
  public $area_name;
  public $host_ip;
  public $memo;
  
  public function __construct()
  {
    parent::__construct();
  }
}