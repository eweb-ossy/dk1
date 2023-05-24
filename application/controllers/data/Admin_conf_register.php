<?php
defined('BASEPATH') or exit('No direct script access alllowed');
// 各種データ読み込み用

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class Admin_conf_register extends MY_Controller
{
    // 労働時間データの読み込み
    public function work_time()
    {
        $config_data = $this->data['configs']; // config data取得

        $config['upload_path'] = './files/';
        if ((int)$config_data['download_filetype']->value === 1) {
            $config['allowed_types'] = 'xlsx';
            $config['file_name'] = 'work_register.xlsx';
        }
        if ((int)$config_data['download_filetype']->value === 2) {
            $config['allowed_types'] = 'xls';
            $config['file_name'] = 'work_register.xls';
        }
        if ((int)$config_data['download_filetype']->value === 3) {
            $config['allowed_types'] = 'csv';
            $config['file_name'] = 'work_register.csv';
        }
        $config['overwrite'] = true;
        $this->load->library('upload', $config);
        $message = '登録エラー';
        if ($this->upload->do_upload('files')) {
            if ((int)$config_data['download_filetype']->value === 1) {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load('./files/work_register.xlsx');
            }
            if ((int)$config_data['download_filetype']->value === 2) {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xls();
                $spreadsheet = $reader->load('./files/work_register.xls');
            }
            if ((int)$config_data['download_filetype']->value === 3) {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Csv();
                $spreadsheet = $reader->load('./files/work_register.csv');
            }
            $sheet = $spreadsheet->getActiveSheet()->toArray();
            foreach ($sheet as $val) {
                $y = (int)$val[0]; // 0 年
                $m = (int)$val[1]; // 1 月
                $d = (int)$val[2]; // 2 日
                if (!checkdate($m, $d, $y)) { // 日付チェック
                    continue;
                }
                if (!$val[3]) { // 3 従業員IDの有無
                    continue;
                }
                $status_data['flag'] = 'edit'; // フラグ
                $status_data['user_id'] = (int)$val[3];
                $status_data['dk_datetime'] = "{$y}-{$m}-{$d}";
                if ($val[4]) { // 4 出勤時刻
                    $status_data['in_work_time'] = $val[4].':00';
                    $status_data['revision_in'] = 1;
                } else {
                    $row = $this->db->query("SELECT in_work_time FROM time_data WHERE `user_id` = '{$status_data['user_id']}' AND dk_date = '{$status_data['dk_datetime']}'")->row();
                    if (isset($row->in_work_time)) {
                        $status_data['in_work_time'] = $row->in_work_time;
                    } else {
                        $status_data['in_work_time'] = NULL;
                        $status_data['revision_in'] = 0;
                    }
                }
                if ($val[5]) { // 5 退勤時刻
                    $status_data['out_work_time'] = $val[5].':00';
                    $status_data['revision_out'] = 1;
                } else {
                    $row = $this->db->query("SELECT out_work_time FROM time_data WHERE `user_id` = '{$status_data['user_id']}' AND dk_date = '{$status_data['dk_datetime']}'")->row();
                    if (isset($row->out_work_time)) {
                        $status_data['out_work_time'] = $row->out_work_time;
                    } else {
                        $status_data['out_work_time'] = NULL;
                        $status_data['out_work_time'] = 0;
                    }
                }
                if ($val[6]) {
                    $status_data['rest'] = $val[6];
                } else {
                    $row = $this->db->query("SELECT rest FROM time_data WHERE `user_id` = '{$status_data['user_id']}' AND dk_date = '{$status_data['dk_datetime']}'")->row();
                    if (isset($row->rest)) {
                        $status_data['rest'] = $row->rest;
                    } else {
                        $status_data['rest'] = 0;
                    }
                }
                if ($val[7]) {
                    $status_data['memo'] = $val[7];
                } else {
                    $row = $this->db->query("SELECT memo FROM time_data WHERE `user_id` = '{$status_data['user_id']}' AND dk_date = '{$status_data['dk_datetime']}'")->row();
                    if (isset($row->memo)) {
                        $status_data['memo'] = $row->memo;
                    } else {
                        $status_data['memo'] = NULL;
                    }
                }
                $status_data['revision'] = 1; // 修正フラグ
                $now = new DateTimeImmutable();
                $status_data['revision_datetime'] = $now->format('Y-m-d H:i:s');
                $status_data['revision_user'] = $this->session->user_name;
                // 分析
                $this->load->library('process_status_lib'); // 分析処理用 lib 読込
                if ($this->process_status_lib->status($status_data)) {
                    $message = '登録完了';
                } else {
                    $message = '登録エラー';
                }
            }
        }
        $this->output
        ->set_content_type('application/text')
        ->set_output($message);
    }
}