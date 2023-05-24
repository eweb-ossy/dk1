<?php 

// 各種CSVファイルダウンロード

defined('BASEPATH') or exit('No direct script access alllowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class Admin_conf_download extends CI_Controller
{
    public function index()
    {
        $type = $this->input->post('type');
        $csv_date = $this->input->post('csv_date');
        $year = substr($csv_date, 0, 4);
        $month = substr($csv_date, 4);
        $month_days = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
        $end_date = sprintf('%04d-%02d-%02d', $year, $month, $month_days);

        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        if ((int)$config_data['end_day'] > 0) {
            $pre_date = new DateTime($year.'-'.$month.'-01');
            $pre_date->sub(DateInterval::createFromDateString('1 month')); // １ヶ月前
            $pre_year = $pre_date->format('Y');
            $pre_month = $pre_date->format('m');
            $pre_day = (int)$config_data['end_day'] + 1;
            $first_date = new DateTime($pre_year.'-'.$pre_month.'-'.$pre_day);
            $pre_month_days = cal_days_in_month(CAL_GREGORIAN, $pre_month, $pre_year);
            $month_days = ($pre_month_days - $pre_day) + $pre_day;
            $end_date = new DateTime($year.'-'.$month.'-'.$pre_day);
        } else {
            $first_date = new DateTime($year.'-'.$month.'-01');
            $end_date = new DateTime($year.'-'.$month.'-'.$month_days);
        }
        $first_date = $first_date->format('Y-m-d');
        $end_date = $end_date->format('Y-m-d');

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
            }
        }
        $data = $tmp = [];
        $this->load->model('model_user');
        $users = $this->model_user->find_exist_month_listmonth($csv_date, $end_date);
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
                $absent_count = (int)$month_days - (int)$work_count;
                $work_hour = sprintf('%d:%02d', floor(array_sum($fact_hour_data[$user_id])/60), array_sum($fact_hour_data[$user_id])%60);
                $work_hour2 = array_sum($fact_hour_data[$user_id]);
            } else {
                $absent_count = 0;
                $work_count = 0;
                $work_hour = '';
            }
            if (isset($over_hour_data[$user_id])) {
                $over_count = count($over_hour_data[$user_id]);
                $over_hour = sprintf('%d:%02d', floor(array_sum($over_hour_data[$user_id])/60), array_sum($over_hour_data[$user_id])%60);
                $over_hour2 = array_sum($over_hour_data[$user_id]);
            } else {
                $over_count = 0;
                $over_hour = '';
            }
            if (isset($night_hour_data[$user_id])) {
                $night_count = count($night_hour_data[$user_id]);
                $night_hour = sprintf('%d:%02d', floor(array_sum($night_hour_data[$user_id])/60), array_sum($night_hour_data[$user_id])%60);
                $night_hour2 = array_sum($night_hour_data[$user_id]);
            } else {
                $night_count = 0;
                $night_hour = '';
            }
            if (isset($late_hour_data[$user_id])) {
                $late_count = count($late_hour_data[$user_id]);
                $late_hour = sprintf('%d:%02d', floor(array_sum($late_hour_data[$user_id])/60), array_sum($late_hour_data[$user_id])%60);
                $late_hour2 = array_sum($late_hour_data[$user_id]);
            } else {
                $late_count = 0;
                $late_hour = '';
            }
            if (isset($left_hour_data[$user_id])) {
                $left_count = count($left_hour_data[$user_id]);
                $left_hour = sprintf('%d:%02d', floor(array_sum($left_hour_data[$user_id])/60), array_sum($left_hour_data[$user_id])%60);
                $left_hour2 = array_sum($left_hour_data[$user_id]);
            } else {
                $left_count = 0;
                $left_hour = '';
            }
            if ($work_hour2 > 0 && isset($over_hour2)) {
                $normal_minute = $work_hour2 - $over_hour2 - $night_hour2;
                $normal_hour = sprintf('%d:%02d', floor($normal_minute/60), $normal_minute%60);
            } else {
                $normal_minute = 0;
                $normal_hour = '';
            }
            if ($type === '001') {
                $data[] = [
                    str_pad($user_id, (int)$config_data['id_size'], '0', STR_PAD_LEFT), // 従業員ID
                    $user->name_sei.' '.$user->name_mei, // 従業員名
                    $year.'/'.$month.'/01',
                    @$work_count ?: '0', // 勤務日数
                    '0', // 欠勤日数
                    '0', // 有休日数
                    @$work_hour ?: '0', // 勤務時間
                    @$over_hour ?: '0', // 残業時間
                    @$night_hour ?: '0', // 深夜残業時間
                    '0', // 早出時間
                    '0', // 延長時間
                    '0', // 休日出勤時間1
                    '0', // 休日残業時間1
                    '0', // 休日深夜残業時間1
                    '0', // 休日出勤時間2
                    '0', // 休日残業時間2
                    '0', // 休日深夜残業時間2
                    '0', // 45-60時間残業
                    '0', // 60越残業時間
                    '0', // 遅刻早退時間
                    '0' // 有給時間
                ];
            }

            // $data[] = [
            //   'user_id'=>str_pad($user_id, $id_size, '0', STR_PAD_LEFT), // 従業員ID
            //   'user_name'=>$user->name_sei.' '.$user->name_mei, // 従業員名
            //   'month_days'=>(int)$month_days, // 月数
            //   'work_count'=>(int)$work_count, // 勤務日数
            //   'absence_count'=>(int)$absent_count, // 欠勤日数
            //   'work_hour'=>$work_hour, // 勤務時間 0:00
            //   'work_minute'=> (int)$work_hour2, // 勤務時間 分
            //   'normal_hour'=>$normal_hour,
            //   'normal_minute'=> (int)$normal_minute,
            //   'over_count'=>$over_count,
            //   'over_hour'=>$over_hour,
            //   'over_minute'=> $over_hour2,
            //   'night_count'=>$night_count,
            //   'night_hour'=>$night_hour,
            //   'night_minute'=> $night_hour2,
            //   'late_count'=>$late_count,
            //   'late_hour'=>$late_hour,
            //   'late_minute'=> $late_hour2,
            //   'left_count'=>$left_count,
            //   'left_hour'=>$left_hour,
            //   'left_minute'=> $left_hour2
            // ];
        }

        $this->load->helper('file');
        delete_files('./files/');

        if ($type === '001') {
            $file_name = 'KINTAI6.csv';
            $column = ['社員コード', '社員氏名', 'タイムカード年月', '勤務日数', '欠勤日数', '有給日数', '勤務時間', '残業時間', '深夜残業時間', '早出時間', '延長時間', '休日出勤時間1', '休日残業時間1', '休日深夜残業時間1', '休日出勤時間2', '休日残業時間2', '休日深夜残業時間2', '45-60時間残業', '60越残業時間', '遅刻早退時間', '有給時間'];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($column, null, 'A1');
            $sheet->fromArray($data, null, 'A2');

            $writer = new Csv($spreadsheet);
            $writer->setLineEnding("\r\n");
            $writer->setEnclosure('');
            $writer->setSheetIndex(0);
            $writer->save('./files/'.$file_name);

            $csvFile = file_get_contents('./files/'.$file_name);
            $csvFile2 = mb_convert_encoding($csvFile , "SJIS" , "UTF-8");
            file_put_contents('./files/'.$file_name , $csvFile2);

            $this->load->helper('download');
            force_download('./files/'.$file_name, null);
        }
    }
}