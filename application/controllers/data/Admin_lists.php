<?php
/**
*   管理画面　従業員別集計（個人）　コントロール
*
*   @copyright  e-web,Inc
*   @author     oshizawa
*/

defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_lists extends CI_Controller
{
    /**
     *   table表示用データを返す
    *
    *   @param $user_id
    *   @param $year
    *   @param $month
    *   @return array json $data[][]
    */
    public function table_data()
    {
        $today = new DateTime();
        $today_year = $today->format('Y');
        $today_month = $today->format('m');
        $today_day = $today->format('d');
        $user_id = $this->input->post('user_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $end_day = (int)$this->input->post('end_day'); // 締め日
        if ($end_day > 0) {
            $pre_date = new DateTime($year.'-'.$month.'-01');
            $pre_date->sub(DateInterval::createFromDateString('1 month')); // １ヶ月前
            $pre_year = $pre_date->format('Y');
            $pre_month = $pre_date->format('m');
            $pre_day = $end_day + 1;
            $first_date = new DateTime($pre_year.'-'.$pre_month.'-'.$pre_day);
            $end_date = new DateTime($year.'-'.$month.'-'.$end_day.' 00:00:01');
            $top_date = $pre_year.'-'.$pre_month.'-'.$pre_day;
            $last_date = $year.'-'.$month.'-'.$end_day;
        } else {
            $first_date = new DateTime($year.'-'.$month.'-01');
            $end_date = new DateTime($year.'-'.$month.'-'.$month_days.' 00:00:01');
            $top_date = $year.'-'.$month.'-01';
            $last_date = $year.'-'.$month.'-'.$month_days;
        }
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($first_date, $interval, $end_date);
        $this->load->helper('holiday_date');
        $week = array("日", "月", "火", "水", "木", "金", "土", "祝");
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('id, config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        $rules = [];
        $basic_in_time = '';
        $basic_out_time = '';
        $basic_rest_week = [];
        $shift_rest = 0;
        if ((int)$config_data['auto_shift_flag'] === 1) {
            // ルールの取得
            $this->load->library('process_rules_lib'); // rules lib 読込
            $rules = $this->process_rules_lib->get_rule($user_id);
            //
            if (isset($rules->basic_in_time)) {
                $basic_in_time = substr($rules->basic_in_time, 0, 5);
                $basic_in_h = substr($rules->basic_in_time, 0, 2);
                $basic_in_m = substr($rules->basic_in_time, 3, 2);
            }
            if (isset($rules->basic_out_time)) {
                $basic_out_time = substr($rules->basic_out_time, 0, 5);
                $basic_out_h = substr($rules->basic_out_time, 0, 2);
                $basic_out_m = substr($rules->basic_out_time, 3, 2);
            }
            if (isset($rules->basic_rest_weekday)) {
                $basic_rest_week = str_split($rules->basic_rest_weekday);
            }
            if ((int)$rules->rest_rule_flag === 1) {
                $select = 'rest_time';
                $where = ['config_rules_id'=>$rules->id];
                $this->load->model('model_rest_rules');
                $shift_rest = (int)$this->model_rest_rules->find_row($select, $where)->rest_time;
            }
        }

        if ((int)$config_data['area_flag'] === 1) {
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

        if ((int)$config_data['gps_flag'] !== 0) {
            // GPSデータ取得
            $gps_data = [];
            $this->load->model('model_gps_data');
            $where = [
                'user_id' => (int)$user_id,
                'gps_date >=' => $top_date,
                'gps_date <=' => $last_date
            ];
            $result = $this->model_gps_data->find('gps_date, flag, latitude, longitude', $where, '');

            // $result = $this->model_gps_data->gets_user_id($user_id, $top_date, $last_date);
            foreach ($result as $value) {
                $gps_data[$value->gps_date][(int)$value->flag] = [
                    'latitude'=>$value->latitude,
                    'longitude'=>$value->longitude
                ];
            }
        }

        $i = 0;
        foreach ($period as $datetime) {
            $holiday_datetime = new HolidayDateTime($datetime->format('Y-m-d'));
            $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
            $shift_status = '';
            $shift_in_time = '';
            $shift_out_time = '';
            $shift_rest_hour = 0;
            $shift_hour = '';
            $shift_hour2 = 0;
            $shift_rest = 0;
            // auto shiftの場合、事前に定時をシフトに入力
            if ((int)$config_data['auto_shift_flag'] === 1) {
                if ($basic_rest_week) {
                    if ($basic_rest_week[$w] == 1) {
                        $shift_status = '公休';
                    }
                    if ($basic_rest_week[$w] == 0 && $basic_in_time && $basic_out_time) {
                        $shift_status = '出勤';
                        $shift_in_time = $basic_in_time;
                        $shift_out_time = $basic_out_time;
                        $shift_rest_hour = $shift_rest;
                        if ($shift_in_time && $shift_out_time) {
                            $tmp = date_parse_from_format('Y-m-d h:i:s', $datetime->format('Y-m-d').' '.$shift_in_time);
                            $in_shift_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                            $tmp = date_parse_from_format('Y-m-d h:i:s', $datetime->format('Y-m-d').' '.$shift_out_time);
                            $out_shift_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                            if (strtotime($out_shift_time_calc) > strtotime($in_shift_time_calc)) {
                                $shift_hour2 = (strtotime($out_shift_time_calc) - strtotime($in_shift_time_calc)) / 60;
                                $shift_hour2 -= $shift_rest;
                                $shift_hour = $shift_hour2 >= 0 ? sprintf("%d:%02d", floor($shift_hour2/60), $shift_hour2%60) : '';
                            }
                        }
                    }
                }
            }
            $data[$i] = [
                'day'=> (int)$datetime->format('d'),
                'week'=> $week[$w],
                'shift_status'=>$shift_status,
                'shift_in_time'=>$shift_in_time,
                'shift_out_time'=>$shift_out_time,
                'shift_hour'=>$shift_hour
            ];
            if ((int)$config_data['area_flag'] === 1) {
                $data[$i] += ['area' =>''];
            }
            $data[$i] += [
                'in_time'=>'',
                'out_time'=>'',
                'in_work_time'=>'',
                'out_work_time'=>'',
                'work_hour'=>''
            ];
            if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                $data[$i] += ['work_minute'=>''];
            }
            if ((int)$config_data['normal_time_flag'] === 1) {
                $data[$i] += ['normal_hour'=>''];
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$i] += ['normal_minute'=>''];
                }
            }
            $data[$i] += ['rest_hour'=>''];
            if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                $data[$i] += ['rest_minute'=>''];
            }
            if ((int)$config_data['over_time_view_flag'] === 1) {
                $data[$i] += ['over_hour'=>''];
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$i] += ['over_minute'=>''];
                }
            }
            if ((int)$config_data['night_time_view_flag'] === 1) {
                $data[$i] += ['night_hour'=>''];
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$i] += ['night_minute'=>''];
                }
            }
            $data[$i] += ['late_hour'=>''];
            if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                $data[$i] += ['late_minute'=>''];
            }
            $data[$i] += ['left_hour'=>''];
            if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                $data[$i] += ['left_minute'=>''];
            }
            $data[$i] += ['status'=>''];
            $data[$i] += ['memo'=>''];
            $data[$i] += ['work_hour2'=>''];
            $data[$i] += ['rest_hour2'=>''];
            $data[$i] += ['area_id'=>''];
            if ((int)$config_data['normal_time_flag'] === 1) {
                $data[$i] += ['normal_hour2'=>''];
            }
            if ((int)$config_data['over_time_view_flag'] === 1) {
                $data[$i] += ['over_hour2'=>''];
            }
            if ((int)$config_data['night_time_view_flag'] === 1) {
                $data[$i] += ['night_hour2'=>''];
            }
            $data[$i] += ['late_hour2'=>''];
            $data[$i] += ['left_hour2'=>''];
            $data[$i] += ['today_flag'=>''];
            $data[$i] += ['in_latitude'=>@$gps_data[$datetime->format('Y-m-d')][1]['latitude'] ?: ''];
            $data[$i] += ['in_longitude'=>@$gps_data[$datetime->format('Y-m-d')][1]['longitude'] ?: ''];
            $data[$i] += ['out_latitude'=>@$gps_data[$datetime->format('Y-m-d')][2]['latitude'] ?: ''];
            $data[$i] += ['out_longitude'=>@$gps_data[$datetime->format('Y-m-d')][2]['longitude'] ?: ''];
            $data[$i] += ['shift_rest'=>$shift_rest_hour,
            'shift_hour2'=>$shift_hour2];
            $num[(int)$datetime->format('d')] = $i; // 変換用
            $data[$i] += ['date'=>$datetime->format('Y-m-d')];
            $i++;
        }

        $first_date = $first_date->format('Y-m-d');
        $end_date = $end_date->format('Y-m-d');

        $shift_data = [];
        $this->load->model('model_shift');
        $shift_data = $this->model_shift->gets_to_end_date_user_id($first_date, $end_date, $user_id);
        if ($shift_data) {
            foreach ($shift_data as $row) {
                $shift_status = $shift_in_time = $shift_out_time = $shift_hour = '';
                $shift_hour2 = $shift_rest = 0;
                if ($row->status == 0) {
                    $shift_status = '出勤';
                    $shift_in_time = $row->in_time === NULL ? '' : substr($row->in_time, 0, 5);
                    $shift_out_time = $row->out_time === NULL ? '' : substr($row->out_time, 0, 5);
                    $shift_rest = (int)$row->rest;
                    if ($shift_in_time && $shift_out_time) {
                        $tmp = date_parse_from_format('Y-m-d h:i:s', $datetime->format('Y-m-d').' '.$shift_in_time);
                        $in_shift_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                        $tmp = date_parse_from_format('Y-m-d h:i:s', $datetime->format('Y-m-d').' '.$shift_out_time);
                        $out_shift_time_calc = strftime('%Y-%m-%d %H:%M:%S', mktime($tmp['hour'], $tmp['minute'], 0, $tmp['month'], $tmp['day'], $tmp['year']));
                        if (strtotime($out_shift_time_calc) > strtotime($in_shift_time_calc)) {
                            $shift_hour2 = (strtotime($out_shift_time_calc) - strtotime($in_shift_time_calc)) / 60;
                            $shift_hour2 -= $shift_rest;
                            $shift_hour = (int)$shift_hour2 >= 0 ? sprintf("%d:%02d", floor($shift_hour2/60), $shift_hour2%60) : '';
                        }
                    }
                }
                if ($row->status == 1) {
                    $shift_status = '公休';
                }
                if ($row->status == 2) {
                    $shift_status = '有給';
                }
                $day = substr($row->dk_date, -2);
                $data[$num[(int)$day]]['shift_status'] = $shift_status;
                $data[$num[(int)$day]]['shift_in_time'] = $shift_in_time;
                $data[$num[(int)$day]]['shift_out_time'] = $shift_out_time;
                $data[$num[(int)$day]]['shift_rest'] = $shift_rest;
                $data[$num[(int)$day]]['shift_hour'] = $shift_hour;
                $data[$num[(int)$day]]['shift_hour2'] = $shift_hour2;
            }
        }

        $this->load->model('model_time');
        $time_data = $this->model_time->gets_to_end_date_user_id($first_date, $end_date, $user_id);
        if ($time_data) {
            foreach ($time_data as $row) {
                $in_time = $row->in_time === null ? '' : substr($row->in_time, 0, 5);
                $out_time = $row->out_time === null ? '' : substr($row->out_time, 0, 5);
                $in_work_time = $row->in_work_time === null ? '' : substr($row->in_work_time, 0, 5);
                $out_work_time = $row->out_work_time === null ? '' : substr($row->out_work_time, 0, 5);
                if ($row->fact_work_hour > 0) {
                    $fact_work_hour = sprintf('%d:%02d', floor($row->fact_work_hour/60), $row->fact_work_hour%60);
                    $fact_work_hour2 = $row->fact_work_hour;
                } else {
                    $fact_work_hour = '';
                    $fact_work_hour2 = 0;
                }
                if ($row->rest > 0) {
                    $rest_hour = sprintf('%d:%02d', floor($row->rest/60), $row->rest%60);
                    $rest_hour2 = $row->rest;
                } else {
                    $rest_hour = '';
                    $rest_hour2 = 0;
                }
                if ($row->over_hour > 0) {
                    $over_hour = sprintf('%d:%02d', floor($row->over_hour/60), $row->over_hour%60);
                    $over_hour2 = $row->over_hour;
                } else {
                    $over_hour = '';
                    $over_hour2 = 0;
                }
                if ($row->night_hour > 0) {
                    $night_hour = sprintf('%d:%02d', floor($row->night_hour/60), $row->night_hour%60);
                    $night_hour2 = $row->night_hour;
                } else {
                    $night_hour = '';
                    $night_hour2 = 0;
                }
                if ($row->late_hour > 0) {
                    $late_hour = sprintf('%d:%02d', floor($row->late_hour/60), $row->late_hour%60);
                    $late_hour2 = $row->late_hour;
                } else {
                    $late_hour = '';
                    $late_hour2 = 0;
                }
                if ($row->left_hour > 0) {
                    $left_hour = sprintf('%d:%02d', floor($row->left_hour/60), $row->left_hour%60);
                    $left_hour2 = $row->left_hour;
                } else {
                    $left_hour = '';
                    $left_hour2 = 0;
                }
                $day = substr($row->dk_date, -2);
                $data[$num[(int)$day]]['area'] = @$area_name[$row->area_id] ?: '';
                $data[$num[(int)$day]]['in_time'] = $in_time;
                $data[$num[(int)$day]]['out_time'] = $out_time;
                $data[$num[(int)$day]]['in_work_time'] = $in_work_time;
                $data[$num[(int)$day]]['out_work_time'] = $out_work_time;
                if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                    $data[$num[(int)$day]]['work_hour'] = $fact_work_hour;
                }
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$num[(int)$day]]['work_minute'] = $fact_work_hour2 > 0 ? $fact_work_hour2 : '';
                }
                if ((int)$config_data['normal_time_flag'] === 1) {
                    $normal_hour2 = $fact_work_hour2 - $over_hour2;
                    if ($normal_hour2 > 0) {
                        $normal_hour = sprintf('%d:%02d', floor($normal_hour2/60), $normal_hour2%60);
                    } else {
                        $normal_hour = '';
                    }
                    $data[$num[(int)$day]]['normal_hour'] = $normal_hour;
                    if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                        $data[$num[(int)$day]]['normal_minute'] = $normal_hour2 > 0 ? $normal_hour2 : '';
                    }
                    $data[$num[(int)$day]]['normal_hour2'] = (int)$normal_hour2;
                }
                if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                    $data[$num[(int)$day]]['rest_hour'] = $rest_hour;
                }
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$num[(int)$day]]['rest_minute'] = $rest_hour2 > 0 ? $rest_hour2 : '';
                }
                if ((int)$config_data['over_time_view_flag'] === 1) {
                    if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                        $data[$num[(int)$day]]['over_hour'] = $over_hour;
                    }
                    if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                        $data[$num[(int)$day]]['over_minute'] = $over_hour2 > 0 ? $over_hour2 : '';
                    }
                    $data[$num[(int)$day]]['over_hour2'] = (int)$over_hour2;
                }
                if ((int)$config_data['night_time_view_flag'] === 1) {
                    if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                        $data[$num[(int)$day]]['night_hour'] = $night_hour;
                    }
                    if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                        $data[$num[(int)$day]]['night_minute'] = $night_hour2 > 0 ? $night_hour2 : '';
                    }
                    $data[$num[(int)$day]]['night_hour2'] = (int)$night_hour2;
                }
                if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                    $data[$num[(int)$day]]['late_hour'] = $late_hour;
                }
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$num[(int)$day]]['late_minute'] = $late_hour2 > 0 ? $late_hour2 : '';
                }
                if ((int)$config_data['minute_time_flag'] === 0 || (int)$config_data['minute_time_flag'] === 1) {
                    $data[$num[(int)$day]]['left_hour'] = $left_hour;
                }
                if ((int)$config_data['minute_time_flag'] === 1 || (int)$config_data['minute_time_flag'] === 2) {
                    $data[$num[(int)$day]]['left_minute'] = $left_hour2 > 0 ? $left_hour2 : '';
                }
                $data[$num[(int)$day]]['status'] = $row->status ?: '';
                $data[$num[(int)$day]]['memo'] = $row->memo ?: '';
                $data[$num[(int)$day]]['work_hour2'] = (int)$fact_work_hour2;
                $data[$num[(int)$day]]['rest_hour2'] = (int)$rest_hour2;
                $data[$num[(int)$day]]['late_hour2'] = (int)$late_hour2;
                $data[$num[(int)$day]]['left_hour2'] = (int)$left_hour2;
                $data[$num[(int)$day]]['area_id'] = $row->area_id ?: '';
            }
        }

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function user_data()
    {
        $user_id = $this->input->post('user_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $month_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $end_date = sprintf('%04d-%02d-%02d', $year, $month, $month_days);
        $to_month = $year.$month;

        $this->load->model('model_user');
        $user_data = $this->model_user->find_exist_month_userid($to_month, $end_date, $user_id);
        $this->load->model('model_group_title');
        $group_title = $this->model_group_title->gets_data();
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
        $user = [
            'user_name' => $user_data->name_sei.' '.$user_data->name_mei,
            'user_kana' => $user_data->kana_sei.' '.$user_data->kana_mei,
            'group1_name' => $group_title[0]->title.' : '.@$group1_name[$user_data->group1_id] ?: '',
            'group2_name' => $group_title[1]->title.' : '.@$group2_name[$user_data->group2_id] ?: '',
            'group3_name' => $group_title[2]->title.' : '.@$group3_name[$user_data->group3_id] ?: '',
            'month'=>$month
        ];

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($user));
    }

    public function select_users()
    {
        $now_date = $this->input->post('now_date');

        $this->load->model('model_user');
        $users_data = $this->model_user->find_exist_all($now_date);
        $users = [];
        foreach ($users_data as $key => $value) {
            $users[] = [
                'user_name'=> $value->name_sei.' '.$value->name_mei,
                'user_id'=> $value->user_id
            ];
        }

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($users));
    }
}
