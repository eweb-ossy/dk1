<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_company_rules extends MY_Model
{
  public $rule;
  public $type;
  public $value;
  public $memo;
  
  public function __construct()
  {
    parent::__construct();
  }
}