<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Analyze extends CI_Controller
{
    public function status()
    {
        $now = new DateTimeImmutable();
        $nowTime = $now->format(' H:i:s');
        $status_data['dk_datetime'] = $this->input->get('dk_date').$nowTime;
        $status_data['flag'] = 'auto';

        $this->load->library('process_status_lib'); // 分析処理用 lib 読込
        if ($this->process_status_lib->status($status_data)) {
            $message = $status_data['dk_datetime'].' : update ok';
        } else {
            $message = $status_data['dk_datetime'].' : error!';
        }

        echo $message;
    }

    public function notice_status()
    {
        $now = new DateTimeImmutable();
        $now_date = $now->format('Y-m-d');
        $now_datetime_w = $now->format('Y年m月d日H時i分');

        $message = "勤務状況\n";
        $message .= $now_datetime_w."現在\n\n";

        $time_data = [];
        $none_work = $work = [];
        $this->load->model('model_time');
        $time_data = $this->model_time->gets_now_users_status($now_date);
        foreach ($time_data as $value) {
            $user_id = $value->user_id;
            $status_flag = (int)$value->status_flag;
            $name = $value->name_sei.' '.$value->name_mei;
            $status = $value->status;
            $in_time = "";
            if ($value->in_time) {
                $time = new DateTimeImmutable($value->in_time);
                $in_time = $time->format('H:i');
            }
            $out_time = "";
            if ($value->out_time) {
                $time = new DateTimeImmutable($value->out_time);
                $out_time = $time->format('H:i');
            }
            if ($status_flag === 3 || $status_flag === 9 || $status_flag === 10 || $status_flag === 22 || $status_flag === 29) {
                $none_work[] = $name."(".$user_id."): ".$status;
            } else {
                $work[] = $name."(".$user_id."): ".$status." ".$in_time."〜".$out_time;
            }
        }

        $message .= "出勤: ".count($work)."名\n";
        foreach ($work as $value) {
            $message .= $value."\n";
        }

        $message .= "\n未出勤: ".count($none_work)."名\n";
        foreach ($none_work as $value) {
            $message .= $value."\n";
        }

        // LINE通知
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        if ((int)$config_data['line_flag'] === 1) { // line_flag = 1 -> LINE通知
            $line_message = [
                'type' => 'text',
                'text' => $message
            ];
            $this->load->library('process_line_lib'); // LINE通知用 lib 読込
            $this->process_line_lib->line_message($line_message, '');
        }
        // mail通知
        // notice_mail_flag = 1 メールのみ　notice_mail_flag = 9 すべて
        if ((int)$config_data['notice_mail_flag'] === 2 || (int)$config_data['notice_mail_flag'] === 9) {
            $mail_subject = '勤務状況通知:'.$now_datetime_w;
            $this->load->library('process_mail_lib');
            $this->process_mail_lib->mail_send($mail_subject, $message);
        }
        // Slack通知
        if ((int)$config_data['slack_flag'] === 1) {
            $slack_message = [
                'username' => '打刻keeperBOT',
                'text' => $message
            ];
            $this->load->library('process_slack_lib'); // Slack通知用 lib 読込
            $this->process_slack_lib->slack_message($slack_message);
        }

        echo $now_datetime_w." 勤務状況 送信ok";
    }

    // 通知　シフト情報
    public function notice_shift()
    {
        $now = new DateTime();
        $now_date = $now->format('Y-m-d');
        $now_datetime_w = $now->format('Y年m月d日H時i分');
        $next = new DateTime('+1 day');
        $next_date = $next->format('Y-m-d');

        $this->load->database();
        // 従業員データ取得
        $result = $this->db->query("SELECT CONCAT(`name_sei`, ' ', `name_mei`) AS `name`, `user_id` FROM user_data WHERE `state` = 1")->result();
        $user_data = array_column($result, 'name', 'user_id');
        unset($result);
        // シフトデータ取得
        $shift_data = $this->db->query("SELECT `user_id`, substring(`in_time`, 1, 5) AS `in_time`, substring(`out_time`, 1, 5) AS `out_time`, `status` FROM shift_data WHERE `dk_date` = '{$next_date}' ORDER BY `in_time` ASC, `user_id` ASC")->result();

        $work = [];
        $holiday = [];
        $paid = [];
        foreach ($shift_data as $value) {
            if ($value->status == 0 && $value->in_time) {
                $work[] = $value->in_time."〜".$value->out_time." ".$user_data[$value->user_id];
            }
            if ($value->status == 1) {
                $holiday[] = $user_data[$value->user_id];
            }
            if ($value->status == 2) {
                $paid[] = $user_data[$value->user_id];
            }
        }
        unset($value);
        $message = "明日(".$next_date.") 出勤予定者\n";
        if ($work) {
            $message .= "\n";
            $message .= "出勤：".count($work)."名\n";
            foreach ($work as $value) {
                $message .= $value."\n";
            }
        } else {
            $message .= "\n";
            $message .= "出勤なし\n";
        }
        if ($holiday) {
            $message .= "\n";
            $message .= "公休：".count($holiday)."名\n";
            foreach ($holiday as $value) {
                $message .= $value."\n";
            }
        } else {
            $message .= "\n";
            $message .= "公休なし\n";
        }
        if ($paid) {
            $message .= "\n";
            $message .= "有給：".count($paid)."名\n";
            foreach ($paid as $value) {
                $message .= $value."\n";
            }
        }

        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        if ((int)$config_data['line_flag'] === 1) { // line_flag = 1 -> LINE通知
            $line_message = [
                'type' => 'text',
                'text' => $message
            ];
            $this->load->library('process_line_lib'); // LINE通知用 lib 読込
            $this->process_line_lib->line_message($line_message, '');
        }

        // mail通知
        // notice_mail_flag = 1 メールのみ　notice_mail_flag = 9 すべて
        if ((int)$config_data['notice_mail_flag'] === 2 || (int)$config_data['notice_mail_flag'] === 9) {
            $mail_subject = '勤務状況通知:'.$now_datetime_w;
            $this->load->library('process_mail_lib');
            $this->process_mail_lib->mail_send($mail_subject, $message);
        }

        // Slack通知
        if ((int)$config_data['slack_flag'] === 1) {
            $slack_message = [
                'username' => '打刻keeperBOT',
                'text' => $message
            ];
            $this->load->library('process_slack_lib'); // Slack通知用 lib 読込
            $this->process_slack_lib->slack_message($slack_message);
        }

        echo $now_datetime_w." 勤務状況 送信ok";
    }
}