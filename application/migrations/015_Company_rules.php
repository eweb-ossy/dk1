<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Company_rules extends CI_Migration
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
            'rule' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint'=> '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint'=> '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'memo' => [
                'type' => 'TEXT',
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('company_rules');

        $data = [
            [
                'rule'=> 'company_week_start',
                'type'=> 'INT',
                'value'=> '0',
                'memo'=> '１週間の定義　開始曜日'
            ],
            [
                'rule'=> 'company_end_day',
                'type'=> 'INT',
                'value'=> '0',
                'memo'=> '月の締め日　0=月末'
            ],
            [
                'rule'=> 'company_over_day',
                'type'=> 'INT',
                'value'=> '0',
                'memo'=> '１日の終了時刻　0=24時'
            ]
        ];
        $this->db->insert_batch('company_rules', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('company_rules');
    }
}