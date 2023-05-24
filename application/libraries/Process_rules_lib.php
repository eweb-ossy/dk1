<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_rules_lib
{
    protected $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    // ルールデータの読み込み
    public function get_rule($user_id)
    {
        $rules = [];
        $this->CI->load->model('model_config_rules'); // model config_rules
        $select = 'id, in_marume_flag, in_marume_hour, in_marume_time, out_marume_flag, out_marume_hour, out_marume_time, basic_in_time, basic_out_time, basic_rest_weekday, rest_rule_flag, over_limit_hour';

        $where = ['user_id' => (int)$user_id];
        $rules = $this->CI->model_config_rules->find_row($select, $where);
        if (!$rules) {
            $this->CI->load->model('model_group_history'); // model group_history
            $group_history_data = $this->CI->model_group_history->get_last_userid((int)$user_id);
            if (isset($group_history_data->group3_id)) {
                $where = ['group_id' => 3, 'group_no' => (int)$group_history_data->group3_id];
                $group_rule = $this->CI->model_config_rules->find_row($select, $where);
                if ($group_rule) {
                    $rules = $group_rule;
                }
            }
            if (isset($group_history_data->group2_id)) {
                $where = ['group_id' => 2, 'group_no' => (int)$group_history_data->group2_id];
                $group_rule = $this->CI->model_config_rules->find_row($select, $where);
                if ($group_rule) {
                    $rules = $group_rule;
                }
            }
            if (isset($group_history_data->group1_id)) {
                $where = ['group_id' => 1, 'group_no' => (int)$group_history_data->group1_id];
                $group_rule = $this->CI->model_config_rules->find_row($select, $where);
                if ($group_rule) {
                    $rules = $group_rule;
                }
            }
        }
        if (!$rules) {
            $where = ['all_flag' => 1];
            $rules = $this->CI->model_config_rules->find_row($select, $where);
        }

        return $rules;
    }
}
