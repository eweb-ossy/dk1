<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_info extends MY_Controller
{
    public function table_data()
    {
        $now = new DateTime();
        $dk_date = $this->input->post('dk_date') ?: $now->format('Y-m-d');
        $dk_date_obj = new DateTime($dk_date);
        $dk_year = $dk_date_obj->format('Y');
        $dk_month = $dk_date_obj->format('m');

        // first date - end date 
        if ($this->data['configs']['end_day']->value > 0) {
            $end_day = $this->data['configs']['end_day']->value;
            $pre = new DateTime($dk_year.'-'.$dk_month.'-01');
            $pre->sub(DateInterval::createFromDateString('1 month')); // 1 month old
            $pre_year = $pre->format('Y');
            $pre_month = $pre->format('m');
            $pre_month_days = cal_days_in_month(CAL_GREGORIAN, $pre_month, $pre_year);
            $first_date = sprintf('%04d-%02d-%02d', $pre_year, $pre_month, $end_day + 1);
            $end_date = sprintf('%04d-%02d-%02d', $dk_year, $dk_month, $end_day); 
        } else {
            $first_date = sprintf('%04d-%02d-%02d', $dk_year, $dk_month, 1);
            $end_date = sprintf('%04d-%02d-%02d', $dk_year, $dk_month, cal_days_in_month(CAL_GREGORIAN, $dk_month, $dk_year));
        }

        // user data
        $result = $this->db->query('SELECT `user_id`, CONCAT(`name_sei`, " ", `name_mei`) `name` FROM `user_data` ORDER BY `user_id`')->result();
        $user_data = array_column($result, NULL, 'user_id');

        // gps data 
        $result = $this->db->query("SELECT `user_id`, `gps_date`, `flag`, `latitude`, `longitude`, `ip_address`, `platform`, `info` FROM `gps_data` WHERE `gps_date` = '{$dk_date}'")->result();
        foreach ($result as $key => $value) {
            $flag = $value->flag == 1 ? 'in' : 'out';
            $gps_data[$flag][$value->user_id] = [
                'latitude'=> $value->latitude,
                'longitude'=> $value->longitude,
                'ip_address'=> $value->ip_address,
                'platform'=> $value->platform,
                'info'=> $value->info
            ];
        }

        // area data 
        $result = $this->db->query("SELECT `area_name`, `host_ip` FROM `area_data`")->result();
        $area_data = array_column($result, 'area_name', 'host_ip');

        // time data
        $time_data = $this->db->query("SELECT `user_id`, `dk_date`, `fact_work_hour`, `status`, `status_flag`, `area_id`, LEFT(`in_work_time`, 5) `in_work_time`, LEFT(`out_work_time`, 5) `out_work_time`, LEFT(`in_time`, 5) `in_time`, LEFT(`out_time`, 5) `out_time` FROM `time_data` WHERE `dk_date` BETWEEN '{$first_date}' AND '{$end_date}' AND `status_flag` > 0")->result();
        foreach ($time_data as $key => $value) {
            $time_user_data[$value->user_id][$value->dk_date] = [
               'fact_work_hour'=> $value->fact_work_hour,
               'status_flag'=> $value->status_flag
           ];

        }

        $output = [];
        $gps_type = ['in', 'out'];
        foreach ($time_data as $key => $value) {
            $user_id = $value->user_id;
            if ($value->dk_date == $dk_date) {
                $time_status = $value->in_work_time.' - '.$value->out_work_time;
                $all_time[$user_id] = array_column($time_user_data[$user_id], 'fact_work_hour');
                $all_hour = array_sum($all_time[$user_id]);
                $count = 0;
                foreach ($all_time[$user_id] as $hour) {
                    if ($hour > 0) $count++;
                }
                $ave_hour = $all_hour > 0 && $count > 0 ? $all_hour / $count : NULL;
                $all_status_flag = array_column($time_user_data[$user_id], 'status_flag');
                $status = array_count_values($all_status_flag);
                $holiday = 0;
                $holiday = $holiday + @$status['22'] ?: 0;
                $holiday = $holiday + @$status['68'] ?: 0;
                $absence = 0;
                $absence = $absence + @$status['59'] ?: 0;
                $paid = 0;
                $paid = $paid + @$status['29'] ?: 0;
                $paid = $paid + @$status['75'] ?: 0;
                $info = '';
                foreach ($gps_type as $type) {
                    $area[$type] = '';
                    $map[$type] = '';
                    if (isset($gps_data[$type][$user_id])) {
                        $area[$type] = @$area_data[$gps_data[$type][$user_id]['ip_address']] ?: '未登録エリア';
                        $latitude = @$gps_data[$type][$user_id]['latitude'] ?: '';
                        $longitude = @$gps_data[$type][$user_id]['longitude'] ?: '';
                        if ($latitude && $longitude) {
                            $map[$type] = "<iframe src='https://maps.google.co.jp/maps?output=embed&t=m&hl=ja&z=15&ll={$latitude},{$longitude}&q={$latitude},{$longitude}' frameborder='0' scrolling='no' width='300px' height='78px'></iframe>";
                        }
                        $info .= @$gps_data[$type][$user_id]['info'] ?: '';
                   }
                }

                $output[] = [
                    'user_id'=> str_pad($user_id, (int)$this->data['configs']['id_size']->value, '0', STR_PAD_LEFT),
                    'user_name'=> @$user_data[$user_id]->name ?: '',
                    'status'=> $value->status ?: '',
                    'status_flag'=> $value->status_flag ?: '',
                    'in_time'=> $value->in_time ?: '',
                    'out_time'=> $value->out_time ?: '',
                    'count'=> $count,
                    'fact_work_hour'=> $value->fact_work_hour ? sprintf("%d:%02d", floor((int)$value->fact_work_hour/60), (int)$value->fact_work_hour%60) : '',
                    'time_status'=> $time_status,
                    'all_hour'=> $all_hour > 0 ? sprintf("%d:%02d", floor($all_hour/60), $all_hour%60) : '',
                    'ave_hour'=> $ave_hour ? sprintf("%d:%02d", floor($ave_hour/60), $ave_hour%60) : '',
                    'holiday'=> $holiday > 0 ? $holiday : '',
                    'absence'=> $absence > 0 ? $absence : '',
                    'paid'=> $paid > 0 ? $paid : '',
                    'area_info_in'=> @$area['in'] ?: '',
                    'area_info_out'=> @$area['out'] ?: '',
                    'in_platform'=> @$gps_data['in'][$user_id]['platform'] ?: '',
                    'out_platform'=> @$gps_data['out'][$user_id]['platform'] ?: '',
                    'in_map'=> @$map['in'] ?: '',
                    'out_map'=> @$map['out'] ?: '',
                    'map'=> $info,
                    'times'=> $first_date.' - '.$end_date,
                    'data'=> $time_user_data[$user_id]
                ];
            }
        }

        // output
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($output));
    }
}

