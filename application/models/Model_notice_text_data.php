<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_notice_text_data extends MY_Model
{
  public $notice_id; // int 
  public $text_datetime; // datetime  
  public $user_id; // int 
  public $notice_text; // text 
  public $notice_status; // int 
  
  public function __construct()
  {
    parent::__construct();
  }
}
