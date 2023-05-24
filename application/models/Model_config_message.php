<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_config_message extends MY_Model
{
  public $public_message1_flag;
  public $public_message1_title;
  public $public_message1;
  public $in_message1_text;
  public $in_message2_flag;
  public $in_message2_diff;
  public $in_message2_text;
  public $in_message3_flag;
  public $in_message3_diff;
  public $in_message3_text;
  
  public function __construct()
  {
    parent::__construct();
  }
}
