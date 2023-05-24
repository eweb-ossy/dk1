<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_list_user extends CI_Controller
{
    public function table_data()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $end_date = sprintf('%04d-%02d-%02d', $year, $month, $month_days);
        $to_month = $year.$month;
        $end_day = (int)$this->input->post('end_day'); // 締め日

        $user_id = $this->input->post('user_id'); // Mypageから

        if ($end_day > 0) {
            $pre_date = new DateTime($year.'-'.$month.'-01');
            $pre_date->sub(DateInterval::createFromDateString('1 month')); // １ヶ月前
            $pre_year = $pre_date->format('Y');
            $pre_month = $pre_date->format('m');
            $pre_day = $end_day + 1;
            $first_date = new DateTime($pre_year.'-'.$pre_month.'-'.$pre_day);
            $end_date = new DateTime($year.'-'.$month.'-'.$pre_day);
        } else {
            $first_date = new DateTime($year.'-'.$month.'-01');
            $end_date = new DateTime($year.'-'.$month.'-'.$month_days);
        }
        $first_date = $first_date->format('Y-m-d');
        $end_date = $end_date->format('Y-m-d');
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('id, config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');
        // get group title
        $this->load->model('model_group_title');
        $result = $this->model_group_title->gets_data();
        foreach ($result as $row) {
            $group_title[$row->group_id] = $row->title;
        }
        // get group name
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
        $this->load->model('model_time');
        $result = $this->model_time->gets_to_end_date_all($first_date, $end_date);
        if ($result) {
            foreach ($result as $hour_data) {
                if (isset($hour_data->fact_work_hour) && $hour_data->fact_work_hour > 0) {
                    $fact_hour_data[$hour_data->user_id][] = $hour_data->fact_work_hour;
                }
                if (isset($hour_data->over_hour) && $hour_data->over_hour > 0) {
                    $over_hour_data[$hour_data->user_id][] = $hour_data->over_hour;
                }
                if (isset($hour_data->night_hour) && $hour_data->night_hour > 0) {
                    $night_hour_data[$hour_data->user_id][] = $hour_data->night_hour;
                }
                if (isset($hour_data->late_hour) && $hour_data->late_hour > 0) {
                    $late_hour_data[$hour_data->user_id][] = $hour_data->late_hour;
                }
                if (isset($hour_data->left_hour) && $hour_data->left_hour > 0) {
                    $left_hour_data[$hour_data->user_id][] = $hour_data->left_hour;
                }
                if ($hour_data->status_flag == 29 || $hour_data->status_flag == 75) {
                    $paid_num[$hour_data->user_id][] = $hour_data->status_flag; // 有給日数
                }
                if ($hour_data->status_flag == 59) {
                    $absence_num[$hour_data->user_id][] = $hour_data->status_flag; // 欠勤日数
                }
                if ($hour_data->status_flag == 36 || $hour_data->status_flag == 44 || $hour_data->status_flag == 45 || $hour_data->status_flag == 63 || $hour_data->status_flag == 70) {
                    $oneside_num[$hour_data->user_id][] = $hour_data->status_flag; // 片打刻
                }
                if ($hour_data->status_flag == 21 || $hour_data->status_flag == 28 || $hour_data->status_flag == 35 || $hour_data->status_flag == 41 || $hour_data->status_flag == 62 || $hour_data->status_flag == 69 || $hour_data->status_flag == 76) {
                    $zero_num[$hour_data->user_id][] = $hour_data->status_flag; // 0H出勤
                }
            }
        }

        $data = $tmp = [];
        $i = 0;
        $this->load->model('model_user');

        if ($user_id) {
            $this->load->model('model_notice_data_bk');
            $auth_data = $this->model_notice_data_bk->gets_auth($user_id);
            $auth_data = array_column($auth_data, 'low_user_id');
            $users = $this->model_user->find_exist_month_listmonth_users($to_month, $end_date, $auth_data);
        } else {
            $users = $this->model_user->find_exist_month_listmonth($to_month, $end_date);
        }

        foreach ($users as $user) {
            $work_hour2 = $over_hour2 = $night_hour2 = $late_hour2 = $left_hour2 = 0;
            if (!in_array($user->user_id, $tmp)) {
                $tmp[] = $user->user_id;
            } else {
                continue;
            }
            $user_id = $user->user_id;
            if (isset($fact_hour_data[$user_id])) {
                $work_count = count($fact_hour_data[$user_id]);
                $work_hour = sprintf('%d:%02d', floor(array_sum($fact_hour_data[$user_id])/60), array_sum($fact_hour_data[$user_id])%60);
                $work_hour2 = array_sum($fact_hour_data[$user_id]);
            } else {
                $work_count = '';
                $work_hour = '';
            }
            if (isset($over_hour_data[$user_id])) {
                $over_count = count($over_hour_data[$user_id]);
                $over_hour = sprintf('%d:%02d', floor(array_sum($over_hour_data[$user_id])/60), array_sum($over_hour_data[$user_id])%60);
                if ((int)$config_data['over_time_view_flag'] == 1) {
                    $over_hour2 = array_sum($over_hour_data[$user_id]);
                }
            } else {
                $over_count = '';
                $over_hour = '';
            }
            if (isset($night_hour_data[$user_id])) {
                $night_count = count($night_hour_data[$user_id]);
                $night_hour = sprintf('%d:%02d', floor(array_sum($night_hour_data[$user_id])/60), array_sum($night_hour_data[$user_id])%60);
                if ((int)$config_data['night_time_view_flag'] == 1) {
                    $night_hour2 = array_sum($night_hour_data[$user_id]);
                }
            } else {
                $night_count = '';
                $night_hour = '';
            }
            if (isset($late_hour_data[$user_id])) {
                $late_count = count($late_hour_data[$user_id]);
                $late_hour = sprintf('%d:%02d', floor(array_sum($late_hour_data[$user_id])/60), array_sum($late_hour_data[$user_id])%60);
                $late_hour2 = array_sum($late_hour_data[$user_id]);
            } else {
                $late_count = '';
                $late_hour = '';
            }
            if (isset($left_hour_data[$user_id])) {
                $left_count = count($left_hour_data[$user_id]);
                $left_hour = sprintf('%d:%02d', floor(array_sum($left_hour_data[$user_id])/60), array_sum($left_hour_data[$user_id])%60);
                $left_hour2 = array_sum($left_hour_data[$user_id]);
            } else {
                $left_count = '';
                $left_hour = '';
            }
            if ($work_hour2 > 0 && isset($over_hour2)) {
                $normal_minute = $work_hour2 - $over_hour2 - $night_hour2;
                $normal_hour = sprintf('%d:%02d', floor($normal_minute/60), $normal_minute%60);
            } else {
                $normal_minute = 0;
                $normal_hour = '';
            }
            $data[$i] = [
                'user_id'=>str_pad($user_id, (int)$config_data['id_size'], '0', STR_PAD_LEFT),
                'user_name'=>$user->name_sei.' '.$user->name_mei
            ];
            if ($group_title[1]) {
                $data[$i] += [
                    'group1_name'=>@$group1_name[$user->group1_id] ?: ''
                ];
            }
            if ($group_title[2]) {
                $data[$i] += [
                    'group2_name'=>@$group2_name[$user->group2_id] ?: ''
                ];
            }
            if ($group_title[3]) {
                $data[$i] += [
                    'group3_name'=>@$group3_name[$user->group3_id] ?: ''
                ];
            }
            $data[$i] += [
                'work_count'=>$work_count
            ];
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                $data[$i] += [
                    'work_hour'=>$work_hour,
                ];
            }
            if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                $data[$i] += [
                    'work_minute'=> $work_hour2 > 0 ? $work_hour2 : '',
                ];
            }
            if ((int)$config_data['normal_time_flag'] === 1) {
                if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                    $data[$i] += [
                        'normal_hour'=>$normal_hour,
                    ];
                }
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$i] += [
                        'normal_minute'=> $normal_minute > 0 ? $normal_minute : '',
                    ];
                }
            }
            if ((int)$config_data['over_time_view_flag'] === 1) {
                $data[$i] += [
                    'over_count'=>$over_count
                ];
                if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                    $data[$i] += [
                        'over_hour'=>$over_hour
                    ];
                }
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$i] += [
                        'over_minute'=> $over_hour2 > 0 ? $over_hour2 : ''
                    ];
                }
            }
            if ((int)$config_data['night_time_view_flag'] === 1) {
                $data[$i] += [
                    'night_count'=>$night_count
                ];
                if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                    $data[$i] += [
                        'night_hour'=>$night_hour
                    ];
                }
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$i] += [
                        'night_minute'=> $night_hour2 > 0 ? $night_hour2 : ''
                    ];
                }
            }
            $data[$i] += [
                'late_count'=>$late_count
            ];
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                $data[$i] += [
                    'late_hour'=>$late_hour
                ];
            }
            if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                $data[$i] += [
                    'late_minute'=> $late_hour2 > 0 ? $late_hour2 : ''
                ];
            }
            $data[$i] += [
                'left_count'=>$left_count
            ];
            if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                $data[$i] += [
                    'left_hour'=>$left_hour
                ];
            }
            if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                $data[$i] += [
                    'left_minute'=> $left_hour2 > 0 ? $left_hour2 : ''
                ];
            }
            $data[$i] += ['paid_num' => isset($paid_num[$user_id]) ? count($paid_num[$user_id]) : ''];
            $data[$i] += ['absence_num' => isset($absence_num[$user_id]) ? count($absence_num[$user_id]) : ''];
            $data[$i] += ['oneside_num' => isset($oneside_num[$user_id]) ? count($oneside_num[$user_id]) : ''];
            $data[$i] += ['zero_num' => isset($zero_num[$user_id]) ? count($zero_num[$user_id]) : ''];

            $data[$i] += ['work_hour2' => $work_hour2];
            $data[$i] += ['over_hour2' => $over_hour2];
            $data[$i] += ['night_hour2' => $night_hour2];
            $data[$i] += ['late_hour2' => $late_hour2];
            $data[$i] += ['left_hour2' => $left_hour2];
            $data[$i] += ['normal_hour2' => $normal_minute];
            $i++;
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}
