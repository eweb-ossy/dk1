<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Config_rules extends CI_Migration
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
                'constraint' => 11,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'group_id' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'group_no' => [
                'type' => 'TINYINT',
                'constraint' => 3,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'all_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'in_marume_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'in_marume_hour' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'in_marume_type' => [
                'type' => 'VARCHAR',
                'constraint' => '4',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'in_marume_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'out_marume_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'out_marume_hour' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'out_marume_type' => [
                'type' => 'VARCHAR',
                'constraint' => '4',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'out_marume_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'basic_in_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'basic_out_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'basic_rest_weekday' => [
                'type' => 'VARCHAR',
                'constraint' => '8',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'rest_rule_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'over_limit_hour' => [
                'type' => 'INT',
                'constraint' => 4,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 1,
                'null'=> TRUE
            ],
            'order' => [
                'type' => 'INT',
                'constraint' => 3,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'summary' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('config_rules');
    }

    public function down()
    {
        $this->dbforge->drop_table('config_rules');
    }
}