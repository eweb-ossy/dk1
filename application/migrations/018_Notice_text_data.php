<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Notice_text_data extends CI_Migration
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
                'type' => 'BIGINT',
                'constraint' => 20,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'text_datetime' => [
                'type' => 'DATETIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'notice_text' => [
                'type' => 'TEXT',
                'null'=> TRUE
            ],
            'notice_status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('notice_text_data');
    }

    public function down()
    {
        $this->dbforge->drop_table('notice_text_data');
    }
}