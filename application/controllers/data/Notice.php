<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Notice extends CI_Controller
{

    /**
     * 通知データ 一覧　出力
     *
     * @return json $data
     */
    public function get_data()
    {
        $data = [];
        $notice_data = [];
        $user_id = $this->input->post('user_id');
        $this->load->model('model_notice_data_bk');
        $notice_data = $this->model_notice_data_bk->gets_to_all();
        $auth_data = [];
        $auth_data = $this->model_notice_data_bk->gets_auth($user_id);
        $auth_data = array_column($auth_data, 'low_user_id');
        array_push($auth_data, $user_id);
        //
        $this->load->model('model_user');
        foreach ($notice_data as $value) {
            if (in_array($value->to_user_id, $auth_data)) {
                $user_name = $this->model_user->get_now_state_userid($value->to_user_id);
                $data[] = [
                    'user_id'=> $value->to_user_id,
                    'user_name'=> $user_name->name_sei.' '.$user_name->name_mei,
                    'notice_flag'=> $value->notice_flag,
                    'to_date'=> $value->to_date,
                    'notice_datetime'=> $value->notice_datetime,
                    'notice_id'=> $value->notice_id,
                    'notice_status'=>$value->notice_status
                ];
            }
        }
        $this->output // 出力 json
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    /**
     * 通知データ　単体 + message　出力
     *
     * @return json $data
     */
    public function get_id_data()
    {
        $notice_data = [];
        $notice_id = $this->input->post('notice_id');
        $this->load->model('model_notice_data_bk');
        $notice_data = $this->model_notice_data_bk->get_notice_id($notice_id);
        $this->load->model('model_user');
        $user_name = $this->model_user->get_now_state_userid($notice_data->to_user_id);
        $user_data = ['user_name' =>$user_name->name_sei.' '.$user_name->name_mei];
        $notice_text_data = $this->model_notice_data_bk->get_text_notice_id($notice_id);
        foreach ($notice_text_data as $val) {
            $user_name = $this->model_user->get_now_state_userid($val->user_id);
            $message_data[] = [
                'text_datetime' => $val->text_datetime,
                'user_name' => $user_name->name_sei.' '.$user_name->name_mei,
                'message_text' => $val->notice_text,
                'user_id' => $val->user_id
            ];
        }
        $massage = ['massage' => $message_data];
        $data = (array)$notice_data + (array)$user_data + (array)$massage;
        $this->output // 出力 json
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    /**
     * 指定日付の従業員出勤状況を返す
     *
     * @param int     $user_id
     * @param string  $date
     *
     * @return array  $outPutData
     **/
    public function user_work_status()
    {
        $user_id = $this->input->post('user_id');
        $date = $this->input->post('date');

        // 勤務状況の取得
        $in_time = $out_time = $status = '';
        $time_data = [];
        $select = 'in_time, out_time, in_work_time, out_work_time, status, revision';
        $where = [
            'user_id'=>(int)$user_id,
            'dk_date'=>$date
        ];
        $this->load->model('model_time_data');
        $result = $this->model_time_data->find_row($select, $where);
        if ($result) {
            $revision = (int)$result->revision;
            if ($result->in_time && !$result->in_work_time) {
                $in_time = $result->in_time;
            }
            if (!$result->in_time && $result->in_work_time) {
                $in_time = $result->in_work_time;
            }
            if ($result->in_time && $result->in_work_time && $revision === 0) {
                $in_time = $result->in_time;
            }
            if ($result->in_time && $result->in_work_time && $revision === 1) {
                $in_time = $result->in_work_time;
            }
            if ($result->out_time && !$result->out_work_time) {
                $out_time = $result->out_time;
            }
            if (!$result->out_time && $result->out_work_time) {
                $out_time = $result->out_work_time;
            }
            if ($result->out_time && $result->out_work_time && $revision === 0) {
                $out_time = $result->out_time;
            }
            if ($result->out_time && $result->out_work_time && $revision === 1) {
                $out_time = $result->out_work_time;
            }
            $status = $result->status;
        }
        $time_data['time']['in_time'] = $in_time;
        $time_data['time']['out_time'] = $out_time;
        $time_data['time']['status'] = $status;

        // シフトの取得
        $shift_data['shift'] = [
            'status'=>'',
            'in_time'=>'',
            'out_time'=>''
        ];
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('id, config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');
        if ((int)$config_data['auto_shift_flag'] === 1) {
            $this->load->library('process_rules_lib'); // rules lib 読込
            $rules = $this->process_rules_lib->get_rule($user_id);
            //
            if ($rules->basic_in_time) {
                $basic_in_time = substr($rules->basic_in_time, 0, 5);
            }
            if ($rules->basic_out_time) {
                $basic_out_time = substr($rules->basic_out_time, 0, 5);
            }
            if ($rules->basic_rest_weekday) {
                $basic_rest_week = str_split($rules->basic_rest_weekday);
            }
            $datetime = new DateTime($date);
            $this->load->helper('holiday_date');
            $holiday_datetime = new HolidayDateTime($date);
            $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
            if (isset($basic_rest_week)) {
                if ($basic_rest_week[$w] == 1) {
                    $shift_data['shift'] = [
                        'status'=>1,
                        'in_time'=>'',
                        'out_time'=>''
                    ];
                }
                if ($basic_rest_week[$w] == 0) {
                    $shift_data['shift'] = [
                        'status'=>0,
                        'in_time'=>$basic_in_time,
                        'out_time'=>$basic_out_time
                    ];
                }
            }
        }
        $select = 'in_time, out_time, status';
        $where = [
            'user_id'=>(int)$user_id,
            'dk_date'=>$date
        ];
        $this->load->model('model_shift_data');
        if ($this->model_shift_data->find_row($select, $where)) {
            $shift_data['shift'] = $this->model_shift_data->find_row($select, $where);
        }

        $outPutData = array_merge($time_data, $shift_data);

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($outPutData));
    }

    /**
     * 新規申請　保存
     * @param string  $to_user_id         申請者のuser_id
     * @param string  $to_date            申請希望年月日
     * @param string  $end_date           申請希望年月日 複数時
     * @param int     $notice_flag        申請ステータスID
     *  1:修正依頼 2:削除依頼 3:遅刻依頼 4:早退依頼 5:残業依頼 6:有給依頼 7:欠勤依頼
     *  8:その他依頼 11:休暇依頼
     * @param string  $notice_text        申請コメント
     * @param string  $notice_name        申請者名
     * @param array   $noticeHopeData     申請希望日の勤務・シフト状況
     */
    public function new()
    {
        $to_user_id = $this->input->post('to_user_id');
        $to_date = $this->input->post('to_date');
        $end_date = $this->input->post('end_date') ?: NULL;
        if ($end_date) {
            $dateW = "申請希望日 {$to_date} から {$end_date} まで";
        } else {
            $dateW = "申請希望日 {$to_date}";
        }
        $notice_flag = (int)$this->input->post('notice_flag');
        $select = 'notice_status_title';
        $where = ['notice_status_id'=>$notice_flag];
        $this->load->model('model_notice_status_data');
        $notice_title = $this->model_notice_status_data->find_row($select, $where)->notice_status_title;
        $notice_in_time = $this->input->post('notice_in_time') ?: NULL;
        $notice_in_timeW = $notice_in_time ? "申請出勤時刻 ".substr($notice_in_time, 0, 5) : "";
        $notice_out_time = $this->input->post('notice_out_time') ?: NULL;
        $notice_out_timeW = $notice_out_time ? "申請退勤時刻 ".substr($notice_out_time, 0, 5) : "";
        $notice_text = $this->input->post('notice_text');
        $notice_name = $this->input->post('user_name');
        $noticeHopeData = $this->input->post('noticeHopeData');
        $before_in_time = $noticeHopeData['time']['in_time'] ?: NULL;
        $before_in_timeW = $before_in_time ? substr($before_in_time, 0, 5) : "未出勤";
        $before_out_time = $noticeHopeData['time']['out_time'] ?: NULL;
        $before_out_timeW = $before_out_time ? substr($before_out_time, 0, 5) : "未退勤";
        $before_status = $noticeHopeData['time']['status'] ?: "勤務なし";
        $before_shift_in_time = $noticeHopeData['shift']['in_time'] ?: "未登録";
        $before_shift_out_time = $noticeHopeData['shift']['out_time'] ?: "未登録";
        $before_shift_status = $noticeHopeData['shift']['status'] ?: "未登録";

        $notice_id = time();
        $now = new DateTime();
        $notice_datetime = $now->format('Y-m-d H:i:s');
        $notice_datetimeW = $now->format('Y年m月d日 H:i');

        $message_title = "【新規 {$notice_title}】申請者：{$notice_name} ID:{$to_user_id} {$notice_datetimeW}";
        $message_contents =
"【{$notice_title}】
申請者  {$notice_name} ID {$to_user_id}
{$dateW}
{$notice_in_timeW} - {$notice_out_timeW}
コメント
{$notice_text}

【申請対象日データ】
勤務   {$before_status} {$before_in_timeW} - {$before_out_timeW}
シフト {$before_shift_status} {$before_shift_in_time} - {$before_shift_out_time}

送信日時 {$notice_datetimeW}
────────────
打刻keeper 配信システム
────────────
この通知は打刻keeperから送信されました。

Copyright (C) e-web, Inc. All Rights Reserved.
";

        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('id, config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        // 通知
        //
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

    /**
     * 通知　保存
     */
    public function submit_message()
    {
        $notice_text_data = [];
        $now = new DateTimeImmutable();
        $notice_text_data['notice_datetime'] = $now->format('Y-m-d H:i:s'); // 通知 日時
        $now_datetime = $now->format('Y年m月d日 H時i分'); // メッセージ表示用
        $notice_text_data['notice_id'] = $this->input->post('notice_id'); // 通知ID
        $notice_text_data['notice_text'] = $this->input->post('message'); // 通知メッセージ
        $notice_text_data['notice_status'] = $this->input->post('flag'); // 通知ステータス 0->返信 1->承認 2->NG
        $notice_text_data['to_user_id'] = (int)$this->input->post('user_id'); // 発信者　従業員ID

        // 発信者情報の取得
        $this->load->model('model_user');
        $to_user_data = $this->model_user->find_all_userid($notice_text_data['to_user_id']);
        $to_user_name = $to_user_data->name_sei.' '.$to_user_data->name_mei; // 発信者名

        $this->load->model('model_notice_data_bk');

        // 申請依頼タイトル取得
        $notice_flag = $this->input->post('notice_flag'); // 申請フラグ
        $notice_title = $this->model_notice_data_bk->get_notice_status_title((int)$notice_flag)->notice_status_title; // 申請タイトル名

        $notice_date = $this->input->post('notice_date'); // 申請日付
        $notice_end_date = $this->input->post('notice_end_date'); // 申請日付　end
        if ($notice_end_date === '0000-00-00') {
            $notice_end_date = '';
        }

        // 申請者情報の取得
        $from_userId = (int)$this->input->post('from_userId'); // 申請者　従業員ID
        $from_user_data = $this->model_user->find_all_userid($from_userId);
        $from_user_name = $from_user_data->name_sei.' '.$from_user_data->name_mei; // 申請者名

        // 自動処理
        if ((int)$notice_text_data['notice_status'] > 0) {
            // 承認orNGの場合はnotice_dataを更新
            if ($this->model_notice_data_bk->update_data($notice_text_data)) {
                $data = 'ok';
            } else {
                $data = 'err_notice_data';
            }
            // 申請 -> 承認時
            if ($notice_text_data['notice_status'] == 1) {
                $noticeData = $this->model_notice_data_bk->get_notice_id($notice_text_data['notice_id']);
                $notice_text_data['notice_text'] = '承認';
                // 承認時前のテキストデータを取得
                $noticeTextData = $this->model_notice_data_bk->get_text_notice_id_last($notice_text_data['notice_id']);

                // 承認時　申請別処理
                switch ($notice_flag) {
                    case 1: // 修正申請 承認時
                        $status_data['flag'] = 'notice';
                        $status_data['user_id'] = $from_userId;
                        $status_data['dk_datetime'] = $notice_date;
                        $status_data['notice_memo'] = $noticeTextData->notice_text; // 最終申請テキストデータ
                        // find row
                        $select = 'in_time, out_time, in_work_time, out_work_time';
                        $where = [
                            'dk_date'=>$notice_date,
                            'user_id'=>(int)$from_userId
                        ];
                        $this->load->model('Model_time_data');
                        $time_data = $this->Model_time_data->find_row($select, $where);

                        $in_time = $time_data->in_time; // 元 実出勤時刻
                        $out_time = $time_data->out_time; // 元 実退勤時刻
                        $in_time_array = date_parse_from_format('h:i:s', $in_time);
                        $out_time_array = date_parse_from_format('h:i:s', $out_time);
                        $in_work_time = $time_data->in_work_time; // 元 出勤時刻
                        $out_work_time = $time_data->out_work_time; // 元 退勤時刻
                        $in_work_time_array = date_parse_from_format('h:i:s', $in_work_time);
                        $out_work_time_array = date_parse_from_format('h:i:s', $out_work_time);
                        $notice_in_time = $noticeData->notice_in_time; // 申請 出勤時刻
                        $notice_out_time = $noticeData->notice_out_time; // 申請 退勤時刻
                        $notice_in_time_array = date_parse_from_format('h:i:s', $notice_in_time);
                        $notice_out_time_array = date_parse_from_format('h:i:s', $notice_out_time);
                        
                        // 修正前に現状の出退勤時刻を変数に入れてNULLを回避　#1 21-07-29
                        $status_data['in_work_time'] = $in_work_time;
                        $status_data['out_work_time'] = $out_work_time;

                        // 申請 出勤時刻　比較 -> 元 実出勤時刻 & 出勤時刻と違う場合は出勤時刻を修正 　　改修　#2 21-07-30
                        if ($notice_in_time_array['hour'].$notice_in_time_array['minute'] !== $in_time_array['hour'].$in_time_array['minute']) {
                            if ($notice_in_time_array['hour'].$notice_in_time_array['minute'] !== $in_work_time_array['hour'].$in_work_time_array['minute']) {
                                $status_data['revision_in'] = 1; // 修正フラグ
                                $status_data['in_work_time'] = $notice_in_time;
                            }
                        }
                        // 申請 退勤時刻　比較 -> 元 実退勤時刻 & 退勤時刻と違う場合は退勤時刻を修正　　改修　#2 21-07-30
                        if ($notice_out_time_array['hour'].$notice_out_time_array['minute'] !== $out_time_array['hour'].$out_time_array['minute']) {
                            if ($notice_out_time_array['hour'].$notice_out_time_array['minute'] !== $out_work_time_array['hour'].$out_work_time_array['minute']) {
                                $status_data['revision_out'] = 1; // 修正フラグ
                                $status_data['out_work_time'] = $notice_out_time;
                            }
                        }

                        $status_data['revision'] = 2; // 修正フラグ #修正 220203 自動修正ではrevisionに　2　を入れる
                        $status_data['revision_user'] = $to_user_name; // 修正者
                        $status_data['revision_datetime'] = $notice_text_data['notice_datetime']; // 修正時刻

                        // 分析
                        $this->load->library('process_status_lib'); // 分析処理用 lib 読込
                        $this->process_status_lib->status($status_data);
                    break;

                    case 2: // 削除申請　承認時
                        $time_data['flag'] = 'notice';
                        $time_data['user_id'] = $from_userId;
                        $time_data['dk_date'] = $notice_date;
                        $time_data['memo'] = $noticeTextData->notice_text; // 最終申請テキストデータ
                        $time_data['in_time'] = NULL;
                        $time_data['out_time'] = NULL;
                        $time_data['in_work_time'] = NULL;
                        $time_data['out_work_time'] = NULL;
                        $time_data['fact_hour'] = 0;
                        $time_data['fact_work_hour'] = 0;
                        $time_data['rest'] = 0;
                        $time_data['over_hour'] = 0;
                        $time_data['night_hour'] = 0;
                        $time_data['left_hour'] = 0;
                        $time_data['late_hour'] = 0;
                        $time_data['in_flag'] = 0;
                        $time_data['out_flag'] = 0;
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        $time_data['id'] = $now_time_data->id;
                        $this->model_time->update_data($time_data); // アップデート
                    break;

                    case 3: // 遅刻申請　承認時
                        $time_data['notice_memo'] = $noticeTextData->notice_text;
                        $time_data['revision_user'] = $to_user_name;
                        $time_data['revision_datetime'] = $notice_text_data['notice_datetime'];
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        if (!$now_time_data) {
                            $this->model_time->insert_data($time_data); // 新規登録
                        } else {
                            $time_data['id'] = $now_time_data->id;
                            $time_data['memo'] = $now_time_data->memo;
                            if ($time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['memo']."\n".$time_data['notice_memo'];
                            }
                            if (!$time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['notice_memo'];
                            }
                            $this->model_time->update_notice_data($time_data); // アップデート
                        }
                    break;

                    case 4: // 早退申請　承認時
                        $time_data['notice_memo'] = $noticeTextData->notice_text;
                        $time_data['revision_user'] = $to_user_name;
                        $time_data['revision_datetime'] = $notice_text_data['notice_datetime'];
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        if (!$now_time_data) {
                            $this->model_time->insert_data($time_data); // 新規登録
                        } else {
                            $time_data['id'] = $now_time_data->id;
                            $time_data['memo'] = $now_time_data->memo;
                            if ($time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['memo']."\n".$time_data['notice_memo'];
                            }
                            if (!$time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['notice_memo'];
                            }
                            $this->model_time->update_notice_data($time_data); // アップデート
                        }
                    break;

                    case 5: // 残業申請　承認時
                        $time_data['notice_memo'] = $noticeTextData->notice_text;
                        $time_data['revision_user'] = $to_user_name;
                        $time_data['revision_datetime'] = $notice_text_data['notice_datetime'];
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        if (!$now_time_data) {
                            $this->model_time->insert_data($time_data); // 新規登録
                        } else {
                            $time_data['id'] = $now_time_data->id;
                            $time_data['memo'] = $now_time_data->memo;
                            if ($time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['memo']."\n".$time_data['notice_memo'];
                            }
                            if (!$time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['notice_memo'];
                            }
                            $this->model_time->update_notice_data($time_data); // アップデート
                        }
                    break;

                    case 6: // 有給申請（全日） 承認時
                        if ($notice_end_date) {
                            $start = new DateTime($notice_date);
                            $end = new DateTime($notice_end_date.' 00:00:01');
                            $interval = new DateInterval('P1D');
                            $period = new DatePeriod($start, $interval, $end);

                            foreach ($period as $datetime) {
                                $to_date = $datetime->format('Y-m-d');
                                $shift_data['status'] = 2;
                                $shift_data['in_time'] = null;
                                $shift_data['out_time'] = null;
                                $shift_data['user_id'] = $from_userId;
                                $shift_data['dk_date'] = $to_date;
                                $shift_data['rest'] = 0;
                                $shift_data['hour'] = 0;
                                $shift_data['paid_hour'] = 1;
                                $this->load->model('model_shift');
                                $get_shift_data = $this->model_shift->check_day_userid($to_date, $from_userId);
                                if ($get_shift_data) { //DB登録
                                    if ($this->model_shift->update_shift($shift_data)) {
                                        $data = 'ok';
                                    } else {
                                        $data = 'err_update';
                                    }
                                } else {
                                    if ($this->model_shift->insert_shift($shift_data)) {
                                        $data = 'ok';
                                    } else {
                                        $data = 'err';
                                    }
                                }
                            }
                        } else {
                            $shift_data['status'] = 2;
                            $shift_data['in_time'] = null;
                            $shift_data['out_time'] = null;
                            $shift_data['user_id'] = $from_userId;
                            $shift_data['dk_date'] = $notice_date;
                            $shift_data['rest'] = 0;
                            $shift_data['hour'] = 0;
                            $shift_data['paid_hour'] = 1;
                            $this->load->model('model_shift');
                            $get_shift_data = $this->model_shift->check_day_userid($notice_date, $from_userId);
                            if ($get_shift_data) { //DB登録
                                if ($this->model_shift->update_shift($shift_data)) {
                                    $data = 'ok';
                                } else {
                                    $data = 'err_update';
                                }
                            } else {
                                if ($this->model_shift->insert_shift($shift_data)) {
                                    $data = 'ok';
                                } else {
                                    $data = 'err';
                                }
                            }
                        }
                        $time_data['flag'] = 'notice';
                        $time_data['user_id'] = $from_userId;
                        $time_data['dk_date'] = $notice_date;
                        $time_data['memo'] = $noticeTextData->notice_text; // 最終申請テキストデータ
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        if (!$now_time_data) {
                            $this->model_time->insert_data($time_data); // 新規登録
                        } else {
                            $time_data['id'] = $now_time_data->id;
                            $this->model_time->update_data($time_data); // アップデート
                        }
                    break;

                    case 9: // 有給申請（半日） 承認時
                        $shift_data['status'] = 2;
                        $shift_data['in_time'] = null;
                        $shift_data['out_time'] = null;
                        $shift_data['user_id'] = $from_userId;
                        $shift_data['dk_date'] = $notice_date;
                        $shift_data['rest'] = 0;
                        $shift_data['hour'] = 0;
                        $shift_data['paid_hour'] = 0.5;
                        $this->load->model('model_shift');
                        $get_shift_data = $this->model_shift->check_day_userid($notice_date, $from_userId);
                        if ($get_shift_data) { //DB登録
                            if ($this->model_shift->update_shift($shift_data)) {
                                $data = 'ok';
                            } else {
                                $data = 'err_update';
                            }
                        } else {
                            if ($this->model_shift->insert_shift($shift_data)) {
                                $data = 'ok';
                            } else {
                                $data = 'err';
                            }
                        }
                        $time_data['flag'] = 'notice';
                        $time_data['user_id'] = $from_userId;
                        $time_data['dk_date'] = $notice_date;
                        $time_data['memo'] = $noticeTextData->notice_text; // 最終申請テキストデータ
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        if (!$now_time_data) {
                            $this->model_time->insert_data($time_data); // 新規登録
                        } else {
                            $time_data['id'] = $now_time_data->id;
                            $time_data['memo'] = $now_time_data->memo;
                            if ($time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['memo']."\n".$time_data['notice_memo'];
                            }
                            if (!$time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['notice_memo'];
                            }
                            $this->model_time->update_data($time_data); // アップデート
                        }
                    break;

                    case 7: // 欠勤申請　承認時
                        $time_data['notice_memo'] = $noticeTextData->notice_text;
                        $time_data['revision_user'] = $to_user_name;
                        $time_data['revision_datetime'] = $notice_text_data['notice_datetime'];
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        if (!$now_time_data) {
                            $this->model_time->insert_data($time_data); // 新規登録
                        } else {
                            $time_data['id'] = $now_time_data->id;
                            $time_data['memo'] = $now_time_data->memo;
                            if ($time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['memo']."\n".$time_data['notice_memo'];
                            }
                            if (!$time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['notice_memo'];
                            }
                            $this->model_time->update_notice_data($time_data); // アップデート
                        }
                    break;

                    case 8: // その他申請　承認時
                        $time_data['notice_memo'] = $noticeTextData->notice_text;
                        $time_data['revision_user'] = $to_user_name;
                        $time_data['revision_datetime'] = $notice_text_data['notice_datetime'];
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        if (!$now_time_data) {
                            $this->model_time->insert_data($time_data); // 新規登録
                        } else {
                            $time_data['id'] = $now_time_data->id;
                            $time_data['memo'] = $now_time_data->memo;
                            if ($time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['memo']."\n".$time_data['notice_memo'];
                            }
                            if (!$time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['notice_memo'];
                            }
                            $this->model_time->update_notice_data($time_data); // アップデート
                        }
                    break;

                    case 11: // 休暇申請　承認時
                        $time_data['notice_memo'] = $noticeTextData->notice_text;
                        $time_data['revision_user'] = $to_user_name;
                        $time_data['revision_datetime'] = $notice_text_data['notice_datetime'];
                        $this->load->model('model_time'); // model time 読込
                        $now_time_data = $this->model_time->check_day_userid($notice_date, $from_userId);
                        if (!$now_time_data) {
                            $this->model_time->insert_data($time_data); // 新規登録
                        } else {
                            $time_data['id'] = $now_time_data->id;
                            $time_data['memo'] = $now_time_data->memo;
                            if ($time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['memo']."\n".$time_data['notice_memo'];
                            }
                            if (!$time_data['memo'] && $time_data['notice_memo'] !== NULL) {
                                $time_data['memo'] = $time_data['notice_memo'];
                            }
                            $this->model_time->update_notice_data($time_data); // アップデート
                        }
                    break;

                    default:
                    break;
                }
            }

            // NG時
            if ($notice_text_data['notice_status'] == 2) {
                $notice_text_data['notice_text'] = 'NG';
            }
        }

        // メッセージの作成
        if ($to_user_name == $from_user_name) {
            $from_user_name = '';
        } else {
            $from_user_name = $from_user_name.'に';
        }
        if ($notice_end_date) {
            $notice_end_date = ' 〜 '.$notice_end_date;
        }

        // 通知送信用メッセージ
        $message =
        $now_datetime."\n".
        $to_user_name.'が'.$from_user_name."メッセージを送信しました。\n\n".
        $notice_title.' >> '.$notice_date.$notice_end_date."\n\n".
        $notice_text_data['notice_text'];

        // notice_text追加登録
        if ($this->model_notice_data_bk->insert_text($notice_text_data)) {
            $data = 'ok';
        } else {
            $data = 'err_notice_text';
        }

        // LINE通知
        // config data取得
        $this->load->model('model_config_values');
        $where = [];
        $result = $this->model_config_values->find('id, config_name, value', $where, '');
        $config_data = array_column($result, 'value', 'config_name');

        if ((int)$config_data['line_flag'] === 1) { // line_flag = 1 -> LINE通知
            $line_message = [
                'type' => 'text',
                'text' => $message
            ];
            $this->load->library('process_line_lib'); // LINE通知用 lib 読込
            $this->process_line_lib->line_message($line_message, $notice_text_data['to_user_id']);
        }

        // mail通知
        // notice_mail_flag = 2 申請のみ　notice_mail_flag = 9 すべて
        if ((int)$config_data['notice_mail_flag'] === 2 || (int)$config_data['notice_mail_flag'] === 9) {
            $mail_subject = '申請通知';
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

        $this->output // 出力 json
        ->set_content_type('application/text')
        ->set_output(json_encode($data));
    }

    // 既読用データ　保存
    public function push_user()
    {
        $user_id = $this->input->post('user_id'); // user id
        $notice_id = $this->input->post('notice_id'); // notice_id

        $this->load->model('model_notice_data_bk');
        $notice_text_data = $this->model_notice_data_bk->get_text_notice_id_id($notice_id);
        foreach ($notice_text_data as $value) {
            $notice_text_id = $value->id;
            if (!$this->model_notice_data_bk->get_text_users_notice_id_check($notice_text_id, $user_id)) {
                $this->model_notice_data_bk->insert_notice_text_user($user_id, $notice_text_id);
            }
        }
        $this->output // 出力 json
        ->set_content_type('application/text')
        ->set_output('db');
    }
}
