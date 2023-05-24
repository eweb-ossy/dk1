<?php
/**
*   管理画面　日別集計　コントロール
*
*   @copyright  e-web,Inc.
*   @author     oshizawa
*/

defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_list_day extends MY_Controller
{

    /**
     *   list_day table表示用データを返す
    *   mypage_state table表示用データを返す
    *
    *   @param  string $from_date
    *   @param  int $user_id
    *   @return array $output_data
    */
    public function table_data()
    {
        $from_date = $this->input->post('date');
        $user_id = $this->input->post('user_id'); // Mypageから
        $date = new DateTimeImmutable($from_date);

        $this->load->model('model_get');
        $group_title = $this->model_get->group_title();

        $this->load->model('model_group');
        $result = $this->model_group->find_group1_all();
        foreach ($result as $row) {
            $group1_name[$row->id] = $row->group_name;
            $group1_order[$row->id] = $row->group_order;
        }
        $result = $this->model_group->find_group2_all();
        foreach ($result as $row) {
            $group2_name[$row->id] = $row->group_name;
            $group2_order[$row->id] = $row->group_order;
        }
        $result = $this->model_group->find_group3_all();
        foreach ($result as $row) {
            $group3_name[$row->id] = $row->group_name;
            $group3_order[$row->id] = $row->group_order;
        }
        $this->load->model('model_group_history');
        $result = $this->model_group_history->find_all();
        foreach ($result as $row) {
            if (new DateTimeImmutable($row->to_date) <= new DateTimeImmutable($from_date)) {
                $group1_id[$row->user_id] = $row->group1_id;
                $group2_id[$row->user_id] = $row->group2_id;
                $group3_id[$row->user_id] = $row->group3_id;
            }
        }

        if ((int)$this->data['configs']['area_flag']->value === 1) {
            // エリア名データ取得
            $area_name = [];
            $this->load->model('model_area_data');
            $where = [];
            $result = $this->model_area_data->find('id, area_name', $where, '');
            $area_name = array_column($result, 'area_name', 'id');
            // foreach ($result as $row) {
            //   $area_name[$row->id] = $row->area_name;
            // }
        }

        if ((int)$this->data['configs']['gps_flag']->value !== 0) {
            // GPSデータ取得
            $gps_data = [];
            $this->load->model('model_gps_data');
            $where = ['gps_date' => $from_date];
            $result = $this->model_gps_data->find('user_id, flag, latitude, longitude', $where, '');

            // $result = $this->model_gps_data->get_date_data($from_date);
            foreach ($result as $value) {
                $gps_data[$value->user_id][(int)$value->flag] = [
                    'latitude'=>$value->latitude,
                    'longitude'=>$value->longitude
                ];
            }
        }

        $users_data = [];
        $times_data = [];
        $this->load->model('model_user');
        $this->load->model('model_time');
        // $user_idがある場合、取得対象従業員IDを$auth_dataに定義
        if ($user_id) {
            $this->load->model('model_notice_data_bk');
            $auth_data = $this->model_notice_data_bk->gets_auth($user_id);
            $auth_data = array_column($auth_data, 'low_user_id');
            // 従業員データ取得 user_id指定
            $users_data = $this->model_user->gets_exist_users($from_date, $auth_data);
            // 時間データ取得 user_id指定
            $times_data = $this->model_time->gets_day_in_userid($from_date, $auth_data);
        } else {
            // 従業員データ取得 ALL
            $users_data = $this->model_user->find_exist_all($from_date);
            // 時間データ取得 ALL
            $times_data = $this->model_time->gets_day_all($from_date);
        }

        // メッセージデータ取得
        $this->load->database();
        $message_data = $this->db->query('SELECT * FROM message_data WHERE date_format(created_at, "%Y-%m-%d") = "'.$from_date.'"')->result();
        foreach ($message_data as $key => $value) {
            $message_data[$value->user_id][$value->type] = [
                'message'=> $value->message,
                'datetime'=> $value->created_at
            ];
        }

        if ($times_data) {
            foreach ($times_data as $time) {
                $in_time = $time->in_time === null ? '' : substr($time->in_time, 0, 5);
                $out_time = $time->out_time === null ? '' : substr($time->out_time, 0, 5);
                $in_work_time = $time->in_work_time === null ? '' : substr($time->in_work_time, 0, 5);
                $out_work_time = $time->out_work_time === null ? '' : substr($time->out_work_time, 0, 5);
                $work_hour = $time->fact_work_hour > 0 ? sprintf("%d:%02d", floor($time->fact_work_hour/60), $time->fact_work_hour%60) : '';
                $rest_hour = $time->rest >= 0 ? sprintf("%d:%02d", floor($time->rest/60), $time->rest%60) : '';
                if ($time->fact_work_hour == 0 && $time->rest == 0) {
                    $rest_hour = '';
                }
                if ((int)$this->data['configs']['over_time_view_flag']->value === 1) {
                    $over_hour = $time->over_hour > 0 ? sprintf("%d:%02d", floor($time->over_hour/60), $time->over_hour%60) : '';
                    $ovar_hour2 = $time->over_hour;
                } else {
                    $ovar_hour2 = 0;
                }
                if ((int)$this->data['configs']['night_time_view_flag']->value === 1) {
                    $night_hour = $time->night_hour > 0 ? sprintf("%d:%02d", floor($time->night_hour/60), $time->night_hour%60) : '';
                    $night_hour2 = $time->night_hour;
                } else {
                    $night_hour2 = 0;
                }
                $late_hour = $time->late_hour > 0 ? sprintf("%d:%02d", floor($time->late_hour/60), $time->late_hour%60) : '';
                $left_hour = $time->left_hour > 0 ? sprintf("%d:%02d", floor($time->left_hour/60), $time->left_hour%60) : '';

                if ((int)$this->data['configs']['normal_time_flag']->value === 1) {
                    if ($time->fact_work_hour > 0) {
                        $normal_minute = $time->fact_work_hour - $ovar_hour2;
                        $normal_hour = sprintf("%d:%02d", floor($normal_minute/60), $normal_minute%60);
                    } else {
                        $normal_minute = 0;
                        $normal_hour = '';
                    }
                }

                $times[$time->user_id] = [
                    'in_time' => $in_time,
                    'out_time' => $out_time,
                    'in_work_time' => $in_work_time,
                    'out_work_time' => $out_work_time,
                    'work_hour' => $work_hour,
                    'work_hour2' => $time->fact_work_hour,
                    'rest_hour' => $rest_hour,
                    'rest_hour2' => $time->rest,
                    'late_hour' => $late_hour,
                    'late_hour2' => $time->late_hour,
                    'left_hour' => $left_hour,
                    'left_hour2' => $time->left_hour,
                    'status' => $time->status,
                    'memo' => $time->memo
                ];
                if ((int)$this->data['configs']['minute_time_flag']->value === 1 || (int)$this->data['configs']['minute_time_flag']->value === 2) {
                    $times[$time->user_id] += [
                        'work_minute' => $time->fact_work_hour,
                        'rest_minute' => $time->rest,
                        'over_minute' => $time->over_hour,
                        'night_minute' => $time->night_hour,
                        'late_minute' => $time->late_hour,
                        'left_minute' => $time->left_hour
                    ];
                }
                if ((int)$this->data['configs']['over_time_view_flag']->value === 1) {
                    $times[$time->user_id] += [
                        'over_hour' => $over_hour,
                        'over_hour2' => $time->over_hour
                    ];
                }
                if ((int)$this->data['configs']['night_time_view_flag']->value === 1) {
                    $times[$time->user_id] += [
                        'night_hour' => $night_hour,
                        'night_hour2' => $time->night_hour
                    ];
                }
                if ((int)$this->data['configs']['area_flag']->value === 1) {
                    $times[$time->user_id] += [
                        'area_id' => $time->area_id,
                        'area_name' => @$area_name[$time->area_id] ?: ''
                    ];
                }
                if ((int)$this->data['configs']['normal_time_flag']->value === 1) {
                    $times[$time->user_id] += [
                        'normal_hour' => $normal_hour,
                        'normal_minute' => $normal_minute
                    ];
                }
            }
        }

        // シフトデータ取得
        $this->load->model('model_shift');
        $shift_data = $this->model_shift->find_day_all($from_date);
        $status_text = ['出勤','公休','有給','未定'];
        if ($shift_data) {
            foreach ($shift_data as $key => $value) {
                $shift[$value->user_id] = [
                    'status' => $value->status,
                    'in_time' => $value->in_time,
                    'out_time' => $value->out_time,
                    'shift_rest' => $value->rest
                ];
            }
        }
        if ((int)$this->data['configs']['auto_shift_flag']->value === 1) {
            // ルールの取得
            $rules_data = [];
            $select = 'id, all_flag, user_id, group_id, group_no, basic_rest_weekday, basic_in_time, basic_out_time, rest_rule_flag';
            $where = [];
            $orderby = '';
            $this->load->model('model_config_rules');
            $rules_data = $this->model_config_rules->find($select, $where, $orderby);
            $this->load->helper('holiday_date');
            $holiday_datetime = new HolidayDateTime($from_date);
            $holiday_datetime->holiday() ? $w = 7 : $w = (int)$date->format('w');
            $rest_rules_data = [];
            $select = 'config_rules_id, rest_time';
            $where = [];
            $orderby = '';
            $this->load->model('model_rest_rules');
            $result = $this->model_rest_rules->find($select, $where, $orderby);
            $rest_rules_data = array_column($result, 'rest_time', "config_rules_id");
        }

        $output_data = [];
        $i = 0;
        foreach ($users_data as $user) {
            $user_id = $user->user_id;
            // シフトデータ
            $shift_in_time = $shift_out_time = $shift_status = '';
            $shift_rest = 0;
            // ルール予定
            if ((int)$this->data['configs']['auto_shift_flag']->value === 1) {
                $basic_rest_week = [];
                foreach ($rules_data as $value) {
                    if ($value->all_flag == 1) {
                        $basic_rest_week = str_split($value->basic_rest_weekday);
                        if ((int)$basic_rest_week[$w] === 1) {
                            $shift_status = '公休';
                        }
                        if ((int)$basic_rest_week[$w] === 0 && $value->basic_in_time && $value->basic_out_time) {
                            $shift_status = '出勤';
                            $shift_in_time = $value->basic_in_time;
                            $shift_out_time = $value->basic_out_time;
                            if ((int)$value->rest_rule_flag === 1) {
                                $shift_rest = $rest_rules_data[$value->id];
                            }
                        }
                    }
                    if ($value->user_id == $user_id) {
                        $basic_rest_week = str_split($value->basic_rest_weekday);
                        if ((int)$basic_rest_week[$w] === 1) {
                            $shift_status = '公休';
                        }
                        if ((int)$basic_rest_week[$w] === 0 && $value->basic_in_time && $value->basic_out_time) {
                            $shift_status = '出勤';
                            $shift_in_time = $value->basic_in_time;
                            $shift_out_time = $value->basic_out_time;
                            if ((int)$value->rest_rule_flag === 1) {
                                $shift_rest = $rest_rules_data[$value->id];
                            }
                        }
                    }
                    if (isset($group3_id[$user_id])) {
                        if ($value->group_id == 3 && $value->group_no == $group3_id[$user_id]) {
                            $basic_rest_week = str_split($value->basic_rest_weekday);
                            if ((int)$basic_rest_week[$w] === 1) {
                                $shift_status = '公休';
                            }
                            if ((int)$basic_rest_week[$w] === 0 && $value->basic_in_time && $value->basic_out_time) {
                                $shift_status = '出勤';
                                $shift_in_time = $value->basic_in_time;
                                $shift_out_time = $value->basic_out_time;
                                if ((int)$value->rest_rule_flag === 1) {
                                    $shift_rest = $rest_rules_data[$value->id];
                                }
                            }
                        }
                    }
                    if (isset($group2_id[$user_id])) {
                        if ($value->group_id == 2 && $value->group_no == $group2_id[$user_id]) {
                            $basic_rest_week = str_split($value->basic_rest_weekday);
                            if ((int)$basic_rest_week[$w] === 1) {
                                $shift_status = '公休';
                            }
                            if ((int)$basic_rest_week[$w] === 0 && $value->basic_in_time && $value->basic_out_time) {
                                $shift_status = '出勤';
                                $shift_in_time = $value->basic_in_time;
                                $shift_out_time = $value->basic_out_time;
                                if ((int)$value->rest_rule_flag === 1) {
                                    $shift_rest = $rest_rules_data[$value->id];
                                }
                            }
                        }
                    }
                    if (isset($group1_id[$user_id])) {
                        if ($value->group_id == 1 && $value->group_no == $group1_id[$user_id]) {
                            $basic_rest_week = str_split($value->basic_rest_weekday);
                            if ((int)$basic_rest_week[$w] === 1) {
                                $shift_status = '公休';
                            }
                            if ((int)$basic_rest_week[$w] === 0 && $value->basic_in_time && $value->basic_out_time) {
                                $shift_status = '出勤';
                                $shift_in_time = $value->basic_in_time;
                                $shift_out_time = $value->basic_out_time;
                                if ((int)$value->rest_rule_flag === 1) {
                                    $shift_rest = $rest_rules_data[$value->id];
                                }
                            }
                        }
                    }
                }
            }
            if (isset($shift[$user_id])) {
                $shift_in_time = $shift_out_time = '';
                $shift_status = $status_text[(int)$shift[$user_id]['status']];
                if ((int)$shift[$user_id]['status'] === 0) {
                    $shift_in_time = $shift[$user_id]['in_time'];
                    $shift_out_time = $shift[$user_id]['out_time'];
                    $shift_rest = $shift[$user_id]['shift_rest'];
                }
            }
            // シフト時間取得
            $shift_hour = '';
            $shift_hour2 = 0;
            if ($shift_in_time && $shift_out_time) {
                $tmp = date_parse_from_format('Y-m-d h:i:s', $from_date.' '.$shift_in_time);
                $in_shift_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                $tmp = date_parse_from_format('Y-m-d h:i:s', $from_date.' '.$shift_out_time);
                $out_shift_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                if (strtotime($out_shift_time_calc) > strtotime($in_shift_time_calc)) {
                    $shift_hour2 = (strtotime($out_shift_time_calc) - strtotime($in_shift_time_calc)) / 60;
                    $shift_hour2 -= $shift_rest;
                    $shift_hour = $shift_hour2 >= 0 ? sprintf("%d:%02d", floor($shift_hour2/60), $shift_hour2%60) : '';
                }
            }

            // 出力データ作成
            $output_data[$i] = [
                'user_id'=>str_pad($user_id, (int)$this->data['configs']['id_size']->value, '0', STR_PAD_LEFT),
                'user_name'=>$user->name_sei.' '.$user->name_mei
            ];
            if (isset($group_title[1])) {
                $output_data[$i] += [
                    'group1_name'=>@$group1_name[$group1_id[$user_id]] ?: ''
                ];
            }
            if (isset($group_title[2])) {
                $output_data[$i] += [
                    'group2_name'=>@$group2_name[$group2_id[$user_id]] ?: ''
                ];
            }
            if (isset($group_title[3])) {
                $output_data[$i] += [
                    'group3_name'=>@$group3_name[$group3_id[$user_id]] ?: ''
                ];
            }
            if ((int)$this->data['configs']['area_flag']->value === 1) {
                $output_data[$i] += [
                    'area' => @$area_name[$times[$user_id]['area_id']] ?: ''
                ];
            }
            $output_data[$i] += [
                'shift_status' => $shift_status,
                'shift_in_time' => $shift_in_time ? substr($shift_in_time, 0, 5) : '',
                'shift_out_time' => $shift_out_time ? substr($shift_out_time, 0, 5) : '',
                'shift_hour' => $shift_hour
            ];
            $output_data[$i] += [
                'status' => @$times[$user_id]['status'] ?: '',
                'in_time' => @$times[$user_id]['in_time'] ?: '',
                'out_time' => @$times[$user_id]['out_time'] ?: '',
                'in_work_time' => @$times[$user_id]['in_work_time'] ?: '',
                'out_work_time' => @$times[$user_id]['out_work_time'] ?: ''
            ];
            if ((int)$this->data['configs']['minute_time_flag']->value === 0 || (int)$this->data['configs']['minute_time_flag']->value === 1) {
                $output_data[$i] += [
                    'work_hour' => @$times[$user_id]['work_hour'] ?: ''
                ];
            }
            if ((int)$this->data['configs']['minute_time_flag']->value === 1 || (int)$this->data['configs']['minute_time_flag']->value === 2) {
                $output_data[$i] += [
                    'work_minute' => @$times[$user_id]['work_minute'] ?: ''
                ];
            }
            if ((int)$this->data['configs']['normal_time_flag']->value === 1) {
                if ((int)$this->data['configs']['minute_time_flag']->value === 0 || (int)$this->data['configs']['minute_time_flag']->value === 1) {
                    $output_data[$i] += [
                        'normal_hour' => @$times[$user_id]['normal_hour'] ?: ''
                    ];
                }
                if ((int)$this->data['configs']['minute_time_flag']->value === 1 || (int)$this->data['configs']['minute_time_flag']->value === 2) {
                    $output_data[$i] += [
                        'normal_minute' => @$times[$user_id]['normal_minute'] ?: ''
                    ];
                }
            }
            if ((int)$this->data['configs']['minute_time_flag']->value === 0 || (int)$this->data['configs']['minute_time_flag']->value === 1) {
                $output_data[$i] += [
                    'rest_hour' => @$times[$user_id]['rest_hour'] ?: ''
                ];
            }
            if ((int)$this->data['configs']['minute_time_flag']->value === 1 || (int)$this->data['configs']['minute_time_flag']->value === 2) {
                $output_data[$i] += [
                    'rest_minute' => @$times[$user_id]['rest_minute'] ?: ''
                ];
            }
            if ((int)$this->data['configs']['over_time_view_flag']->value === 1) {
                if ((int)$this->data['configs']['minute_time_flag']->value === 0 || (int)$this->data['configs']['minute_time_flag']->value === 1) {
                    $output_data[$i] += [
                        'over_hour' => @$times[$user_id]['over_hour'] ?: ''
                    ];
                }
                if ((int)$this->data['configs']['minute_time_flag']->value === 1 || (int)$this->data['configs']['minute_time_flag']->value === 2) {
                    $output_data[$i] += [
                        'over_minute' => @$times[$user_id]['over_minute'] ?: ''
                    ];
                }
            }
            if ((int)$this->data['configs']['night_time_view_flag']->value === 1) {
                if ((int)$this->data['configs']['minute_time_flag']->value === 0 || (int)$this->data['configs']['minute_time_flag']->value === 1) {
                    $output_data[$i] += [
                        'night_hour' => @$times[$user_id]['night_hour'] ?: ''
                    ];
                }
                if ((int)$this->data['configs']['minute_time_flag']->value === 1 || (int)$this->data['configs']['minute_time_flag']->value === 2) {
                    $output_data[$i] += [
                        'night_minute' => @$times[$user_id]['night_minute'] ?: ''
                    ];
                }
            }
            if ((int)$this->data['configs']['minute_time_flag']->value === 0 || (int)$this->data['configs']['minute_time_flag']->value === 1) {
                $output_data[$i] += [
                    'late_hour' => @$times[$user_id]['late_hour'] ?: ''
                ];
            }
            if ((int)$this->data['configs']['minute_time_flag']->value === 1 || (int)$this->data['configs']['minute_time_flag']->value === 2) {
                $output_data[$i] += [
                    'late_minute' => @$times[$user_id]['late_minute'] ?: ''
                ];
            }
            if ((int)$this->data['configs']['minute_time_flag']->value === 0 || (int)$this->data['configs']['minute_time_flag']->value === 1) {
                $output_data[$i] += [
                    'left_hour' => @$times[$user_id]['left_hour'] ?: ''
                ];
            }
            if ((int)$this->data['configs']['minute_time_flag']->value === 1 || (int)$this->data['configs']['minute_time_flag']->value === 2) {
                $output_data[$i] += [
                    'left_minute' => @$times[$user_id]['left_minute'] ?: ''
                ];
            }
            $output_data[$i] += [
                'memo' => @$times[$user_id]['memo'] ?: '',
                'message_in' => @$message_data[$user_id]['in']['message'] ?: '',
                'message_out' => @$message_data[$user_id]['out']['message'] ?: '',
                // 下記はテーブル非表示
                'work_hour2' => @(int)$times[$user_id]['work_hour2'] ?: '',
                'rest_hour2' => @(int)$times[$user_id]['rest_hour2'] ?: '',
                'over_hour2' => @(int)$times[$user_id]['over_hour2'] ?: '',
                'night_hour2' => @(int)$times[$user_id]['night_hour2'] ?: '',
                'late_hour2' => @(int)$times[$user_id]['late_hour2'] ?: '',
                'left_hour2' => @(int)$times[$user_id]['left_hour2'] ?: '',
                'group1_order' => @(int)$group1_order[$group1_id[$user_id]] ?: 999,
                'group2_order' => @(int)$group2_order[$group2_id[$user_id]] ?: 999,
                'group3_order' => @(int)$group3_order[$group3_id[$user_id]] ?: 999,
                'in_latitude' => @$gps_data[$user_id][1]['latitude'] ?: '',
                'in_longitude' => @$gps_data[$user_id][1]['longitude'] ?: '',
                'out_latitude' => @$gps_data[$user_id][2]['latitude'] ?: '',
                'out_longitude' => @$gps_data[$user_id][2]['longitude'] ?: '',
                'area_id' => @(int)$times[$user_id]['area_id'] ?: '',
                'normal_hour2' => @(int)$times[$user_id]['normal_minute'] ?: '',
                'shift_hour2' => (int)$shift_hour2,
                'shift_rest' => (int)$shift_rest,
            ];
            $i++;
        }
        // output
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($output_data));
    }

    /**
     * 時刻修正　保存
     *
     * @param int     user_id
     * @param string  today
     * @param string  in_time
     * @param string  out_time
     * @param int     area_id
     * @return array  $callback
     */
    public function save()
    {
        $status_data['flag'] = 'edit'; // フラグ
        $status_data['user_id'] = (int)$this->input->post('user_id'); // 従業員ID
        $shiftData['user_id'] = (int)$this->input->post('user_id'); // 従業員ID
        $status_data['dk_datetime'] = $this->input->post('today');
        $shiftData['dk_date'] = $this->input->post('today');
        $in_time = $this->input->post('in_time');
        $out_time = $this->input->post('out_time');
        $shift_in_time = $this->input->post('shift_in_time');
        $shift_out_time = $this->input->post('shift_out_time');
        if ($in_time !== '') {
            $status_data['in_work_time'] = $in_time.':00';
            $status_data['revision_in'] = 1;
        } else {
            $status_data['in_work_time'] = NULL;
            $status_data['revision_in'] = 0;
        }
        if ($out_time !== '') {
            $status_data['out_work_time'] = $out_time.':00';
            $status_data['revision_out'] = 1;
        } else {
            $status_data['out_work_time'] = NULL;
            $status_data['revision_out'] = 0;
        }
        if ($shift_in_time !== '') {
            $shiftData['in_time'] = $shift_in_time.':00';
        } else {
            $shiftData['in_time'] = NULL;
        }
        if ($shift_out_time !== '') {
            $shiftData['out_time'] = $shift_out_time.':00';
        } else {
            $shiftData['out_time'] = NULL;
        }
        if ((int)$this->data['configs']['over_day']->value > 0) { // 日またぎ対応
            if ($in_time !== '') {
                $in_hour = substr($in_time, 0, 2);
                $in_minute = substr($in_time, 3, 2);
                if ((int)$in_hour <= (int)$this->data['configs']['over_day']->value) {
                    $in_hour = (int)$in_hour + 24;
                    $status_data['in_work_time'] = $in_hour.':'.$in_minute.':00';
                }
            }
            if ($out_time !== '') {
                $out_hour = substr($out_time, 0, 2);
                $out_minute = substr($out_time, 3, 2);
                if ((int)$out_hour <= (int)$this->data['configs']['over_day']->value) {
                    $out_hour = (int)$out_hour + 24;
                    $status_data['out_work_time'] = $out_hour.':'.$out_minute.':00';
                }
            }
            if ($shift_in_time !== '') {
                $shift_in_hour = substr($shift_in_time, 0, 2);
                $shift_in_minute = substr($shift_in_time, 3, 2);
                if ((int)$shift_in_hour <= (int)$this->data['configs']['over_day']->value) {
                    $shift_in_hour = (int)$shift_in_hour + 24;
                    $shiftData['in_time'] = $shift_in_hour.':'.$shift_in_minute.':00';
                }
            }
            if ($shift_out_time !== '') {
                $shift_out_hour = substr($shift_out_time, 0, 2);
                $shift_out_minute = substr($shift_out_time, 3, 2);
                if ((int)$shift_out_hour <= (int)$this->data['configs']['over_day']->value) {
                    $shift_out_hour = (int)$shift_out_hour + 24;
                    $shiftData['out_time'] = $shift_out_hour.':'.$shift_out_minute.':00';
                }
            }
        }
        $status_data['area_id'] = $this->input->post('area_id');
        $status_data['rest'] = $this->input->post('rest');
        $status_data['memo'] = $this->input->post('memo');
        $status_data['revision'] = 1; // 修正フラグ
        $now = new DateTimeImmutable();
        $status_data['revision_datetime'] = $now->format('Y-m-d H:i:s');
        $status_data['revision_user'] = $this->session->user_name;
        $shiftData['rest'] = $this->input->post('shift_rest');
        $shiftData['status'] = $this->input->post('shift_status');
        $shiftData['hour'] = $this->input->post('shift_hour');

        if ($this->input->post('flag') !== 'mypage' && $shiftData['status'] !== '') {
            // shift data 保存
            $select = 'id';
            $where = ['dk_date'=>$shiftData['dk_date'], 'user_id'=>$shiftData['user_id']];
            $this->load->model('Model_shift_data');
            if ($this->Model_shift_data->find_row($select, $where)) {
                $shift_db_id = $this->Model_shift_data->find_row($select, $where)->id;
                if ($shiftData['status'] === 'del') {
                    $this->Model_shift_data->delete($shift_db_id);
                } else {
                    $this->Model_shift_data->update($shift_db_id, $shiftData);
                }
            } elseif ($shiftData['status'] !== 'del') {
                if ($shiftData['status'] == 0 && $shiftData['in_time'] == '' && $shiftData['out_time'] == '') {
                } else {
                    $this->Model_shift_data->user_id = $shiftData['user_id'];
                    $this->Model_shift_data->dk_date = $shiftData['dk_date'];
                    $this->Model_shift_data->in_time = $shiftData['in_time'];
                    $this->Model_shift_data->out_time = $shiftData['out_time'];
                    $this->Model_shift_data->hour = $shiftData['hour'] > 0 ? $shiftData['hour'] : 0;
                    $this->Model_shift_data->rest = $shiftData['rest'] > 0 ? $shiftData['rest'] : 0;
                    $this->Model_shift_data->status = $shiftData['status'];
                    $this->Model_shift_data->insert();
                }
            }
        }
        $message = 'ok';

        // 分析
        $this->load->library('process_status_lib'); // 分析処理用 lib 読込
        if ($this->process_status_lib->status($status_data)) {
            $message = 'ok';
        } else {
            $message = 'err';
        }

        $callback = [
            'message'=>$message,
            'user_id'=>$status_data['user_id'],
            'today'=>$status_data['dk_datetime']
        ];

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($callback));
    }
}
