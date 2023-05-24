<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Rest_rules extends CI_Migration
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
            'config_rules_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'rest_time' => [
                'type' => 'INT',
                'constraint' => 3,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'rest_type' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'limit_work_hour' => [
                'type' => 'INT',
                'constraint' => 4,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'rest_in_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'rest_out_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('rest_rules');
    }

    public function down()
    {
        $this->dbforge->drop_table('rest_rules');
    }
}