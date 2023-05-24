<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Message_title_data extends CI_Migration
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
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '11',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'detail' => [
                'type' => 'TEXT'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('message_title_data');

        $data = [
            [
                'id' => 1,
                'type'=> 'gateway',
                'flag'=> 0,
                'title'=> 'お知らせ',
                'detail'=> 'お知らせの内容がここにはいります。'
            ]
        ];
        $this->db->insert_batch('message_title_data', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('message_title_data');
    }
}