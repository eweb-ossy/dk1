<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_notice_status_data extends MY_Model
{
  public $notice_status_id; // int 
  public $notice_status_title; // varchar 
  public $status; // int 
  public $group; // int 
  public $order; // int 
  public $term; // int 
  
  public function __construct()
  {
    parent::__construct();
  }
}