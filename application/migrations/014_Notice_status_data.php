<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Notice_status_data extends CI_Migration
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
            'notice_status_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'notice_status_title' => [
                'type' => 'VARCHAR',
                'constraint'=> '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 1,
                'null'=> TRUE
            ],
            'group' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'order' => [
                'type' => 'INT',
                'constraint' => 3,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'term' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('notice_status_data');

        $data = [
            [
                'notice_status_id'=> 1,
                'notice_status_title'=> '修正依頼',
                'status'=> 1,
                'group'=> 1,
                'order'=> 1,
                'term'=> 0
            ],
            [
                'notice_status_id'=> 2,
                'notice_status_title'=> '削除依頼',
                'status'=> 1,
                'group'=> 1,
                'order'=> 2,
                'term'=> 0
            ],
            [
                'notice_status_id'=> 3,
                'notice_status_title'=> '遅刻申請',
                'status'=> 1,
                'group'=> 2,
                'order'=> 3,
                'term'=> 1
            ],
            [
                'notice_status_id'=> 4,
                'notice_status_title'=> '早退申請',
                'status'=> 1,
                'group'=> 2,
                'order'=> 4,
                'term'=> 1
            ],
            [
                'notice_status_id'=> 5,
                'notice_status_title'=> '残業申請',
                'status'=> 1,
                'group'=> 2,
                'order'=> 5,
                'term'=> 1
            ],
            [
                'notice_status_id'=> 6,
                'notice_status_title'=> '有給申請',
                'status'=> 1,
                'group'=> 3,
                'order'=> 7,
                'term'=> 9
            ],
            [
                'notice_status_id'=> 7,
                'notice_status_title'=> '欠勤申請',
                'status'=> 1,
                'group'=> 2,
                'order'=> 6,
                'term'=> 1
            ],
            [
                'notice_status_id'=> 8,
                'notice_status_title'=> 'その他申請',
                'status'=> 1,
                'group'=> 3,
                'order'=> 8,
                'term'=> 9
            ],
            [
                'notice_status_id'=> 11,
                'notice_status_title'=> '休暇申請',
                'status'=> 1,
                'group'=> 3,
                'order'=> 9,
                'term'=> 1
            ]
        ];
        $this->db->insert_batch('notice_status_data', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('notice_status_data');
    }
}