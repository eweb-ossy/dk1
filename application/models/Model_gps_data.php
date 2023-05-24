<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_gps_data extends MY_Model
{
  public $gps_date;
  public $flag;
  public $user_id;
  public $latitude;
  public $longitude;
  public $ip_address;
  public $browser;
  public $version;
  public $mobile;
  public $platform;
  public $info;
  
  public function __construct()
  {
    parent::__construct();
  }
}