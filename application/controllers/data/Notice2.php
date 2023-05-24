<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Notice2 extends CI_Controller
{
    public function new()
    {
        $to_user_id = $this->input->post('user_id'); // 申請者従業員ID
        $to_date = $this->input->post('to_date'); // 申請希望日
        $notice_flag = (int)$this->input->post('notice_flag'); // 申請ID
        $end_date = $this->input->post('end_date') ?: NULL; // 申請希望日複数時
        $notice_in_time = $this->input->post('notice_in_time') ?: NULL; // 修正出勤時刻
        $notice_out_time = $this->input->post('notice_out_time') ?: NULL; // 修正退勤時刻
        $notice_text = $this->input->post('notice_text'); // 修正コメント
        $notice_name = $this->input->post('user_name'); // 申請従業員名
        $before_in_time = $this->input->post('select_in_time');
        $before_out_time = $this->input->post('select_out_time');
        $before_status = $this->input->post('select_time_status');
        $before_shift_in_time = $this->input->post('select_in_shift');
        $before_shift_out_time = $this->input->post('select_out_shift');
        $before_shift_status = $this->input->post('select_shift_status');

        // 申請の項目名を取得
        $select = 'notice_status_title';
        $where = ['notice_status_id'=> $notice_flag];
        $this->load->model('model_notice_status_data');
        $notice_title = $this->model_notice_status_data->find_row($select, $where)->notice_status_title;

        // 現在時刻を取得
        $now = new DateTime();
        $notice_datetime = $now->format('Y-m-d H:i:s');
        $notice_datetimeW = $now->format('Y年m月d日 H時i分');

        // 表示用
        $toDate = new DateTime($to_date);
        if ($end_date) {
            $endDate = new DateTime($end_date);
            $dateW = "申請希望日 ".$toDate->format('Y年m月d日')."から".$endDate->format('Y年m月d日')."まで";
        } else {
            $dateW = "申請希望日 ".$toDate->format('Y年m月d日');
        }

        // 通知用
        $message_title = "【新規 {$notice_title}】申請者：{$notice_name} ID:{$to_user_id} {$notice_datetimeW}"; // 表題タイトル
        $message_contents = "【{$notice_title}】\n"; // 本文
        $message_contents .= "申請者  {$notice_name} ID({$to_user_id})\n";
        $message_contents .= "{$dateW}\n";
        if ($notice_in_time) {
            $message_contents .= "{$notice_in_time} - {$notice_out_time}\n";
        } else {
            $message_contents .= "\n";
        }
        if ($notice_text) {
            $message_contents .= "コメント\n";
            $message_contents .= "{$notice_text}\n\n";
        } else {
            $message_contents .= "\n";
        }
        $message_contents .= "【申請対象日データ】\n";
        $message_contents .= "勤務   {$before_status} {$before_in_time} - {$before_out_time}\n";
        $message_contents .= "シフト {$before_shift_status} {$before_shift_in_time} - {$before_shift_out_time}\n\n";
        $message_contents .= "この通知は打刻keeperから送信されました。\n";
        $message_contents .= "{$notice_datetimeW}送信\n";

        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('id, config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        // LINE通知
        if ((int)$config_data['line_flag'] === 1) {
            $line_message = [
                'type' => 'text',
                'text' => $message_title."\n\n".$message_contents
            ];
            $this->load->library('process_line_lib'); // LINE通知用 lib 読込
            $this->process_line_lib->line_message($line_message, $to_user_id);
        }
        // mail通知 notice_mail_flag = 2 通知のみ　notice_mail_flag = 9 すべて
        if ((int)$config_data['notice_mail_flag'] === 2 || (int)$config_data['notice_mail_flag']  === 9) {
            $this->load->library('process_mail_lib'); // mail通知用 lib 読込
            $this->process_mail_lib->mail_send($message_title, $message_contents);
        }
        // Slack通知
        if ((int)$config_data['slack_flag'] === 1) {
            $slack_message = [
            'username' => '打刻keeperBOT',
            'text' => $message_title."\n\n".$message_contents
            ];
            $this->load->library('process_slack_lib'); // Slack通知用 lib 読込
            $this->process_slack_lib->slack_message($slack_message);
        }

        // 保存
        $notice_id = time();
        $this->load->model('model_notice_data');
        $this->model_notice_data->notice_id = $notice_id;
        $this->model_notice_data->notice_datetime = $notice_datetime;
        $this->model_notice_data->to_user_id = (int)$to_user_id;
        $this->model_notice_data->to_date = $to_date;
        $this->model_notice_data->notice_flag = $notice_flag;
        $this->model_notice_data->notice_in_time = $notice_in_time;
        $this->model_notice_data->notice_out_time = $notice_out_time;
        $this->model_notice_data->notice_status = 0;
        $this->model_notice_data->before_in_time = $before_in_time;
        $this->model_notice_data->before_out_time = $before_out_time;
        $this->model_notice_data->end_date = $end_date;
        if ($this->model_notice_data->insert()) {
            $data = 'ok';
        } else {
            $data = 'err_notice_data';
        }
        $this->load->model('model_notice_text_data');
        $this->model_notice_text_data->notice_id = $notice_id;
        $this->model_notice_text_data->text_datetime = $notice_datetime;
        $this->model_notice_text_data->user_id = (int)$to_user_id;
        $this->model_notice_text_data->notice_text = $notice_text;
        $this->model_notice_text_data->notice_status = 0;
        if ($this->model_notice_text_data->insert()) {
            $data = 'ok';
        } else {
            $data = 'err_notice_text';
        }
        $this->load->model('model_notice_text_users');
        $this->model_notice_text_users->notice_text_id = $notice_id;
        $this->model_notice_text_users->user_id = (int)$to_user_id;
        if ($this->model_notice_text_users->insert()) {
            $data = 'ok';
        } else {
            $data = 'err_notice_text_users';
        }
    
        // 出力
        $this->output
        ->set_content_type('application/text')
        ->set_output($data);
    }

    //
    public function submit_message()
    {
        $now = new DateTime();
        $notice_status = (int)$this->input->post('flag'); // 通知ステータス　0 : 返信 | 1 : 承認 | 2 : NG
        $notice_text_data = [
            'notice_datetime' => $now->format('Y-m-d H:i:s'),
            'notice_id' => $this->input->post('notice_id'),
            'notice_text' => $this->input->post('message'),
            'notice_status' => $notice_status,
            'to_user_id' => (int)$this->input->post('user_id')
        ];
        $notice_flag = (int)$this->input->post('notice_flag'); // 申請種別ID　フラグ　1 : 修正依頼
        $this->load->model('model_notice_data_bk');
        $notice_title = $this->model_notice_data_bk->get_notice_status_title($notice_flag)->notice_status_title; // 申請タイトル
        $notice_date = $this->input->post('notice_date');
        $notice_end_date = $this->input->post('notice_end_date') !== '0000-00-00' ?: '';
        $from_userId = (int)$this->input->post('from_userId');

        $this->load->model('model_user');
        $to_user_data = $this->model_user->find_all_userid($notice_text_data['to_user_id']);
        $to_user_name = $to_user_data->name_sei.' '.$to_user_data->name_mei;
        $from_user_data = $this->model_user->find_all_userid($from_userId);
        $from_user_name = $from_user_data->name_sei.' '.$from_user_data->name_mei;

        if ($notice_status > 0) {
            $output_message = $this->model_notice_data_bk->update_data($notice_text_data) ? 'ok' : 'err_notice_data';
            if ($notice_status === 1) {

            }
        }

        $output_message = $this->model_notice_data_bk->insert_text($notice_text_data) ? 'ok' : 'err_notice_text';
    }
}