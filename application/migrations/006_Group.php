<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Group extends CI_Migration
{
    // group関連
    public function up()
    {
        // group_title
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'group_id' => [
                'type' => 'INT',
                'constraint' => 1
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('group_title');

        $data = [
            [
                'group_id' => 1,
                'title' => '雇用形態'
            ]
        ];
        $this->db->insert_batch('group_title', $data);

        // user_groups 1-3 今後削除予定
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'group_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'state' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 1
            ],
            'group_order' => [
                'type' => 'INT',
                'constraint' => 4
            ],
            'in_time_pat' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0,
                'null'=> TRUE
            ],
            'out_time_pat' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('user_groups1');

        $data = [
            [
                'group_name' => '社員',
                'state' => 1,
                'group_order' => 1
            ],
            [
                'group_name' => 'アルバイト',
                'state' => 1,
                'group_order' => 2
            ],
        ];
        $this->db->insert_batch('user_groups1', $data);

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'group_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'state' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 1
            ],
            'group_order' => [
                'type' => 'INT',
                'constraint' => 4
            ],
            'in_time_pat' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0,
                'null'=> TRUE
            ],
            'out_time_pat' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('user_groups2');

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'group_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'state' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 1
            ],
            'group_order' => [
                'type' => 'INT',
                'constraint' => 4
            ],
            'in_time_pat' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0,
                'null'=> TRUE
            ],
            'out_time_pat' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('user_groups3');

        // group_history 
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 15
            ],
            'group1_id' => [
                'type' => 'INT',
                'constraint' => 4,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'group2_id' => [
                'type' => 'INT',
                'constraint' => 4,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'group3_id' => [
                'type' => 'INT',
                'constraint' => 4,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'to_date' => [
                'type' => 'DATE',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('group_history');
    }

    public function down()
    {
        $this->dbforge->drop_table('group_title');
        $this->dbforge->drop_table('user_groups1');
        $this->dbforge->drop_table('user_groups2');
        $this->dbforge->drop_table('user_groups3');
        $this->dbforge->drop_table('group_history');
    }
    
}