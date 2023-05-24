<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Config_values extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'config_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'work' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'memo' => [
                'type' => 'TEXT',
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('config_values');

        $data = [
            [
                'config_name'=> 'id_size',
                'type'=> 'INT',
                'value'=> '5',
                'work'=> 'front',
                'memo'=> '従業員IDの桁数'
            ],
            [
                'config_name'=> 'company_name',
                'type'=> 'STR',
                'value'=> 'demo local',
                'work'=> 'front',
                'memo'=> '会社名　表示用'
            ],
            [
                'config_name'=> 'gateway_mail_flag',
                'type'=> 'INT',
                'value'=> '1',
                'work'=> 'front',
                'memo'=> '共通出退勤画面　依頼フラグ　0=使用しない　1=使用する'
            ],
            [
                'config_name'=> 'over_time_view_flag',
                'type'=> 'INT',
                'value'=> '1',
                'work'=> 'front',
                'memo'=> '集計　残業表示フラグ　0=表示しない　1=表示する'
            ],
            [
                'config_name'=> 'night_time_view_flag',
                'type'=> 'INT',
                'value'=> '1',
                'work'=> 'front',
                'memo'=> '集計　深夜表示フラグ　0=表示しない　1=表示する'
            ],
            [
                'config_name'=> 'aporan_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'アポラン連携機能フラグ　0=連携しない　1=連携する'
            ],
            [
                'config_name'=> 'download_filetype',
                'type'=> 'INT',
                'value'=> '1',
                'work'=> 'front',
                'memo'=> '入出力ファイルタイプ　1=excel2017、2=excel2003、3=csv'
            ],
            [
                'config_name'=> 'revision_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> ''
            ],
            [
                'config_name'=> 'system_id',
                'type'=> 'STR',
                'value'=> 'demo',
                'work'=> 'front',
                'memo'=> 'DB連携　システムID'
            ],
            [
                'config_name'=> 'line_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'LINE連携フラグ　0=連携しない　1=連携する'
            ],
            [
                'config_name'=> 'line_token',
                'type'=> 'STR',
                'value'=> '',
                'work'=> 'back',
                'memo'=> 'LINE連携用トークン'
            ],
            [
                'config_name'=> 'advance_pay_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> ''
            ],
            [
                'config_name'=> 'notice_mail_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'メール通知用フラグ　0=通知しない　1=出退勤のみ　2=申請のみ　3=すべて'
            ],
            [
                'config_name'=> 'notice_mailaddress1',
                'type'=> 'STR',
                'value'=> '',
                'work'=> 'back',
                'memo'=> 'メール通知用アドレス'
            ],
            [
                'config_name'=> 'notice_mailaddress2',
                'type'=> 'STR',
                'value'=> '',
                'work'=> 'back',
                'memo'=> 'メール通知用アドレス'
            ],
            [
                'config_name'=> 'notice_mailaddress3',
                'type'=> 'STR',
                'value'=> '',
                'work'=> 'back',
                'memo'=> 'メール通知用アドレス'
            ],
            [
                'config_name'=> 'notice_mailaddress4',
                'type'=> 'STR',
                'value'=> '',
                'work'=> 'back',
                'memo'=> 'メール通知用アドレス'
            ],
            [
                'config_name'=> 'notice_mailaddress5',
                'type'=> 'STR',
                'value'=> '',
                'work'=> 'back',
                'memo'=> 'メール通知用アドレス'
            ],
            [
                'config_name'=> 'over_day',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '日付またぎ終了時刻　0=24時まで'
            ],
            [
                'config_name'=> 'rest_input_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '休憩入力用フラグ　0=使用しない　1=使用する'
            ],
            [
                'config_name'=> 'goaway_input_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> ''
            ],
            [
                'config_name'=> 'gps_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '位置情報取得フラグ　0=取得しない　1=すべて取得する　2=モバイルのみ取得'
            ],
            [
                'config_name'=> 'qrcode_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'QRコード利用フラグ　0=利用しない　1=利用する'
            ],
            [
                'config_name'=> 'area_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'エリア管理フラグ　0=しない　1=する'
            ],
            [
                'config_name'=> 'end_day',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '締め日　0=月末　1-27=締め日'
            ],
            [
                'config_name'=> 'resq_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'レスQ料連携フラグ　0=連携しない　1=連携する'
            ],
            [
                'config_name'=> 'resq_company_code',
                'type'=> 'STR',
                'value'=> '',
                'work'=> 'back',
                'memo'=> 'レスQ料用'
            ],
            [
                'config_name'=> 'edit_min',
                'type'=> 'INT',
                'value'=> '1',
                'work'=> 'front',
                'memo'=> '時刻修正入力　分単位'
            ],
            [
                'config_name'=> 'edit_in_time',
                'type'=> 'STR',
                'value'=> '09:00',
                'work'=> 'front',
                'memo'=> '時刻修正入力　出勤時刻デフォルト'
            ],
            [
                'config_name'=> 'edit_out_time',
                'type'=> 'STR',
                'value'=> '18:00',
                'work'=> 'front',
                'memo'=> '時刻修正入力　退勤時刻デフォルト'
            ],
            [
                'config_name'=> 'nonstop_input_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '直行・直帰フラグ　0=使用しない　1=使用する'
            ],
            [
                'config_name'=> 'mypage_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage フラグ　0=使用しない　1=使用する'
            ],
            [
                'config_name'=> 'mypage_input_flag',
                'type'=> 'INT',
                'value'=> '1',
                'work'=> 'front',
                'memo'=> 'MyPage 出退勤フラグ　0=利用不可　1=利用可'
            ],
            [
                'config_name'=> 'mypage_profile_edit_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage プロフィール編集フラグ　0=編集不可　1=編集可'
            ],
            [
                'config_name'=> 'mypage_password_edit_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage 自身でのパスワード変更フラグ　0=変更不可　1=変更可'
            ],
            [
                'config_name'=> 'mypage_end_day',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage 締め日　0=月末'
            ],
            [
                'config_name'=> 'mypage_user_edit_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage 上司による勤怠編集フラグ　0=編集不可　1=編集可'
            ],
            [
                'config_name'=> 'auto_shift_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '自動シフトフラグ　0=しない　1=する'
            ],
            [
                'config_name'=> 'shift_view_flag',
                'type'=> 'INT',
                'value'=> '1',
                'work'=> 'front',
                'memo'=> 'デフォルトシフト表示　0=リスト表示　1=カレンダー表示'
            ],
            [
                'config_name'=> 'shift_first_view_hour',
                'type'=> 'INT',
                'value'=> '6',
                'work'=> 'front',
                'memo'=> 'シフト　リスト表示　始まりの時刻'
            ],
            [
                'config_name'=> 'shift_end_view_hour',
                'type'=> 'INT',
                'value'=> '24',
                'work'=> 'front',
                'memo'=> 'シフト　リスト表示　終了の時刻'
            ],
            [
                'config_name'=> 'shift_cal_first_day',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'シフトカレンダー最初の曜日　0-6'
            ],
            [
                'config_name'=> 'minute_time_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '集計　分表示フラグ　0=表示しない　1=表示する　2=分のみ表示する'
            ],
            [
                'config_name'=> 'normal_time_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '集計　通常時間表示フラグ　0=表示する　1=表示しない'
            ],
            [
                'config_name'=> 'mypage_my_inout_view_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage マイ勤務状況　0=実出退勤表示　1=出退勤表示　2=表示しない'
            ],
            [
                'config_name'=> 'mypage_status_inout_view_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage 従業員勤務状況　0=実出退勤表示　1=出退勤表示　2=表示しない'
            ],
            [
                'config_name'=> 'gateway_status_view_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '共通出退勤画面　出勤状況　0=実出退勤表示　1=出退勤表示　2=表示しない'
            ],
            [
                'config_name'=> 'mypage_status_view_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage ダッシュボード　出勤状況　0=実出退勤表示　1=出退勤表示　2=表示しない'
            ],
            [
                'config_name'=> 'mail_title_notice',
                'type'=> 'STR',
                'value'=> '【新規申請通知】',
                'work'=> 'back',
                'memo'=> 'メールタイトル　申請時'
            ],
            [
                'config_name'=> 'mail_title_gateway',
                'type'=> 'STR',
                'value'=> '【出退勤通知】',
                'work'=> 'back',
                'memo'=> 'メールタイトル　出勤時'
            ],
            [
                'config_name'=> 'mypage_shift_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage シフト管理フラグ　0=使用しない　1=使用する'
            ],
            [
                'config_name'=> 'shift_first_hour',
                'type'=> 'INT',
                'value'=> '9',
                'work'=> 'front',
                'memo'=> 'デフォルト　シフト入力出勤時刻'
            ],
            [
                'config_name'=> 'shift_end_hour',
                'type'=> 'INT',
                'value'=> '18',
                'work'=> 'front',
                'memo'=> 'デフォルト　シフト入力退勤時刻'
            ],
            [
                'config_name'=> 'shift_input_hour',
                'type'=> 'INT',
                'value'=> '15',
                'work'=> 'front',
                'memo'=> 'シフト入力　分単位'
            ],
            [
                'config_name'=> 'slack_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'Slack連携フラグ　0=連携しない　1=連携する'
            ],
            [
                'config_name'=> 'slack_webhook_url',
                'type'=> 'STR',
                'value'=> '',
                'work'=> 'front',
                'memo'=> 'Slack Webhook URL'
            ],
            [
                'config_name'=> 'mypage_shift_alert',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage シフト未提出時警告　0=しない　1=する'
            ],
            [
                'config_name'=> 'shift_closing_day',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'シフト提出締め切り日　0=月末'
            ],
            [
                'config_name'=> 'esna_pay_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'ESNA時給管理システム連携フラグ　0=しない　1=する'
            ],
            [
                'config_name'=> 'user_api_output_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '従業員データapi連携フラグ'
            ],
            [
                'config_name'=> 'notice_comment_require',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '申請時コメント　0=しない　99=必須'
            ],
            [
                'config_name'=> 'gateway_comment_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '出退勤時のコメント機能　0=使用しない　1=使用'
            ],
            [
                'config_name'=> 'mypage_self_edit_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> 'MyPage 自身の打刻修正が可能　0=しない　1=する'
            ],
            [
                'config_name'=> 'pay_flag',
                'type'=> 'INT',
                'value'=> '0',
                'work'=> 'front',
                'memo'=> '給与管理フラグ　0=使用しない　1=使用する'
            ],
        ];
        $this->db->insert_batch('config_values', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('config_values');
    }
}