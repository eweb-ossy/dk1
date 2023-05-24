# ************************************************************
# Sequel Ace SQL dump
# バージョン 20046
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# ホスト: mysql101.xbiz.ne.jp (MySQL 5.7.37)
# データベース: eweb_dk005
# 生成時間: 2023-01-25 07:04:12 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# テーブルのダンプ area_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `area_data`;

CREATE TABLE `area_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `area_name` varchar(255) DEFAULT NULL,
  `host_ip` varchar(255) DEFAULT NULL,
  `memo` text,
  `mode_input` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ authority_area_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `authority_area_data`;

CREATE TABLE `authority_area_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `area_name` varchar(255) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ authority_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `authority_data`;

CREATE TABLE `authority_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) DEFAULT NULL,
  `all_authority_id` tinyint(1) DEFAULT '0',
  `area_id` int(3) DEFAULT NULL,
  `area_authority_id` tinyint(1) DEFAULT '0',
  `group_id` int(3) DEFAULT NULL,
  `group_authority_id` tinyint(1) DEFAULT '0',
  `authority_type` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ authority_group_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `authority_group_data`;

CREATE TABLE `authority_group_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) DEFAULT NULL,
  `area_id` int(3) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ company_rules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company_rules`;

CREATE TABLE `company_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rule` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `memo` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `company_rules` WRITE;
/*!40000 ALTER TABLE `company_rules` DISABLE KEYS */;

INSERT INTO `company_rules` (`id`, `rule`, `type`, `value`, `memo`, `created_at`, `updated_at`)
VALUES
	(1,'company_week_start','INT','0','１週間の定義　開始曜日',NULL,NULL),
	(2,'company_end_day','INT','0','月の締め日　0=月末',NULL,NULL),
	(3,'company_over_day','INT','0','１日の終了時刻　0=24時',NULL,NULL);

/*!40000 ALTER TABLE `company_rules` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sound_flag` int(1) NOT NULL DEFAULT '1',
  `id_size` int(2) NOT NULL DEFAULT '6',
  `company_name` text,
  `system_mail1` varchar(255) DEFAULT NULL,
  `system_mail2` varchar(255) DEFAULT NULL,
  `memo_open_flag` int(1) NOT NULL DEFAULT '0',
  `gateway_mail_flag` int(1) NOT NULL DEFAULT '0',
  `pay_cut_off_date` int(2) DEFAULT '0',
  `over_time_flag` tinyint(1) DEFAULT '1',
  `night_time_flag` tinyint(1) DEFAULT '1',
  `aporan_flag` tinyint(1) DEFAULT '0',
  `download_filetype` tinyint(1) DEFAULT '1',
  `revision_flag` tinyint(1) DEFAULT '0',
  `system_id` varchar(255) DEFAULT NULL,
  `line_flag` tinyint(1) DEFAULT '0',
  `line_token` varchar(255) DEFAULT NULL,
  `advance_pay_flag` tinyint(1) DEFAULT '0',
  `notice_mail_flag` tinyint(1) DEFAULT '0',
  `notice_mailaddress1` varchar(255) DEFAULT NULL,
  `notice_mailaddress2` varchar(255) DEFAULT NULL,
  `notice_mailaddress3` varchar(255) DEFAULT NULL,
  `notice_mailaddress4` varchar(255) DEFAULT NULL,
  `notice_mailaddress5` varchar(255) DEFAULT NULL,
  `over_day` tinyint(1) DEFAULT '0',
  `rest_input_flag` tinyint(1) DEFAULT '0',
  `goaway_input_flag` tinyint(1) DEFAULT '0',
  `gps_flag` tinyint(1) DEFAULT '0',
  `qrcode_flag` tinyint(1) DEFAULT '0',
  `area_flag` tinyint(4) DEFAULT '0',
  `end_day` tinyint(2) DEFAULT '0',
  `resq_flag` tinyint(1) DEFAULT '0',
  `resq_company_code` varchar(255) DEFAULT NULL,
  `edit_min` tinyint(2) DEFAULT '1',
  `edit_in_time` varchar(255) DEFAULT NULL,
  `edit_out_time` varchar(255) DEFAULT NULL,
  `nonstop_input_flag` tinyint(1) DEFAULT '0',
  `mypage_flag` tinyint(1) DEFAULT '0',
  `mypage_input_flag` tinyint(1) DEFAULT '0',
  `mypage_profile_edit_flag` tinyint(1) DEFAULT '0',
  `mypage_password_edit_flag` tinyint(1) DEFAULT '0',
  `mypage_end_day` tinyint(2) DEFAULT '0',
  `mypage_user_edit_flag` tinyint(1) DEFAULT '0',
  `auto_shift_flag` tinyint(1) DEFAULT '0',
  `shift_view_flag` tinyint(1) DEFAULT '0',
  `shift_first_view_hour` tinyint(2) DEFAULT '6',
  `shift_end_view_hour` tinyint(2) DEFAULT '24',
  `shift_cal_first_day` tinyint(1) DEFAULT '0',
  `minute_time_flag` tinyint(1) DEFAULT '0',
  `normal_time_flag` tinyint(1) DEFAULT '0',
  `mypage_my_inout_view_flag` tinyint(1) DEFAULT '0',
  `mypage_status_inout_view_flag` tinyint(1) DEFAULT '0',
  `mypage_status_view_flag` tinyint(1) DEFAULT '0',
  `gateway_status_view_flag` tinyint(1) DEFAULT '0',
  `mail_title_notice` varchar(255) DEFAULT NULL,
  `mail_title_gateway` varchar(255) DEFAULT NULL,
  `mypage_shift_flag` tinyint(1) DEFAULT '0',
  `shift_first_hour` tinyint(2) DEFAULT '9',
  `shift_end_hour` tinyint(2) DEFAULT '18',
  `shift_input_hour` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ config_message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_message`;

CREATE TABLE `config_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `public_message1_flag` int(1) DEFAULT '0',
  `public_message1_title` varchar(255) DEFAULT NULL,
  `public_message1` text,
  `in_message1_text` varchar(255) DEFAULT NULL,
  `in_message2_flag` int(1) DEFAULT '0',
  `in_message2_diff` int(3) DEFAULT '0',
  `in_message2_text` varchar(255) DEFAULT NULL,
  `in_message3_flag` int(1) DEFAULT '0',
  `in_message3_diff` int(3) DEFAULT '0',
  `in_message3_text` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `config_message` WRITE;
/*!40000 ALTER TABLE `config_message` DISABLE KEYS */;

INSERT INTO `config_message` (`id`, `public_message1_flag`, `public_message1_title`, `public_message1`, `in_message1_text`, `in_message2_flag`, `in_message2_diff`, `in_message2_text`, `in_message3_flag`, `in_message3_diff`, `in_message3_text`, `created_at`, `updated_at`)
VALUES
	(1,0,'お知らせ','出退勤画面メッセージ内容',NULL,0,0,NULL,0,0,NULL,NULL,NULL);

/*!40000 ALTER TABLE `config_message` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ config_rules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_rules`;

CREATE TABLE `config_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `group_id` tinyint(1) DEFAULT NULL,
  `group_no` tinyint(3) DEFAULT NULL,
  `all_flag` tinyint(1) DEFAULT '0',
  `in_marume_flag` tinyint(1) DEFAULT '0',
  `in_marume_hour` tinyint(2) DEFAULT NULL,
  `in_marume_time` time DEFAULT NULL,
  `out_marume_flag` tinyint(1) DEFAULT '0',
  `out_marume_hour` tinyint(2) DEFAULT NULL,
  `out_marume_time` time DEFAULT NULL,
  `basic_in_time` time DEFAULT NULL,
  `basic_out_time` time DEFAULT NULL,
  `basic_rest_weekday` varchar(8) DEFAULT NULL,
  `rest_rule_flag` tinyint(1) DEFAULT NULL,
  `over_limit_hour` int(4) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `order` int(3) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ config_values
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config_values`;

CREATE TABLE `config_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `work` varchar(255) DEFAULT NULL,
  `memo` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `config_values` WRITE;
/*!40000 ALTER TABLE `config_values` DISABLE KEYS */;

INSERT INTO `config_values` (`id`, `config_name`, `type`, `value`, `work`, `memo`, `created_at`, `updated_at`)
VALUES
	(1,'id_size','INT','5','front','従業員IDの桁数',NULL,NULL),
	(2,'company_name','STR','会社名','front','会社名　表示用',NULL,NULL),
	(3,'memo_open_flag','INT','1','front',NULL,NULL,NULL),
	(4,'gateway_mail_flag','INT','0','front','共通出退勤画面　依頼フラグ　0=使用しない　1=使用する',NULL,NULL),
	(5,'over_time_view_flag','INT','1',NULL,'集計　残業表示フラグ　0=表示しない　1=表示する',NULL,NULL),
	(6,'night_time_view_flag','INT','1',NULL,'集計　深夜表示フラグ　0=表示しない　1=表示する',NULL,NULL),
	(7,'aporan_flag','INT','0',NULL,'アポラン連携機能フラグ　0=連携しない　1=連携する',NULL,NULL),
	(8,'download_filetype','INT','1',NULL,'入出力ファイルタイプ　1=excel2017、2=excel2003、3=csv',NULL,NULL),
	(9,'revision_flag','INT','0',NULL,NULL,NULL,NULL),
	(10,'system_id','STR','id','front','DB連携　システムID',NULL,NULL),
	(11,'line_flag','INT','0',NULL,'LINE連携フラグ　0=連携しない　1=連携する',NULL,NULL),
	(12,'line_token','STR',NULL,NULL,'LINE連携用トークン',NULL,NULL),
	(13,'advance_pay_flag','INT',NULL,NULL,NULL,NULL,NULL),
	(14,'notice_mail_flag','INT','0',NULL,'メール通知用フラグ　0=通知しない　1=出退勤のみ　2=申請のみ　3=すべて',NULL,NULL),
	(15,'notice_mailaddress1','STR','',NULL,'メール通知用アドレス',NULL,NULL),
	(16,'notice_mailaddress2','STR','',NULL,'メール通知用アドレス',NULL,NULL),
	(17,'notice_mailaddress3','STR','',NULL,'メール通知用アドレス',NULL,NULL),
	(18,'notice_mailaddress4','STR','',NULL,'メール通知用アドレス',NULL,NULL),
	(19,'notice_mailaddress5','STR','',NULL,'メール通知用アドレス',NULL,NULL),
	(20,'over_day','INT','0',NULL,'日付またぎ終了時刻　0=24時まで',NULL,NULL),
	(21,'rest_input_flag','INT','0','front','休憩入力用フラグ　0=使用しない　1=使用する',NULL,NULL),
	(22,'goaway_input_flag','INT','0','front',NULL,NULL,NULL),
	(23,'gps_flag','INT','0','front','位置情報取得フラグ　0=取得しない　1=すべて取得する　2=モバイルのみ取得',NULL,NULL),
	(24,'qrcode_flag','INT','0',NULL,NULL,NULL,NULL),
	(25,'area_flag','INT','0',NULL,'エリア管理フラグ　0=しない　1=する',NULL,NULL),
	(26,'end_day','INT','0',NULL,'締め日　0=月末',NULL,NULL),
	(27,'resq_flag','INT','0',NULL,'レスQ料連携フラグ　0=連携しない　1=連携する',NULL,NULL),
	(28,'resq_company_code','STR','',NULL,'レスQ料用',NULL,NULL),
	(29,'edit_min','INT','1',NULL,'時刻修正入力　分単位',NULL,NULL),
	(30,'edit_in_time','STR',NULL,NULL,NULL,NULL,NULL),
	(31,'edit_out_time','STR',NULL,NULL,NULL,NULL,NULL),
	(32,'nonstop_input_flag','INT','0',NULL,'直行・直帰フラグ　0=使用しない　1=使用する',NULL,NULL),
	(33,'mypage_flag','INT','1',NULL,'MyPage フラグ　0=使用しない　1=使用する',NULL,NULL),
	(34,'mypage_input_flag','INT','1',NULL,'MyPage 出退勤フラグ　0=利用不可　1=利用可',NULL,NULL),
	(35,'mypage_profile_edit_flag','INT','0',NULL,'MyPage プロフィール編集フラグ　0=編集不可　1=編集可',NULL,NULL),
	(36,'mypage_password_edit_flag','INT','0',NULL,'MyPage 自身でのパスワード変更フラグ　0=変更不可　1=変更可',NULL,NULL),
	(37,'mypage_end_day','INT','0',NULL,'MyPage 締め日　0=月末',NULL,NULL),
	(38,'mypage_user_edit_flag','INT','0',NULL,'MyPage 上司による勤怠編集フラグ　0=編集不可　1=編集可',NULL,NULL),
	(39,'auto_shift_flag','INT','0',NULL,'自動シフトフラグ　0=しない　1=する',NULL,NULL),
	(40,'shift_view_flag','INT','1',NULL,'デフォルトシフト表示　0=リスト表示　1=カレンダー表示',NULL,NULL),
	(41,'shift_first_view_hour','INT','6',NULL,'シフト　リスト表示　始まりの時刻',NULL,NULL),
	(42,'shift_end_view_hour','INT','24',NULL,'シフト　リスト表示　終了の時刻',NULL,NULL),
	(43,'shift_cal_first_day','INT','0',NULL,'シフトカレンダー最初の曜日　0-6',NULL,NULL),
	(44,'minute_time_flag','INT','0',NULL,'集計　分表示フラグ　0=表示しない　1=表示する　2=分のみ表示する',NULL,NULL),
	(45,'normal_time_flag','INT','0',NULL,'集計　通常時間表示フラグ　0=表示する　1=表示しない',NULL,NULL),
	(46,'mypage_my_inout_view_flag','INT','0',NULL,'MyPage マイ勤務状況　0=実出退勤表示　1=出退勤表示　2=表示しない',NULL,NULL),
	(47,'mypage_status_inout_view_flag','INT','0',NULL,'MyPage 従業員勤務状況　0=実出退勤表示　1=出退勤表示　2=表示しない',NULL,NULL),
	(48,'gateway_status_view_flag','INT','0','front','共通出退勤画面　出勤状況　0=実出退勤表示　1=出退勤表示　2=表示しない',NULL,NULL),
	(49,'mypage_status_view_flag','INT','0',NULL,'MyPage ダッシュボード　出勤状況　0=実出退勤表示　1=出退勤表示　2=表示しない',NULL,NULL),
	(50,'mail_title_notice','STR','【新規申請通知】',NULL,'メールタイトル　申請時',NULL,NULL),
	(51,'mail_title_gateway','STR','出退勤通知',NULL,'メールタイトル　出勤時',NULL,NULL),
	(52,'mypage_shift_flag','INT','0',NULL,'MyPage シフト管理フラグ　0=使用しない　1=使用する',NULL,NULL),
	(53,'shift_first_hour','INT','9',NULL,'デフォルト　シフト入力出勤時刻',NULL,NULL),
	(54,'shift_end_hour','INT','18',NULL,'デフォルト　シフト入力退勤時刻',NULL,NULL),
	(55,'shift_input_hour','INT','15',NULL,'シフト入力　分単位',NULL,NULL),
	(56,'slack_flag','INT','0',NULL,'Slack連携フラグ　0=連携しない　1=連携する',NULL,NULL),
	(57,'slack_webhook_url','STR',NULL,NULL,'Slack Webhook URL',NULL,NULL),
	(58,'mypage_shift_alert','INT','0',NULL,'MyPage シフト未提出時警告　0=しない　1=する',NULL,NULL),
	(59,'shift_closing_day','INT',NULL,NULL,'シフト提出締め切り日　0=月末',NULL,NULL),
	(60,'esna_pay_flag','INT','0',NULL,'ESNA時給管理システム連携フラグ　0=しない　1=する',NULL,NULL),
	(61,'user_api_output_flag','INT','0',NULL,'従業員データapi連携フラグ',NULL,NULL),
	(62,'notice_comment_require','INT','99',NULL,'申請時コメント　0=しない　99=必須',NULL,NULL),
	(63,'gateway_comment_flag','INT','0',NULL,'出退勤時のコメント機能　0=使用しない　1=使用',NULL,NULL),
	(64,'mypage_self_edit_flag','INT','0',NULL,'MyPage 自身の打刻修正が可能　0=しない　1=する',NULL,NULL),
	(65,'pay_flag','INT','0',NULL,'給与管理フラグ　0=使用しない　1=使用する',NULL,NULL),
	(66,'pay_password_flag','INT','0',NULL,'MyPage給与明細閲覧時のパスワード有無　0=しない　1=する',NULL,NULL),
	(67,'user_id_define','INT','0',NULL,'従業員IDの定義　0=任意設定　1=自動連番',NULL,NULL),
	(68,'actual_view_flag','INT','0',NULL,'実労働時間の表示　0=しない　1=する',NULL,NULL),
	(69,'list_month_view_flag','INT','1',NULL,'月別集計表示　1=0.00h 2=00:00-00:00',NULL,NULL);

/*!40000 ALTER TABLE `config_values` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ goaway_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `goaway_data`;

CREATE TABLE `goaway_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(15) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT '0',
  `goaway_date` date DEFAULT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `goaway_hour` int(5) DEFAULT NULL,
  `time_data_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ gps_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gps_data`;

CREATE TABLE `gps_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gps_date` date DEFAULT NULL,
  `flag` smallint(1) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ group_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_history`;

CREATE TABLE `group_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(15) NOT NULL,
  `group1_id` int(4) DEFAULT NULL,
  `group2_id` int(4) DEFAULT NULL,
  `group3_id` int(4) DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ group_title
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_title`;

CREATE TABLE `group_title` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` int(1) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `group_title` WRITE;
/*!40000 ALTER TABLE `group_title` DISABLE KEYS */;

INSERT INTO `group_title` (`id`, `group_id`, `title`)
VALUES
	(1,1,'雇用形態'),
	(2,2,'職種'),
	(3,3,'部署');

/*!40000 ALTER TABLE `group_title` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ layout
# ------------------------------------------------------------

DROP TABLE IF EXISTS `layout`;

CREATE TABLE `layout` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_title` varchar(255) DEFAULT NULL,
  `logo_uri_login` varchar(255) DEFAULT NULL,
  `logo_uri_header` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `layout` WRITE;
/*!40000 ALTER TABLE `layout` DISABLE KEYS */;

INSERT INTO `layout` (`id`, `site_title`, `logo_uri_login`, `logo_uri_header`)
VALUES
	(1,'打刻keeper',NULL,NULL);

/*!40000 ALTER TABLE `layout` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ login_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login_data`;

CREATE TABLE `login_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_name` varchar(255) DEFAULT '',
  `authority` int(1) NOT NULL,
  `area_id` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `login_data` WRITE;
/*!40000 ALTER TABLE `login_data` DISABLE KEYS */;

INSERT INTO `login_data` (`id`, `login_id`, `password`, `user_name`, `authority`, `area_id`)
VALUES
	(1,'admin','$2y$10$HLeIq8336aa7RQp/WhKTde19LSYBmL8yn4vM9n4NTY5v8QM6MZGPm','管理者',4,NULL),
	(2,'user','$2y$10$h/VV2V5IrlRdzIhSXO5Gy.oHjZf7s/b1B2Aez7vSqjB7gDbKZZSs6','一般',1,NULL),
	(99,'ossy','$2y$10$.d/HlK5Q3Eauc0dE8JcJ2uW.E/5z6x35FnBLXLrGTbqm2mg1PWk9m','システム管理者',4,NULL),
	(100,'ossy2','$2y$10$.d/HlK5Q3Eauc0dE8JcJ2uW.E/5z6x35FnBLXLrGTbqm2mg1PWk9m','システム管理者',1,NULL);

/*!40000 ALTER TABLE `login_data` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ message_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `message_data`;

CREATE TABLE `message_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(15) DEFAULT NULL,
  `type` varchar(128) DEFAULT NULL,
  `message` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ message_title_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `message_title_data`;

CREATE TABLE `message_title_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(11) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `detail` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `message_title_data` WRITE;
/*!40000 ALTER TABLE `message_title_data` DISABLE KEYS */;

INSERT INTO `message_title_data` (`id`, `type`, `flag`, `title`, `detail`, `created_at`, `updated_at`)
VALUES
	(1,'gateway',0,'お知らせ','出退勤画面メッセージ内容',NULL,NULL);

/*!40000 ALTER TABLE `message_title_data` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ nonstop_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `nonstop_data`;

CREATE TABLE `nonstop_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(15) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT '0',
  `nonstop_date` date DEFAULT NULL,
  `create_datetime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ notice_auth
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notice_auth`;

CREATE TABLE `notice_auth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `low_user_id` int(11) DEFAULT NULL,
  `permit` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ notice_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notice_data`;

CREATE TABLE `notice_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notice_id` varchar(255) DEFAULT NULL,
  `notice_datetime` datetime DEFAULT NULL,
  `to_user_id` int(11) DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `notice_flag` int(2) DEFAULT NULL,
  `notice_in_time` time DEFAULT NULL,
  `notice_out_time` time DEFAULT NULL,
  `notice_status` int(1) DEFAULT '0',
  `from_user_id` int(11) DEFAULT NULL,
  `before_in_time` time DEFAULT NULL,
  `before_out_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ notice_status_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notice_status_data`;

CREATE TABLE `notice_status_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `notice_status_id` int(11) DEFAULT NULL,
  `notice_status_title` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `group` tinyint(1) DEFAULT NULL,
  `order` int(3) DEFAULT NULL,
  `term` tinyint(1) DEFAULT NULL COMMENT '0=過去のみ 1=未来のみ 9=条件なし',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `notice_status_data` WRITE;
/*!40000 ALTER TABLE `notice_status_data` DISABLE KEYS */;

INSERT INTO `notice_status_data` (`id`, `notice_status_id`, `notice_status_title`, `status`, `group`, `order`, `term`, `created_at`, `updated_at`)
VALUES
	(1,1,'修正依頼',1,1,1,0,NULL,NULL),
	(2,2,'削除依頼',1,1,2,0,NULL,NULL),
	(3,3,'遅刻申請',1,2,3,1,NULL,NULL),
	(4,4,'早退申請',1,2,4,1,NULL,NULL),
	(5,5,'残業申請',1,2,5,1,NULL,NULL),
	(6,6,'有給申請',1,3,7,1,NULL,NULL),
	(7,7,'欠勤申請',1,2,6,1,NULL,NULL),
	(8,8,'その他申請',1,3,8,9,NULL,NULL),
	(9,11,'休暇申請',0,3,9,1,NULL,NULL);

/*!40000 ALTER TABLE `notice_status_data` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ notice_text_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notice_text_data`;

CREATE TABLE `notice_text_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notice_id` bigint(20) DEFAULT NULL,
  `text_datetime` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `notice_text` text,
  `notice_status` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ notice_text_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notice_text_users`;

CREATE TABLE `notice_text_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notice_text_id` bigint(20) DEFAULT NULL,
  `user_id` int(15) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ paid_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `paid_data`;

CREATE TABLE `paid_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(15) DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `create_datetime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ pay_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pay_data`;

CREATE TABLE `pay_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date_y` int(4) DEFAULT NULL,
  `date_m` int(2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `work_month` int(11) DEFAULT NULL,
  `division_pay` int(1) DEFAULT NULL,
  `basic_pay` int(11) DEFAULT NULL,
  `allowance_job_class` int(11) DEFAULT NULL,
  `allowance_manager` int(11) DEFAULT NULL,
  `allowance_job_special` int(11) DEFAULT NULL,
  `allowance_duty` int(11) DEFAULT NULL,
  `allowance_managerial` int(11) DEFAULT NULL,
  `allowance_family` int(11) DEFAULT NULL,
  `allowance_house` int(11) DEFAULT NULL,
  `allowance_license` int(11) DEFAULT NULL,
  `makeup_pay` int(11) DEFAULT NULL,
  `hourly_pay` int(11) DEFAULT NULL,
  `hour_work` int(11) DEFAULT NULL,
  `allowance_over` int(11) DEFAULT NULL,
  `allowance_night` int(11) DEFAULT NULL,
  `pay` int(11) DEFAULT NULL,
  `paid_vacation` int(4) DEFAULT NULL,
  `year_work_days` int(4) DEFAULT NULL,
  `fixed_month_days` int(2) DEFAULT NULL,
  `hour_over_work` int(11) DEFAULT NULL,
  `hour_night_work` int(11) DEFAULT NULL,
  `deduction_absence` int(11) DEFAULT NULL,
  `deduction_late` int(11) DEFAULT NULL,
  `paid_vacation_pay` int(11) DEFAULT NULL,
  `work_days` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ pay_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pay_history`;

CREATE TABLE `pay_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_y` int(4) DEFAULT NULL,
  `date_m` int(2) DEFAULT NULL,
  `create_datetime` datetime DEFAULT NULL,
  `update_datetime` datetime DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `pay_day` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ payment_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_data`;

CREATE TABLE `payment_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) DEFAULT NULL COMMENT '従業員ID',
  `year` varchar(4) DEFAULT NULL COMMENT '年',
  `month` varchar(2) DEFAULT NULL COMMENT '月',
  `title` varchar(255) DEFAULT NULL,
  `work1` varchar(255) DEFAULT NULL,
  `work2` varchar(255) DEFAULT NULL,
  `work3` varchar(255) DEFAULT NULL,
  `work4` varchar(255) DEFAULT NULL,
  `work5` varchar(255) DEFAULT NULL,
  `work6` varchar(255) DEFAULT NULL,
  `work7` varchar(255) DEFAULT NULL,
  `work8` varchar(255) DEFAULT NULL,
  `work9` varchar(255) DEFAULT NULL,
  `work10` varchar(255) DEFAULT NULL,
  `work11` varchar(255) DEFAULT NULL,
  `work12` varchar(255) DEFAULT NULL,
  `work13` varchar(255) DEFAULT NULL,
  `work14` varchar(255) DEFAULT NULL,
  `pay1` varchar(255) DEFAULT NULL,
  `pay2` varchar(255) DEFAULT NULL,
  `pay3` varchar(255) DEFAULT NULL,
  `pay4` varchar(255) DEFAULT NULL,
  `pay5` varchar(255) DEFAULT NULL,
  `pay6` varchar(255) DEFAULT NULL,
  `pay7` varchar(255) DEFAULT NULL,
  `pay8` varchar(255) DEFAULT NULL,
  `pay9` varchar(255) DEFAULT NULL,
  `pay10` varchar(255) DEFAULT NULL,
  `pay11` varchar(255) DEFAULT NULL,
  `pay12` varchar(255) DEFAULT NULL,
  `pay13` varchar(255) DEFAULT NULL,
  `pay14` varchar(255) DEFAULT NULL,
  `deduct1` varchar(255) DEFAULT NULL,
  `deduct2` varchar(255) DEFAULT NULL,
  `deduct3` varchar(255) DEFAULT NULL,
  `deduct4` varchar(255) DEFAULT NULL,
  `deduct5` varchar(255) DEFAULT NULL,
  `deduct6` varchar(255) DEFAULT NULL,
  `deduct7` varchar(255) DEFAULT NULL,
  `deduct8` varchar(255) DEFAULT NULL,
  `deduct9` varchar(255) DEFAULT NULL,
  `deduct10` varchar(255) DEFAULT NULL,
  `deduct11` varchar(255) DEFAULT NULL,
  `deduct12` varchar(255) DEFAULT NULL,
  `deduct13` varchar(255) DEFAULT NULL,
  `deduct14` varchar(255) DEFAULT NULL,
  `total1` varchar(255) DEFAULT NULL,
  `total2` varchar(255) DEFAULT NULL,
  `total3` varchar(255) DEFAULT NULL,
  `total4` varchar(255) DEFAULT NULL,
  `total5` varchar(255) DEFAULT NULL,
  `total6` varchar(255) DEFAULT NULL,
  `total7` varchar(255) DEFAULT NULL,
  `memo` text,
  `open` tinyint(1) DEFAULT '0' COMMENT '公開　1 = する　0 = しない',
  `download` datetime DEFAULT NULL COMMENT 'ダウンロード日時',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ payment_password
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_password`;

CREATE TABLE `payment_password` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ payment_title
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_title`;

CREATE TABLE `payment_title` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL COMMENT '1 = 通常時使用　2 = サブ　0 = 未使用',
  `type` varchar(10) DEFAULT NULL,
  `order` varchar(10) DEFAULT NULL,
  `hozAlign` varchar(100) DEFAULT NULL,
  `formatter` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `payment_title` WRITE;
/*!40000 ALTER TABLE `payment_title` DISABLE KEYS */;

INSERT INTO `payment_title` (`id`, `field`, `title`, `status`, `type`, `order`, `hozAlign`, `formatter`, `created_at`, `updated_at`)
VALUES
	(1,'work','勤怠','1','0','100',NULL,NULL,'2022-10-16 15:48:35','2022-10-17 13:32:42'),
	(2,'work1','出勤日数','1','1','101','right',NULL,'2022-10-16 15:48:51','2022-10-17 13:40:23'),
	(3,'work2','欠勤日数','1','1','102','right',NULL,'2022-10-16 15:49:08','2022-10-17 13:43:28'),
	(4,'work3','有休日数','1','1','103','right',NULL,'2022-10-16 15:49:29','2022-10-17 13:43:29'),
	(5,'work4','有休残日数','1','1','104','right',NULL,'2022-10-16 15:49:50','2022-10-17 13:43:30'),
	(6,'work8','労働時間','1','1','108','right',NULL,'2022-10-16 15:55:57','2022-10-17 13:43:32'),
	(7,'work9','普通残業時間','1','1','109','right',NULL,'2022-10-16 15:56:31','2022-10-17 13:43:33'),
	(8,'work10','深夜労働時間','1','1','110','right',NULL,'2022-10-16 15:56:49','2022-10-17 13:43:35'),
	(9,'work11','休日労働時間','1','1','111','right',NULL,'2022-10-16 15:57:12','2022-10-17 13:43:36'),
	(10,'pay','支給','1','0','200',NULL,'money','2022-10-16 15:59:16','2022-10-17 16:14:28'),
	(11,'pay1','基本給','1','1','201','right','money','2022-10-16 15:59:29','2022-10-17 16:14:30'),
	(12,'pay2','時間外手当','1','1','202','right','money','2022-10-16 15:59:44','2022-10-17 16:14:31'),
	(13,'pay3','住宅手当','1','1','203','right','money','2022-10-16 15:59:59','2022-10-17 16:14:33'),
	(14,'pay4','子供手当','1','1','204','right','money','2022-10-16 16:00:41','2022-10-17 16:14:34'),
	(15,'pay5','管理職手当','1','1','205','right','money','2022-10-16 16:01:00','2022-10-17 16:14:35'),
	(16,'pay6','交通費','1','1','206','right','money','2022-10-16 16:01:17','2022-10-17 16:14:37'),
	(17,'pay7','調整金','1','1','207','right','money','2022-10-16 16:01:43','2022-10-17 16:14:38'),
	(18,'pay8','達成金A','1','1','208','right','money','2022-10-16 16:02:01','2022-10-17 16:14:39'),
	(19,'pay9','達成金B','1','1','209','right','money','2022-10-16 16:02:15','2022-10-17 16:14:40'),
	(20,'pay10','達成金C','1','1','210','right','money','2022-10-16 16:02:38','2022-10-17 16:14:42'),
	(21,'pay11','CP','1','1','211','right','money','2022-10-16 16:02:49','2022-10-17 16:14:43'),
	(22,'pay12','社長賞','1','1','212','right','money','2022-10-16 16:03:05','2022-10-17 16:14:44'),
	(23,'deduct','控除','1','0','300',NULL,'money','2022-10-16 16:12:49','2022-10-17 16:14:45'),
	(24,'deduct1','健康保険料','1','1','301','right','money','2022-10-16 16:13:07','2022-10-17 16:14:47'),
	(25,'deduct2','介護保険料','1','1','302','right','money','2022-10-16 16:13:31','2022-10-17 16:14:48'),
	(26,'deduct3','厚生年金等','1','1','303','right','money','2022-10-16 16:13:47','2022-10-17 16:14:50'),
	(27,'deduct4','雇用保険料','1','1','304','right','money','2022-10-16 16:14:13','2022-10-17 16:14:51'),
	(28,'deduct5','所得税','1','1','305','right','money','2022-10-16 16:14:27','2022-10-17 16:14:52'),
	(29,'deduct6','住民税','1','1','306','right','money','2022-10-16 16:14:42','2022-10-17 16:14:54'),
	(30,'deduct8','住宅控除','1','1','308','right','money','2022-10-16 16:15:05','2022-10-17 16:14:56'),
	(31,'deduct7','年末調整','1','1','307','right','money','2022-10-16 16:15:23','2022-10-17 16:14:57'),
	(32,'deduct14','その他控除','1','1','314','right','money','2022-10-16 16:15:41','2022-10-17 16:14:58'),
	(33,'total','合計','1','0','400',NULL,'money','2022-10-16 16:16:01','2022-10-17 16:15:00'),
	(34,'total1','支給合計額','1','1','401','right','money','2022-10-16 16:16:17','2022-10-17 16:15:02'),
	(35,'total2','控除合計額','1','1','402','right','money','2022-10-16 16:16:32','2022-10-17 16:15:04'),
	(36,'total3','差引支給額','1','1','403','right','money','2022-10-16 16:16:49','2022-10-17 16:15:05'),
	(37,'year','年','1','1','001',NULL,NULL,'2022-10-17 08:53:12','2022-10-17 13:36:03'),
	(38,'month','月','1','1','002',NULL,NULL,'2022-10-17 08:53:22','2022-10-17 13:36:08'),
	(39,'user_id','従業員ID','1','1','003',NULL,NULL,'2022-10-17 08:53:53','2022-10-17 13:36:11'),
	(40,'memo','メモ','1','1','500',NULL,NULL,'2022-10-17 08:56:42','2022-10-17 13:33:36'),
	(41,'title','{name}様 {wareki}年{month}月分給与明細書','1','0','900',NULL,NULL,'2022-10-17 17:20:09','2022-10-17 18:59:56');

/*!40000 ALTER TABLE `payment_title` ENABLE KEYS */;
UNLOCK TABLES;


# テーブルのダンプ rest_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rest_data`;

CREATE TABLE `rest_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(15) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT '0',
  `rest_date` date DEFAULT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `rest_hour` int(5) DEFAULT '0',
  `time_data_id` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ rest_rules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rest_rules`;

CREATE TABLE `rest_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_rules_id` int(11) DEFAULT NULL,
  `rest_time` int(3) DEFAULT NULL,
  `rest_type` tinyint(1) DEFAULT NULL,
  `limit_work_hour` int(4) DEFAULT NULL,
  `rest_in_time` time DEFAULT NULL,
  `rest_out_time` time DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ shift_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shift_data`;

CREATE TABLE `shift_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(15) NOT NULL,
  `rest` int(4) NOT NULL DEFAULT '0',
  `hour` int(4) NOT NULL DEFAULT '0',
  `status` int(4) NOT NULL DEFAULT '0',
  `dk_date` date DEFAULT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `paid_hour` float DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ shift_default_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shift_default_data`;

CREATE TABLE `shift_default_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date_y` int(4) NOT NULL,
  `date_m` int(2) NOT NULL,
  `date_d` int(2) NOT NULL,
  `in_date_h` int(2) DEFAULT NULL,
  `in_date_m` int(2) DEFAULT NULL,
  `out_date_h` int(2) DEFAULT NULL,
  `out_date_m` int(2) DEFAULT NULL,
  `stete` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ shift_register_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shift_register_data`;

CREATE TABLE `shift_register_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `dk_date` date DEFAULT NULL,
  `shift_status` tinyint(1) DEFAULT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `hour` int(4) DEFAULT '0',
  `rest` int(4) DEFAULT '0',
  `up_datetime` datetime DEFAULT NULL,
  `flag` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ shift_state_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shift_state_data`;

CREATE TABLE `shift_state_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_value` text,
  `in_date_h` int(2) DEFAULT NULL,
  `in_date_m` int(2) DEFAULT NULL,
  `out_date_h` int(2) DEFAULT NULL,
  `out_date_m` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ time_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `time_data`;

CREATE TABLE `time_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dk_date` date DEFAULT NULL,
  `user_id` int(15) DEFAULT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `in_work_time` time DEFAULT NULL,
  `out_work_time` time DEFAULT NULL,
  `rest` int(5) NOT NULL DEFAULT '0',
  `revision` int(1) NOT NULL DEFAULT '0',
  `in_flag` int(1) NOT NULL DEFAULT '0',
  `out_flag` int(1) NOT NULL DEFAULT '0',
  `fact_hour` int(5) NOT NULL DEFAULT '0',
  `fact_work_hour` int(5) NOT NULL DEFAULT '0',
  `status` text,
  `status_flag` int(2) NOT NULL DEFAULT '0',
  `over_hour` int(5) NOT NULL DEFAULT '0',
  `night_hour` int(5) NOT NULL DEFAULT '0',
  `left_hour` int(5) NOT NULL DEFAULT '0',
  `late_hour` int(5) NOT NULL DEFAULT '0',
  `holiday` float NOT NULL DEFAULT '0',
  `holiday2` int(2) NOT NULL DEFAULT '0',
  `memo` text,
  `shift_in_hour` int(3) DEFAULT NULL,
  `shift_out_hour` int(3) DEFAULT NULL,
  `series_work` int(2) DEFAULT '0',
  `series_holiday` int(2) DEFAULT '0',
  `area_id` int(11) DEFAULT NULL,
  `data_overlap_flag` int(2) DEFAULT NULL,
  `revision_user` varchar(255) DEFAULT NULL,
  `revision_datetime` datetime DEFAULT NULL,
  `notice_memo` text,
  `revision_in` int(1) DEFAULT '0',
  `revision_out` int(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ user_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_data`;

CREATE TABLE `user_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name_sei` varchar(255) NOT NULL DEFAULT '',
  `name_mei` varchar(255) NOT NULL DEFAULT '',
  `kana_sei` varchar(255) DEFAULT '',
  `kana_mei` varchar(255) DEFAULT '',
  `user_id` int(15) NOT NULL,
  `state` int(1) NOT NULL DEFAULT '1',
  `entry_date` date DEFAULT NULL,
  `resign_date` date DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT '',
  `sex` int(1) DEFAULT NULL,
  `memo` text,
  `phone_number1` varchar(20) DEFAULT NULL,
  `phone_number2` varchar(20) DEFAULT NULL,
  `email1` varchar(255) DEFAULT NULL,
  `email2` varchar(255) DEFAULT NULL,
  `put_paid_vacation_month` int(2) DEFAULT NULL,
  `aporan_flag` int(1) DEFAULT '0',
  `line_id` varchar(255) DEFAULT NULL,
  `line_name` varchar(255) DEFAULT NULL,
  `authority_id` int(11) DEFAULT NULL,
  `advance_pay_flag` tinyint(1) DEFAULT '0',
  `notice_mail_flag` tinyint(1) DEFAULT '0',
  `notice_line_flag` tinyint(1) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  `password_change` varchar(100) DEFAULT NULL,
  `in_time_pat` int(2) DEFAULT '0',
  `out_time_pat` int(2) DEFAULT '0',
  `start_month` varchar(6) DEFAULT NULL,
  `user_area_id` int(4) DEFAULT NULL,
  `idm` varchar(255) DEFAULT NULL,
  `shift_alert_flag` tinyint(1) DEFAULT '0' COMMENT '0=する　2=しない',
  `management_flag` tinyint(1) DEFAULT NULL,
  `esna_pay_flag` tinyint(1) DEFAULT NULL,
  `api_output` tinyint(1) DEFAULT NULL,
  `mypage_self` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ user_groups1
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_groups1`;

CREATE TABLE `user_groups1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL DEFAULT '',
  `state` int(1) NOT NULL DEFAULT '1',
  `group_order` int(4) NOT NULL,
  `in_time_pat` int(2) DEFAULT '0',
  `out_time_pat` int(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# テーブルのダンプ user_groups2
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_groups2`;

CREATE TABLE `user_groups2` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL DEFAULT '',
  `state` int(1) NOT NULL DEFAULT '1',
  `group_order` int(4) NOT NULL,
  `in_time_pat` int(2) DEFAULT '0',
  `out_time_pat` int(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# テーブルのダンプ user_groups3
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_groups3`;

CREATE TABLE `user_groups3` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL DEFAULT '',
  `state` int(1) NOT NULL DEFAULT '1',
  `group_order` int(4) NOT NULL,
  `in_time_pat` int(2) DEFAULT '0',
  `out_time_pat` int(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
