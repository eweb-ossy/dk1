<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Rest_data extends CI_Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 15,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'rest_date' => [
                'type' => 'DATE',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'in_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'out_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'rest_hour' => [
                'type' => 'INT',
                'constraint' => 5,
                'default'=> 0,
                'null'=> TRUE
            ],
            'time_data_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('rest_data');
    }

    public function down()
    {
        $this->dbforge->drop_table('rest_data');
    }
}