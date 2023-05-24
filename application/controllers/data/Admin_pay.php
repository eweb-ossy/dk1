<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_pay extends CI_Controller
{
    // 給与データ取得
    public function getPayData()
    {
        $this->load->database();
        // 給与タイトルデータ取得
        $result = $this->db->query("SELECT `field`, `title`, `status`, `hozAlign`, `type`, `formatter` FROM `payment_title` ORDER BY `order` ASC")->result();
        foreach ($result as $key => $value) {
            if ($value->type == 1) {
                $data['column'][] = [
                    'field' => $value->field,
                    'title' => $value->title,
                    'hozAlign' => $value->hozAlign,
                    'formatter' => $value->formatter ?: 'plaintext',
                    'formatterParams' => [
                        'precision' => false
                    ]
                ];
            }
        }
        $data['title'] = array_column($result, NULL, 'field');

        // 給与データ
        $data['data'] = $this->db->query("SELECT payment_data.id, payment_data.user_id, `year`, `month`, work1, work2, work3, work4, work5, work6, work7, work8, work9, work10, work11, work12, work13, work14, pay1, pay2, pay3, pay4, pay5, pay6, pay7, pay8, pay9, pay10, pay11, pay12, pay13, pay14, deduct1, deduct2, deduct3, deduct4, deduct5, deduct6, deduct7, deduct8, deduct9, deduct10, deduct11, deduct12, deduct13, deduct14, total1, total2, total3, total4, total5, total6, total7, payment_data.memo, CONCAT(name_sei, ' ', name_mei) AS `name`, `open`, `download` FROM `payment_data` JOIN `user_data` ON payment_data.user_id = user_data.user_id")->result();

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    // 給与ファイルアップロード
    public function uploadFile()
    {
        $data = $this->input->post('data');
        $columns = array_column(array_shift($data), null);
        function check($str) {
            return str_replace("\r", '', $str);
        }
        $columns = array_map('check', $columns);
        $callback = [];
        if ($data) {
            $this->load->database();
            $result = $this->db->query("SELECT `field`, `title`, `status` FROM payment_title")->result();
            $payment_id_data = array_column($result, NULL, 'title');
            foreach ($data as $key => $value) {
                $save_data = [];
                foreach ($value as $k => $val) {
                    if (isset($columns[$k])) {
                        if (isset($payment_id_data[$columns[$k]])) {
                            $save_data[$payment_id_data[$columns[$k]]->field] = $val;
                        }
                    }
                }
                if (isset($save_data['user_id']) && isset($save_data['year']) && isset($save_data['month'])) {
                    $row = $this->db->query("SELECT `id` FROM `payment_data` WHERE `user_id` = '{$save_data['user_id']}' AND `year` = '{$save_data['year']}' AND `month` = '{$save_data['month']}'")->row();
                    if ($row) {
                        $id = $row->id;
                        $this->db->where('id', $id);
                        $callback[] = $this->db->update('payment_data', $save_data);
                    } else {
                        $callback[] = $this->db->insert('payment_data', $save_data);
                    }
                }
            }
        }

        $response = [
            'callback'=> $callback
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
    }

    // 公開データアップデート保存
    public function updateOpen()
    {
        $id = $this->input->post('id');
        $val = $this->input->post('val');

        $this->load->database();
        $this->db->where('id', $id);
        $callback = $this->db->update('payment_data', ['open'=> $val]);
        $response = [
            'callback'=> $callback
        ];
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
    }



//     public function table_data()
//     {
//         // config data取得
//         $this->load->model('model_config_values');
//         $where = [];
//         $result = $this->model_config_values->find('id, config_name, value', $where, '');
//         $config_data = array_column($result, 'value', 'config_name');

//         $year = $this->input->post('year');
//         $month = $this->input->post('month');
//         $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//         if ((int)$config_data['end_day'] > 0) { // 月末締め日以外の場合
//             $pre = new DateTime($year.'-'.$month.'-01');
//             $pre->sub(DateInterval::createFromDateString('1 month')); // １ヶ月前
//             $pre_year = $pre->format('Y');
//             $pre_month = $pre->format('m');
//             $pre_month_days = cal_days_in_month(CAL_GREGORIAN, $pre_month, $pre_year);
//             $first_date = sprintf('%04d-%02d-%02d', $pre_year, $pre_month, (int)$config_data['end_day'] + 1);
//             $end_date = sprintf('%04d-%02d-%02d', $year, $month, (int)$config_data['end_day']);
//         } else { // 月末締めの場合
//             $first_date = sprintf('%04d-%02d-%02d', $year, $month, 1);
//             $end_date = sprintf('%04d-%02d-%02d', $year, $month, $month_days);
//         }

//         $this->load->model('model_group_title'); // グループタイトル
//         $result = $this->model_group_title->gets_data();
//         foreach ($result as $row) {
//             $group_title[$row->group_id] = $row->title;
//         }
//         $this->load->model('model_group');
//         $result = $this->model_group->find_group1_all();
//         foreach ($result as $row) {
//             $group1_name[$row->id] = $row->group_name;
//             $group1_order[$row->id] = $row->group_order;
//         }
//         $result = $this->model_group->find_group2_all();
//         foreach ($result as $row) {
//             $group2_name[$row->id] = $row->group_name;
//             $group2_order[$row->id] = $row->group_order;
//         }
//         $result = $this->model_group->find_group3_all();
//         foreach ($result as $row) {
//             $group3_name[$row->id] = $row->group_name;
//             $group3_order[$row->id] = $row->group_order;
//         }
//         $this->load->model('model_group_history');
//         $result = $this->model_group_history->find_all();
//         foreach ($result as $row) {
//             $group1_id[$row->user_id] = $row->group1_id;
//             $group2_id[$row->user_id] = $row->group2_id;
//             $group3_id[$row->user_id] = $row->group3_id;
//         }

//         // get time data 
//         $month_time_data = [];
//         $all_time_data = [];
//         $this->load->model('model_time');
//         $month_time_data = $this->model_time->gets_first_to_end_date_fact_work_hour($first_date, $end_date);
//         $all_time_data = $this->model_time->gets_to_end_date_fact_work_hour($end_date);

//         // get 有給データ
//         $month_status_data = [];
//         $all_status_data = [];
//         $this->load->model('model_shift');
//         $month_status_data = $this->model_shift->gets_first_to_end_date_status($first_date, $end_date);
//         $all_status_data = $this->model_shift->gets_to_end_date_status($end_date);

//         $this->load->model('model_user');
//         $users = $this->model_user->gets_state_all();
//         $users_data = [];
//         foreach ($users as $user) {
//             $user_id = $user->user_id;
//             $start_month = '';
//             $entry_year = '';
//             $entry_month = '';
//             $entry_day = '';
//             if ($user->entry_date === null || $user->entry_date === '0000-00-00') {
//                 $entry_date_w = '';
//                 $interval = '';
//             } else {
//                 $now_date = new DateTime();
//                 $entry_date = new DateTime($user->entry_date);
//                 $entry_year = $entry_date->format('Y');
//                 $entry_month = $entry_date->format('m');
//                 $entry_day = $entry_date->format('d');
//                 $entry_date_w = $entry_date->format('Y年m月d日');
//                 if ($entry_date < $now_date) {
//                     $diff_year = (int)$entry_date->diff($now_date)->format('%Y') === 0 ? '' : (int)$entry_date->diff($now_date)->format('%Y').'年';
//                     $diff_month = (int)$entry_date->diff($now_date)->format('%m') === 0 ? '' : (int)$entry_date->diff($now_date)->format('%m').'ヶ月';
//                     $interval = $diff_year.$diff_month;
//                     $start_month = $entry_year . '年' . $entry_month . '月';
//                 } else {
//                     $interval = '入社前';
//                 }
//             }

//             // time data 
//             $filters = [
//                 'user_id' => [$user_id]
//             ];
//             $filter_func = function($filters) {
//                 return function($value) use($filters) {
//                     return in_array($value->user_id, $filters['user_id']);
//                 };
//             };
//             if ($month_time_data) {
//                 $user_time_data =  array_filter($month_time_data, $filter_func($filters));
//                 $work_time_data = array_column($user_time_data, 'fact_work_hour');
//                 $work_hour = array_sum($work_time_data);
//                 $work_num = count($work_time_data);
//                 $work_time = $work_hour > 0 ? sprintf("%d:%02d", floor($work_hour/60), $work_hour%60) : 0;
//             } else {
//                 $work_num = 0;
//                 $work_time = '';
//                 $work_hour = 0;
//             }
//             if ($all_time_data) {
//                 $user_all_time_data = array_filter($all_time_data, $filter_func($filters));
//                 $all_work_time_data = array_column($user_all_time_data, 'fact_work_hour');
//                 $total_work_hour = array_sum($all_work_time_data);
//                 $total_work_num = count($all_work_time_data);
//                 $total_work_time = $total_work_hour > 0 ? sprintf("%d:%02d", floor($total_work_hour/60), $total_work_hour%60) : 0;
//             } else {
//                 $total_work_num = 0;
//                 $total_work_time = '';
//                 $total_work_hour = 0;
//             }

//             // paid data  
//             if ($month_status_data) {
//                 $user_status_data = array_filter($month_status_data, $filter_func($filters));
//                 $paid_data = array_column($user_status_data, 'status');
//                 $paid = count($paid_data);
//             } else {
//                 $paid = 0;
//             }
//             if ($all_status_data) {
//                 $user_all_status_data = array_filter($all_status_data, $filter_func($filters));
//                 $all_paid_data = array_column($user_all_status_data, 'status');
//                 $all_paid = count($all_paid_data);
//             } else {
//                 $all_paid = 0;
//             }

//             $users_data[] = [
//                 'user_id'=>str_pad($user_id, (int)$config_data['id_size'], '0', STR_PAD_LEFT),
//                 'user_name'=>$user->name_sei.' '.$user->name_mei,
//                 'group1_name'=>@$group1_name[$group1_id[$user_id]] ?: '',
//                 'group2_name'=>@$group2_name[$group2_id[$user_id]] ?: '',
//                 'group3_name'=>@$group3_name[$group3_id[$user_id]] ?: '',
//                 'entry_date'=>$entry_date_w,
//                 'interval' => $interval,
//                 'start_month' => $start_month,
//                 'paid_month' => $entry_month ? (int)$entry_month.'月' : '',
//                 'paid' => $paid,
//                 'work_num' => $work_num,
//                 'work_time' => $work_time,
//                 'total_work_num' => $total_work_num,
//                 'total_work_time' => $total_work_time,
//                 'work_hour' => $work_hour,
//                 'total_work_hour' => $total_work_hour
//             ];
//         }
//         // output
//         $this->output
//         ->set_content_type('application/json')
//         ->set_output(json_encode($users_data));
//     }
}
