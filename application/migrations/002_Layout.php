<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Layout extends CI_Migration
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
            'site_title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'logo_uri_login' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'logo_uri_header' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('layout');

        $data = [
            [
                'site_title'=> '打刻keeper',
                'logo_uri_login'=> '',
                'logo_uri_header'=> ''
            ]
        ];
        $this->db->insert_batch('layout', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('layout');
    }
}