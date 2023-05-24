<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_to extends CI_Controller
{
    // table表示用データを返す
    public function table_data()
    {
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
        $this->load->model('model_user');
        $users = $this->model_user->gets_all();
        $users_data = [];
        foreach ($users as $user) {
            if ((int)$user->aporan_flag === 0) {
                $aporan_flag = false;
            } else {
                $aporan_flag = true;
            }
            if ((int)$user->advance_pay_flag === 0) {
                $advance_pay_flag = false;
            } else {
                $advance_pay_flag = true;
            }
            if ((int)$user->esna_pay_flag === 0) {
                $esna_pay_flag = false;
            } else {
                $esna_pay_flag = true;
            }
            if ((int)$user->api_output === 0) {
                $api_output = false;
            } else {
                $api_output = true;
            }
            $users_data[] = [
                'user_id'=>str_pad($user->user_id, (int)$config_data['id_size'], '0', STR_PAD_LEFT),
                'user_name'=>$user->name_sei.' '.$user->name_mei,
                'group1_name'=>@$group1_name[$group1_id[$user->user_id]] ?: '',
                'group2_name'=>@$group2_name[$group2_id[$user->user_id]] ?: '',
                'group3_name'=>@$group3_name[$group3_id[$user->user_id]] ?: '',
                'aporan' => $aporan_flag,
                'advance_pay' => $advance_pay_flag,
                'state' => (int)$user->state,
                'esna_pay_flag' => $esna_pay_flag,
                'user_api_output_flag' => $api_output
            ];
        }
        // output
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($users_data));
    }

    // save data
    public function save_data()
    {
        $user_id = $this->input->post('user_id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        $this->load->model('model_user');
        $data['id'] = $this->model_user->find_all_userid($user_id)->id;
        if ($field === 'aporan') {
            if ($value === 'true') {
                $data['aporan_flag'] = 1;
            } else {
                $data['aporan_flag'] = 0;
            }
        }
        if ($field === 'advance_pay') {
            if ($value === 'true') {
                $data['advance_pay_flag'] = 1;
            } else {
                $data['advance_pay_flag'] = 0;
            }
        }
        if ($field === 'esna_pay_flag') {
            if ($value === 'true') {
                $data['esna_pay_flag'] = 1;
            } else {
                $data['esna_pay_flag'] = 0;
            }
        }
        if ($field === 'user_api_output_flag') {
            if ($value === 'true') {
                $data['api_output'] = 1;
            } else {
                $data['api_output'] = 0;
            }
        }
        $this->model_user->update_data($data);
    }
}
