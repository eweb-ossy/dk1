<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Paid_leave extends CI_Migration
{
    public function up()
    {
        $this->load->database();
        $sql = "CREATE TABLE `paid_leave` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT comment '管理id',
            `user_id` int(15) NOT NULL comment '従業員id',
            `dk_date` int(3) DEFAULT NULL comment '年月日',
            `paid` varchar(10) DEFAULT NULL comment '有給休暇 1なら全日、0.5は半休、0.1は1/10日',
            `paid_status` int(1) DEFAULT NULL comment 'ステータス null,0=未消化 1=消化済み 2=取消 ',
            `memo` text comment 'メモ',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          )
          ENGINE=InnoDB
          DEFAULT CHARSET=utf8
          comment='有給休暇データを保存';";
          $this->db->query($sql);
    }

    public function down()
    {
        $this->dbforge->drop_table('paid_leave');
    }
}