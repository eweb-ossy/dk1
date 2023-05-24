<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Message_data extends CI_Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 15,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint'=> '128',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'message' => [
                'type' => 'TEXT',
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('message_data');
    }

    public function down()
    {
        $this->dbforge->drop_table('message_data');
    }
}