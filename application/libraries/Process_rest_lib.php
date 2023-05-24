<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_rest_lib
{
  protected $CI;
  public function __construct()
  {
    $this->CI =& get_instance();
  }
  
  public function rest($data)
  {
    $data['rest'] = 0;
    $auto_rest = 0;
    $config_rules_id = '';
    // ルールの取得
    $rules = [];
    $this->CI->load->model('model_config_rules'); // model config_rules
    $select = 'id, rest_rule_flag';

    $where = ['user_id' => $data['user_id']];
    $userId_rule = $this->CI->model_config_rules->find_row($select, $where);
    if ($userId_rule) {
      if ((int)$userId_rule->rest_rule_flag === 1) {
        $auto_rest = 1;
        $config_rules_id = (int)$userId_rule->id;
      }
    } else {
      $this->CI->load->model('model_group_history'); // model group_history
      $group_history_data = $this->CI->model_group_history->get_last_userid($data['user_id']);
      $group_rule = [];
      if ($group_history_data->group3_id) {
        $where = ['group_id' => 3, 'group_no' => (int)$group_history_data->group3_id];
        $group_rule = $this->CI->model_config_rules->find_row($select, $where);
        if ($group_rule) {
          if ((int)$group_rule->rest_rule_flag === 1) {
            $auto_rest = 1;
            $config_rules_id = (int)$userId_rule->id;
          }
        }
      }
      $group_rule = [];
      if ($group_history_data->group2_id) {
        $where = ['group_id' => 2, 'group_no' => (int)$group_history_data->group2_id];
        $group_rule = $this->CI->model_config_rules->find_row($select, $where);
        if ($group_rule) {
          if ((int)$group_rule->rest_rule_flag === 1) {
            $auto_rest = 1;
            $config_rules_id = (int)$userId_rule->id;
          }
        }
      }
      $group_rule = [];
      if ($group_history_data->group1_id) {
        $where = ['group_id' => 1, 'group_no' => (int)$group_history_data->group1_id];
        $group_rule = $this->CI->model_config_rules->find_row($select, $where);
        if ($group_rule) {
          if ((int)$group_rule->rest_rule_flag === 1) {
            $auto_rest = 1;
            $config_rules_id = (int)$userId_rule->id;
          }
        }
      }
    }
    if ($auto_rest === 0) {
      $where = ['all_flag' => 1];
      $all_rule = $this->CI->model_config_rules->find_row($select, $where);
      if ($all_rule) {
        if ((int)$all_rule->rest_rule_flag === 1) {
          $auto_rest = 1;
          $config_rules_id = (int)$all_rule->id;
        }
      }
    }
    
    // 自動休憩時間　取得
    if ($auto_rest === 1 && $config_rules_id) {
      // if ($rules->rest_rule_flag == 1) {
        // $config_rules_id = $rules->id;
        $rest_rules = [];
        $this->CI->load->model('model_rest_rules'); // model rest_rules 
        
        $select = 'rest_type, limit_work_hour, rest_time, rest_in_time, rest_out_time';
        $where = ['config_rules_id'=>(int)$config_rules_id];
        $rest_rules = $this->CI->model_rest_rules->find($select, $where, '');
        
        foreach ((array)$rest_rules as $value) {
          if ((int)$value->rest_type === 1) {
            if ((int)$data['fact_work_hour'] >= (int)$value->limit_work_hour) {
              $data['rest'] = $value->rest_time;
            }
          }
          if ((int)$value->rest_type === 2) {
            $rest_in_time = $value->rest_in_time;
            $rest_out_time = $value->rest_out_time;
            if (strtotime($data['dk_date'].' '.$data['in_work_time']) <= strtotime($data['dk_date'].' '.$rest_in_time) && strtotime($data['dk_date'].' '.$data['out_work_time']) >= strtotime($data['dk_date'].' '.$rest_out_time)) {
              $data['rest'] = $value->rest_time;
            }
          }
        // }
      }
    }
    
    // $data['fact_work_hour'] -= $data['rest'];
    
    return $data;
  }
}