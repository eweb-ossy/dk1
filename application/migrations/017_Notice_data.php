<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Notice_data extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'notice_id' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'notice_datetime' => [
                'type' => 'DATETIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'to_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'to_date' => [
                'type' => 'DATE',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'notice_flag' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'notice_in_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'notice_out_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'notice_status' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'from_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'before_in_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'before_out_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'end_date' => [
                'type' => 'DATE',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('notice_data');
    }

    public function down()
    {
        $this->dbforge->drop_table('notice_data');
    }
}