<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_user_detail extends CI_Controller
{
    public function user_data()
    {
        $user_id = $this->input->post('user_id');
        $this->load->model('model_user');
        $user_data = $this->model_user->find_all_userid($user_id);
        $this->load->model('model_group_history');
        $group_data = $this->model_group_history->find_all_userid($user_id);
        $this->load->model('model_group_title');
        $group_title = $this->model_group_title->gets_data();
        $this->load->model('model_group');
        $group1 = $this->model_group->find_group1_all();
        $group2 = $this->model_group->find_group2_all();
        $group3 = $this->model_group->find_group3_all();
        $data = [
            'id'=>$user_data->id,
            'name_sei'=>$user_data->name_sei,
            'name_mei'=>$user_data->name_mei,
            'kana_sei'=>$user_data->kana_sei,
            'kana_mei'=>$user_data->kana_mei,
            'user_id'=>$user_data->user_id,
            'state'=>$user_data->state,
            'entry_date'=>$user_data->entry_date,
            'resign_date'=>$user_data->resign_date,
            'birth_date'=>$user_data->birth_date,
            'zip_code'=>$user_data->zip_code,
            'address'=>$user_data->address,
            'sex'=>$user_data->sex,
            'memo'=>$user_data->memo,
            'phone_number1'=>$user_data->phone_number1,
            'phone_number2'=>$user_data->phone_number2,
            'email1'=>$user_data->email1,
            'email2'=>$user_data->email2,
            'group_history'=>$group_data,
            'group_title'=>$group_title,
            'group1'=>$group1,
            'group2'=>$group2,
            'group3'=>$group3,
            'aporan_flag'=>$user_data->aporan_flag,
            'advance_pay_flag'=>$user_data->advance_pay_flag,
            'shift_alert_flag'=>(int)$user_data->shift_alert_flag,
            'management_flag'=>(int)$user_data->management_flag,
            'esna_pay_flag'=>(int)$user_data->esna_pay_flag,
            'api_output'=>(int)$user_data->api_output,
            'input_confirm_flag'=>(int)$user_data->input_confirm_flag
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function group_data()
    {
        $this->load->model('model_group_title');
        $group_title = $this->model_group_title->gets_data();
        $this->load->model('model_group');
        $group1 = $this->model_group->find_group1_all();
        $group2 = $this->model_group->find_group2_all();
        $group3 = $this->model_group->find_group3_all();
        $data = [
            'group_title'=>$group_title,
            'group1'=>$group1,
            'group2'=>$group2,
            'group3'=>$group3,
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function table_notice_data()
    {
        $user_id = $this->input->post('user_id');
        $now = new DateTime();
        $now_date = $now->format('Y-m-d');
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('id, config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        $this->load->model('model_group_title'); // グループタイトル
        $result = $this->model_group_title->gets_data();
        foreach ($result as $row) {
            $group_title[$row->group_id] = $row->title;
        }
        $this->load->model('model_group');
        $result = $this->model_group->find_group1_all();
        foreach ($result as $row) {
            $group1_name[$row->id] = $row->group_name;
        }
        $result = $this->model_group->find_group2_all();
        foreach ($result as $row) {
            $group2_name[$row->id] = $row->group_name;
        }
        $result = $this->model_group->find_group3_all();
        foreach ($result as $row) {
            $group3_name[$row->id] = $row->group_name;
        }
        $this->load->model('model_group_history');
        $result = $this->model_group_history->find_all();
        foreach ($result as $row) {
            if (new DateTime($row->to_date) <= new DateTime()) {
                $group1_id[$row->user_id] = $row->group1_id;
                $group2_id[$row->user_id] = $row->group2_id;
                $group3_id[$row->user_id] = $row->group3_id;
            }
        }
        $this->load->model('model_notice_data_bk');
        $auth_data = $this->model_notice_data_bk->gets_auth($user_id);
        $auth_data = array_column($auth_data, 'low_user_id');
        $this->load->model('model_user');
        $users = $this->model_user->find_exist_all($now_date);
        $users_data = [];
        foreach ($users as $user) {
            $userid = $user->user_id;
            if ($user_id == $userid) {
                continue;
            }
            if (in_array($user->user_id, $auth_data)) {
                $auth = true;
            } else {
                $auth = false;
            }
            $permit_data = $this->model_notice_data_bk->get_permit($user_id, $user->user_id);
            if ($permit_data) {
                if ($permit_data->permit == 1) {
                    $permit = true;
                } else {
                    $permit = false;
                }
            } else {
                $permit = false;
            }
            $users_data[] = [
                'id'=> str_pad($userid, (int)$config_data['id_size'], '0', STR_PAD_LEFT),
                'user_id'=>str_pad($userid, (int)$config_data['id_size'], '0', STR_PAD_LEFT),
                'user_name'=>$user->name_sei.' '.$user->name_mei,
                'group1_name'=>@$group1_name[$group1_id[$userid]] ?: '',
                'group2_name'=>@$group2_name[$group2_id[$userid]] ?: '',
                'group3_name'=>@$group3_name[$group3_id[$userid]] ?: '',
                'notice' => $auth,
                'permit' => $permit
            ];
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($users_data));
    }

    public function user_id_check() {
        $user_id = (int)$this->input->post('user_id');
        $this->load->database();
        $row = $this->db->query("SELECT id FROM user_data WHERE `user_id` = {$user_id}")->row();
        $user_id = $row ? $row->id : FALSE;
        $this->output->set_output($user_id);
    }

    public function save_page_01()
    {
        $flag = $this->input->post('flag');
        $data['user_id'] = $this->input->post('user_id');
        $data['name_sei'] = $this->input->post('name_sei');
        $data['name_mei'] = $this->input->post('name_mei');
        $data['kana_sei'] = $this->input->post('kana_sei');
        $data['kana_mei'] = $this->input->post('kana_mei');
        $data['state'] = $this->input->post('state');
        $data['sex'] = $this->input->post('sex');
        $data['shift_alert_flag'] = $this->input->post('shift_alert_flag');
        $data['management_flag'] = $this->input->post('management_flag');
        $data['input_confirm_flag'] = $this->input->post('input_confirm_flag');
        if ($this->input->post('entry_date')) {
            $data['entry_date'] = $this->input->post('entry_date');
        } else {
            $data['entry_date'] = null;
        }
        if ($this->input->post('resign_date')) {
            $data['resign_date'] = $this->input->post('resign_date');
        } else {
            $data['resign_date'] = null;
        }
        if ($data['state'] == 1) {
            $data['resign_date'] = 'none';
        }
        if ($this->input->post('user_password')) {
            $password = $this->input->post('user_password');
            $options = array('cost' => 10);
            $data['password'] = password_hash($password, PASSWORD_DEFAULT, $options);
        }
        $this->load->model('model_user');
        $this->load->model('model_group_history');
        if ($flag === 'edit') { // update
            $data['id'] = $this->input->post('id');
            if ($this->model_user->update_data($data)) {
                $message = 'ok_edit';
            } else {
                $message = 'err_update';
            }
        }
        if ($flag === 'new') {
            if (!$this->model_user->find_id($data['user_id'])) {
                if (isset($data['password']) === false) {
                    $password = $data['user_id'];
                    $options = array('cost' => 10);
                    $data['password'] = password_hash($password, PASSWORD_DEFAULT, $options);
                }
                $data['birth_date'] = null;
                $data['phone1'] = null;
                $data['phone2'] = null;
                $data['zip_code'] = null;
                $data['address'] = '';
                $data['sex'] = null;
                $data['memo'] = '';
                $data['email1'] = null;
                $data['email2'] = null;
                $data['paid_vacation_month'] = null;
                if ($this->model_user->insert_user($data)) {
                    $message = 'ok_create';
                } else {
                    $message = 'err_insert';
                }
                $group_data['user_id'] = $this->input->post('user_id');
                $group_data['group1_id'] = NULL;
                $group_data['group2_id'] = NULL;
                $group_data['group3_id'] = NULL;
                if ($data['entry_date'] === null) {
                    $now = new DateTime();
                    $group_data['to_date'] = $now->format('Y-m-d');
                } else {
                    $group_data['to_date'] = $data['entry_date'];
                }
                if ($this->model_group_history->insert_group($group_data)) {
                    $message = 'ok_create';
                } else {
                    $message = 'err_insert';
                }
            } else {
                $message = 'err_ip';
            }
        }
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_02()
    {
        $data['user_id'] = $this->input->post('user_id');
        $group_history_id = $this->input->post('group_history_id');
        $group_history_to_date = $this->input->post('group_history_to_date');
        $group_history_groups = $this->input->post('group_history_groups');

        //
        $data['entry_date'] = min($group_history_to_date);
        $data['id'] = $this->input->post('id');
        $this->load->model('model_user');
        if ($this->model_user->update_data($data)) {
            $message = 'ok';
        } else {
            $message = 'err';
        }

        $this->load->model('model_group_history');
        $history_count = count($group_history_id);
        foreach ($group_history_id as $key => $value) {
            $data['id'] = $value;
            $data['to_date'] = $group_history_to_date[$key];
            $data['group1_id'] = $group_history_groups[$key][0];
            $data['group2_id'] = $group_history_groups[$key][1];
            $data['group3_id'] = $group_history_groups[$key][2];
            if ($value !== 'new') {
                if ($this->model_group_history->update($data)) {
                    $message = 'ok';
                } else {
                    $message = 'err';
                }
            } else {
                if ($this->model_group_history->insert_group($data)) {
                    $message = 'ok';
                } else {
                    $message = 'err';
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

    public function del_page_02()
    {
        $group_history_id = (int)$this->input->post('id');
        $this->load->model('model_group_history');
        if ($this->model_group_history->del($group_history_id)) {
            $message = 'ok_del';
        } else {
            $message = 'err';
        }
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_03()
    {
        $data['id'] = $this->input->post('id');
        $data['birth_date'] = $this->input->post('birth_date');
        $data['phone_number1'] = $this->input->post('phone_number1');
        $data['phone_number2'] = $this->input->post('phone_number2');
        $data['email1'] = $this->input->post('email1');
        $data['email2'] = $this->input->post('email2');
        $data['zip_code'] = $this->input->post('zip_code');
        $data['address'] = $this->input->post('address');
        $this->load->model('model_user');
        $data['id'] = $this->input->post('id');
        if ($this->model_user->update_data($data)) {
            $message = 'ok_edit';
        } else {
            $message = 'err_update';
        }
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_04()
    {
        $data['user_id'] = $this->input->post('user_id');
        $notice_data = $this->input->post('notice_data');
        $permit_data = $this->input->post('permit_data');
        $this->load->model('model_notice_data_bk');
        if ($this->model_notice_data_bk->del_auth($data['user_id'])) {
            $message = 'ok';
        } else {
            $message = 'del_err';
        }
        foreach ((array)$notice_data as $val) {
            $data['low_user_id'] = $val;
            $data['permit'] = 0;
            if ($this->model_notice_data_bk->insert_auth($data)) {
                $message = 'ok';
            }
        }
        foreach ((array)$permit_data as $val) {
            $data['low_user_id'] = $val;
            $data['permit'] = 1;
            if ($this->model_notice_data_bk->update_auth_permit($data)) {
                $message = 'ok';
            }
        }
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_05()
    {
        $data['id'] = $this->input->post('id');
        $data['aporan_flag'] = $this->input->post('aporan_flag');
        $data['advance_pay_flag'] = $this->input->post('advance_pay_flag');
        $data['esna_pay_flag'] = $this->input->post('esna_pay_flag');
        $data['api_output'] = $this->input->post('api_output');
        $this->load->model('model_user');
        if ($this->model_user->update_data($data)) {
            $message = 'ok';
        } else {
            $message = 'err';
        }
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_page_99()
    {
        $user_id = $this->input->post('user_id');
        $this->load->model('model_user');
        if ($this->model_user->delete_low_user($user_id)) {
            $message = 'ok';
        } else {
            $message = 'del_err';
        }
        if ($this->model_user->delete_user($user_id)) {
            $message = 'ok';
        } else {
            $message = 'del_err';
        }
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function save_mypage()
    {
        $data['id'] = $this->input->post('id');
        $data['name_sei'] = $this->input->post('name_sei');
        $data['name_mei'] = $this->input->post('name_mei');
        $data['kana_sei'] = $this->input->post('kana_sei');
        $data['kana_mei'] = $this->input->post('kana_mei');
        $data['sex'] = $this->input->post('sex');
        $data['zip_code'] = $this->input->post('zip_code');
        $data['address'] = $this->input->post('address');
        $data['phone_number1'] = $this->input->post('phone_number1');
        $data['phone_number2'] = $this->input->post('phone_number2');
        $data['email1'] = $this->input->post('email1');
        $data['email2'] = $this->input->post('email2');
        if ($this->input->post('birth_date')) {
            $data['birth_date'] = $this->input->post('birth_date');
        } else {
            $data['birth_date'] = null;
        }
        if ($this->input->post('user_password')) {
            $password = $this->input->post('user_password');
            $options = array('cost' => 10);
            $data['password'] = password_hash($password, PASSWORD_DEFAULT, $options);
            $now = new DateTime();
            $now_date = $now->format('Y-m-d');
            $data['password_change'] = $now_date;
        }
        $this->load->model('model_user');
        if ($this->model_user->update_data($data)) {
            $message = 'ok_edit';
        } else {
            $message = 'err_update';
        }
        $callback = [
            'message'=>$message
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }

    public function getNextUserId()
    {
        $this->load->database();
        $row = $this->db->query("SELECT MAX(`user_id`) AS maxUserID FROM `user_data`")->row();

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($row));
    }
}
