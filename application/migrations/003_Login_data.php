<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Login_data extends CI_Migration
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
            'login_id' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null'=> TRUE
            ],
            'authority' => [
                'type' => 'INT',
                'constraint' => '1'
            ],
            'area_id' => [
                'type' => 'INT',
                'constraint' => '3',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('login_data');

        $data = [
            [
                'id'=> 1,
                'login_id'=> 'admin',
                'password'=> '$2y$10$xruJJRrSbx8PRN6MhX9EiOJg0y1UGIHn/Y0LbLww2GrDLCphvVP3K',
                'user_name'=> '管理者',
                'authority'=> 4
            ],
            [
                'id'=> 2,
                'login_id'=> 'user',
                'password'=> '$2y$10$h/VV2V5IrlRdzIhSXO5Gy.oHjZf7s/b1B2Aez7vSqjB7gDbKZZSs6',
                'user_name'=> '一般',
                'authority'=> 1
            ],
            [
                'id'=> 99,
                'login_id'=> 'ossy',
                'password'=> '$2y$10$.d/HlK5Q3Eauc0dE8JcJ2uW.E/5z6x35FnBLXLrGTbqm2mg1PWk9m',
                'user_name'=> 'システム管理者',
                'authority'=> 4
            ],
            [
                'id'=> 100, 
                'login_id'=> 'ossy2',
                'password'=> '$2y$10$.d/HlK5Q3Eauc0dE8JcJ2uW.E/5z6x35FnBLXLrGTbqm2mg1PWk9m',
                'user_name'=> 'システム管理者',
                'authority'=> 1
            ]
        ];
        $this->db->insert_batch('login_data', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('login_data');
    }
}