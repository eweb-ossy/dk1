<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_notice_text_users extends MY_Model
{
  public $notice_text_id; // int 
  public $user_id; // int 
  
  public function __construct()
  {
    parent::__construct();
  }
}