<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_conf extends CI_Controller
{
    public function save_page_01()
    {
        $saveData = $this->input->post('saveData');
        $this->load->model('model_config_values');
        foreach ($saveData as $value) {
            $id = (int)$value['id'];
            $data['value'] = $value['value'];
            $this->model_config_values->update($id, $data);
        }

        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_02()
    {
        $rule_data = $this->input->post('rule_data');

        $this->load->model('Model_config_rules');
        $this->load->model('Model_rest_rules');
        foreach ($rule_data as $value) {
            $id = $value['rule_id'];
            $all_flag = 0;
            if ($value['rule_type'] == 1) {
                $all_flag = 1;
            }
            $basic_rest_weekday = '00000000';
            if (isset($value['basic_rest_weekday'])) {
                foreach ($value['basic_rest_weekday'] as $val) {
                    $basic_rest_weekday = substr_replace($basic_rest_weekday, '1', (int)$val, 1);
                }
            }
            if ($id == 'new') {
                // insert 
                $this->Model_config_rules->user_id = @$value['rule_user_id'] ?: NULL;
                $this->Model_config_rules->group_id = @$value['rule_group_id'] ?: NULL;
                $this->Model_config_rules->all_flag = $all_flag;
                $this->Model_config_rules->in_marume_flag = @$value['in_marume_flag'] ?: NULL;
                $this->Model_config_rules->in_marume_hour = @$value['in_marume_hour'] ?: NULL;
                $this->Model_config_rules->in_marume_time = @$value['in_marume_time'] ?: NULL;
                $this->Model_config_rules->out_marume_flag = @$value['out_marume_flag'] ?: NULL;
                $this->Model_config_rules->out_marume_hour = @$value['out_marume_hour'] ?: NULL;
                $this->Model_config_rules->out_marume_time = @$value['out_marume_time'] ?: NULL;
                $this->Model_config_rules->basic_in_time = @$value['basic_in_time'] ?: NULL;
                $this->Model_config_rules->basic_out_time = @$value['basic_out_time'] ?: NULL;
                $this->Model_config_rules->basic_rest_weekday = $basic_rest_weekday;
                $this->Model_config_rules->rest_rule_flag = @$value['rest_rule_flag'] ?: NULL;
                $this->Model_config_rules->over_limit_hour = @$value['over_limit_hour'] ?: NULL;
                $this->Model_config_rules->title = @$value['rule_title'] ?: NULL;
                $this->Model_config_rules->order = $value['order'];
                $this->Model_config_rules->insert();
                // find row 
                $select = 'id';
                $where = [
                'order'=>$value['order']
                ];
                $new_rule_id = $this->Model_config_rules->find_row($select, $where)->id;
            } else {
                // update 
                $data = [
                    'user_id'=>@$value['rule_user_id'] ?: NULL,
                    'group_id'=>@$value['rule_group_id'] ?: NULL,
                    'group_no'=>@$value['rule_group_no'] ?: NULL,
                    'all_flag'=>$all_flag,
                    'in_marume_flag'=>@$value['in_marume_flag'] ?: NULL,
                    'in_marume_hour'=>@$value['in_marume_hour'] ?: NULL,
                    'in_marume_time'=>@$value['in_marume_time'] ?: NULL,
                    'out_marume_flag'=>@$value['out_marume_flag'] ?: NULL,
                    'out_marume_hour'=>@$value['out_marume_hour'] ?: NULL,
                    'out_marume_time'=>@$value['out_marume_time'] ?: NULL,
                    'basic_in_time'=>@$value['basic_in_time'] ?: NULL,
                    'basic_out_time'=>@$value['basic_out_time'] ?: NULL,
                    'basic_rest_weekday'=>$basic_rest_weekday,
                    'rest_rule_flag'=>@$value['rest_rule_flag'] ?: NULL,
                    'over_limit_hour'=>@$value['over_limit_hour'] ?: NULL,
                    'title'=>@$value['rule_title'] ?: NULL,
                    'order'=>$value['order']
                ];
                $this->Model_config_rules->update($id, $data);
            }

            //
            $rest_id = $value['rest_rule_id'];
            $rest_type = @$value['rest_type'] ?: NULL;
            $rest_time = @$value['rest_time'] ?: NULL;
            $rest_in_time = @$value['rest_in_time'] ?: NULL;
            $rest_out_time = @$value['rest_out_time'] ?: NULL;
            if ($rest_type == 2) {
                $tmp = date_parse_from_format('Y-m-d h:i:s', '2000-01-01 '.$rest_in_time);
                $rest_in_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                $tmp = date_parse_from_format('Y-m-d h:i:s', '2000-01-01 '.$rest_out_time);
                $rest_out_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                $rest_time = (strtotime($rest_out_time_calc) - strtotime($rest_in_time_calc)) / 60;
            }
            if ($rest_id == 'new') {
                $this->Model_rest_rules->config_rules_id = (int)$new_rule_id;
                $this->Model_rest_rules->rest_time = $rest_time;
                $this->Model_rest_rules->rest_type = $rest_type;
                $this->Model_rest_rules->limit_work_hour = @$value['limit_work_hour'] ?: NULL;
                $this->Model_rest_rules->rest_in_time = $rest_in_time;
                $this->Model_rest_rules->rest_out_time = $rest_out_time;
                $this->Model_rest_rules->insert();
            } else {
                $rest_data = [
                    'config_rules_id'=>$id,
                    'rest_time'=>$rest_time,
                    'rest_type'=>$rest_type,
                    'limit_work_hour'=>@$value['limit_work_hour'] ?: NULL,
                    'rest_in_time'=>$rest_in_time,
                    'rest_out_time'=>$rest_out_time
                ];
                $this->Model_rest_rules->update($rest_id, $rest_data);
            }
        }

        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function del_page_02()
    {
        $rule_id = (int)$this->input->post('rule_id');
        $rest_id = (int)$this->input->post('rest_id');

        // delete 
        $this->load->model('Model_config_rules');
        $this->Model_config_rules->delete($rule_id);
        // delete 
        $this->load->model('Model_rest_rules');
        $this->Model_rest_rules->delete($rest_id);

        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_03()
    {
        $now = new DateTime();
        $now_date = $now->format('Y-m-d H:i:s');
        $message_title_id = $this->input->post('message_title_data_id');
        $message_title_data = [
            'flag'=> $this->input->post('public_message1_flag'),
            'title'=> $this->input->post('public_message1_title'),
            'detail'=> $this->input->post('public_message1')
        ];
        $this->load->database();
        if ($message_title_id) {
            $message_title_data['updated_at'] = $now_date;
            $this->db->where('id', $message_title_id);
            $this->db->update('message_title_data', $message_title_data);
        } else {
            $message_title_data['type'] = 'gateway';
            $message_title_data['created_at'] = $now_date;
            $message_title_data['updated_at'] = $now_date;
            $this->db->set($message_title_data);
            $this->db->insert('message_title_data');
        }

        $saveData = $this->input->post('saveData');
        $this->load->model('model_config_values');
        foreach ($saveData as $value) {
            $id = (int)$value['id'];
            $confData['value'] = $value['value'];
            $this->model_config_values->update($id, $confData);
        }
        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_14()
    {
        $now = new DateTime();
        $now_date = $now->format('Y-m-d H:i:s');
        $message_in_id = $this->input->post('message_in_id');
        $message_in_data = [
            'flag'=> (int)$this->input->post('message_in_flag'),
            'title'=> $this->input->post('message_in_title'),
            'detail'=> $this->input->post('message_in_detail')
        ];
        $this->load->database();
        if ($message_in_id) {
            $message_in_data['updated_at'] = $now_date;
            $this->db->where('id', $message_in_id);
            $this->db->update('message_title_data', $message_in_data);
        } else {
            $message_in_data['type'] = 'in';
            $message_in_data['created_at'] = $now_date;
            $message_in_data['updated_at'] = $now_date;
            $this->db->set($message_in_data);
            $this->db->insert('message_title_data');
        }
        $message_out_id = $this->input->post('message_out_id');
        $message_out_data = [
            'flag'=> $this->input->post('message_out_flag'),
            'title'=> $this->input->post('message_out_title'),
            'detail'=> $this->input->post('message_out_detail')
        ];
        if ($message_out_id) {
            $message_out_data['updated_at'] = $now_date;
            $this->db->where('id', $message_out_id);
            $this->db->update('message_title_data', $message_out_data);
        } else {
            $message_out_data['type'] = 'out';
            $message_out_data['created_at'] = $now_date;
            $message_out_data['updated_at'] = $now_date;
            $this->db->set($message_out_data);
            $this->db->insert('message_title_data');
        }
        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_04()
    {
        $group_title = $this->input->post('group_title');
        foreach ($group_title as $key => $value) {
            $group_title_data['id'] = $key+1;
            if (!$value) {
                $group_title_data['title'] = null;
            }
            $group_title_data['title'] = $value;
            $this->load->model('model_group_title');
            if ($this->model_group_title->update_data($group_title_data)) {
                $message = 'ok';
            } else {
                $message = 'err_update';
            }
        }
        $this->load->model('model_group');
        $group_item = $this->input->post('group_item');
        foreach ($group_item as $key => $value) {
            foreach ($value as $i => $item_name) {
                $group_item[$key][$i+1] = $item_name;
            }
        }
        $group_order = $this->input->post('group_order');
        foreach ($group_order as $key => $value) {
            $data = [];
            foreach ($value as $i => $id) {
                $data['id'] = $id;
                $data['group_name'] = $group_item[$key][$id];
                $data['state'] = 1;
                $data['group_order'] = $i+1;
                if ($key === 0) {
                    if ($this->model_group->get_group1_id($id)) {
                        $this->model_group->update_group1($data);
                    } else {
                        $this->model_group->insert_group1($data);
                    }
                }
                if ($key === 1) {
                    if ($this->model_group->get_group2_id($id)) {
                        $this->model_group->update_group2($data);
                    } else {
                        $this->model_group->insert_group2($data);
                    }
                }
                if ($key === 2) {
                    if ($this->model_group->get_group3_id($id)) {
                        $this->model_group->update_group3($data);
                    } else {
                        $this->model_group->insert_group3($data);
                    }
                }
            }
        }

        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function del_item_page_04()
    {
        $group_id = $this->input->post('group_id');
        $item_id = $this->input->post('item_id');
        $data['id'] = $item_id;
        $data['state'] = 2;
        $this->load->model('model_group');
        if ((int)$group_id === 1) {
            if ($this->model_group->get_group1_id($item_id)) {
                $this->model_group->update_group1($data);
            }
        }
        if ((int)$group_id === 2) {
            if ($this->model_group->get_group2_id($item_id)) {
                $this->model_group->update_group2($data);
            }
        }
        if ((int)$group_id === 3) {
            if ($this->model_group->get_group3_id($item_id)) {
                $this->model_group->update_group3($data);
            }
        }

        $callback = [
        'message'=>'ok'
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function get_loginuser_data_page_05()
    {
        $id = $this->input->post('id');
        $this->load->model('model_login');
        $data = $this->model_login->get_id_data($id);

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function save_page_05()
    {
        $flag = $this->input->post('flag');
        $data['id'] = $this->input->post('id');
        $data['login_id'] = $this->input->post('login_id');
        $data['password'] = $this->input->post('password');
        $data['user_name'] = $this->input->post('user_name');
        $data['authority'] = $this->input->post('authority');
        $data['area_id'] = $this->input->post('area_id');
        $this->load->model('model_login');

        $message = '';
        $login_check = $this->model_login->check_login_id($data['login_id']); // login_id重複チェック
        if ($login_check) {
            if ($login_check->id != $data['id']) {
                $message = 'err_id';
            }
        }
        if ($flag === 'edit' && !$message) {
            if ($data['password']) {
                $options = array('cost' => 10);
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT, $options);
            } else {
                unset($data['password']);
            }
            if ($this->model_login->update_loginuser($data)) {
                $message = 'ok';
            } else {
                $message = 'err_update';
            }
        }
        if ($flag === 'add' && !$message) {
            if ($this->model_login->insert_loginuser($data)) {
                $message = 'ok';
            } else {
                $message = 'err_update';
            }
        }
        $callback = [
            'message'=> $message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function del_page_05()
    {
        $id = $this->input->post('id');
        $this->load->model('model_login');
        if ($this->model_login->del_loginuser($id)) {
            $message = 'ok';
        } else {
            $message = 'err_update';
        }
        $callback = [
            'message'=> $message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function get_area_data_page_06()
    {
        $this->load->model('model_area_data');
        $where = ['id'=>(int)$this->input->post('id')];
        $data = $this->model_area_data->find_row('id, area_name, host_ip', $where);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function save_page_06()
    {
        $flag = $this->input->post('flag');
        $area_name = $this->input->post('area_name');
        $host_ip = $this->input->post('host_ip');

        $message = '';
        $this->load->model('model_area_data');
        $areaData = [
            'area_name' => $area_name,
            'host_ip' => $host_ip
        ];
        if ($this->model_area_data->find_row('id', $areaData)) {
            $message = 'err_id';
        }
        if ($flag === 'edit' && $message === '') {
            $message = 'err_update';
            if ($this->model_area_data->update((int)$this->input->post('id'), $areaData) !== FALSE) {
                $message = 'ok';
            }
        }
        if ($flag === 'add' && $message === '') {
            $message = 'err_insert';
            $this->model_area_data->area_name = $area_name;
            $this->model_area_data->host_ip = $host_ip;
            if ($this->model_area_data->insert() !== FALSE) {
                $message = 'ok';
            }
        }

        $callback = [
            'message'=> $message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function del_page_06()
    {
        $this->load->model('model_area_data');
        $message = 'err_delete';
        if ($this->model_area_data->delete($this->input->post('id')) !== FALSE) {
            $message = 'ok';
        }

        $callback = [
            'message'=> $message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_07()
    {
        $saveData = $this->input->post('saveData');
        $this->load->model('model_config_values');
        foreach ($saveData as $value) {
            $id = (int)$value['id'];
            $confData['value'] = $value['value'];
            $this->model_config_values->update($id, $confData);
        }
        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_08()
    {
        $saveData = $this->input->post('saveData');
        $this->load->model('model_config_values');
        foreach ($saveData as $value) {
            $id = (int)$value['id'];
            $confData['value'] = $value['value'];
            $this->model_config_values->update($id, $confData);
        }
        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_09()
    {
        $saveData = $this->input->post('saveData');
        $this->load->model('model_config_values');
        foreach ($saveData as $value) {
            $id = (int)$value['id'];
            $confData['value'] = $value['value'];
            $this->model_config_values->update($id, $confData);
        }
        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_10()
    {
        $this->load->model('model_notice_data_bk');
        $data = [];
        $notice_order = [];
        $notice_order = $this->input->post('notice_order');
        $index = 1;
        $group = 1;
        foreach ($notice_order as $value) {
            if ($value !== 'none') {
                foreach ($value as $val) {
                    $data['id'] = substr($val, 7);
                    $data['order'] = $index;
                    $data['group'] = $group;
                    if ($this->model_notice_data_bk->update_notice_status($data)) {
                        $message = 'ok';
                    } else {
                        $message = 'err_update';
                    }
                    $index++;
                }
            }
            $group++;
        }
        $data = [];
        $notice_status_data = [];
        $notice_status_data = $this->input->post('notice_status_data');
        foreach ((array)$notice_status_data as $key => $value) {
            $data['id'] = $key;
            $data['status'] = $value;
            if ($this->model_notice_data_bk->update_notice_status($data)) {
                $message = 'ok';
            } else {
                $message = 'err_update';
            }
        }
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_12()
    {
        $saveData = $this->input->post('saveData');
        $this->load->model('model_config_values');
        foreach ($saveData as $value) {
            $id = (int)$value['id'];
            $confData['value'] = $value['value'];
            $this->model_config_values->update($id, $confData);
        }
        $message = 'ok';
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_16()
    {
        $pay_flag = $this->input->post('pay_flag');

        $this->load->database();
        $update = $this->db->where('config_name', 'pay_flag')->update('config_values', ['value'=> $pay_flag]);
        $callback['message'] = $update ? 'ok' : 'ng';
        $this->output->set_content_type('application/json')->set_output(json_encode($callback));
    }
}
