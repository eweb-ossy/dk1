<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_User_data extends CI_Migration
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
            'name_sei' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'name_mei' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'kana_sei' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null'=> TRUE
            ],
            'kana_mei' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null'=> TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 15
            ],
            'state' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 1
            ],
            'entry_date' => [
                'type' => 'DATE',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'resign_date' => [
                'type' => 'DATE',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'birth_date' => [
                'type' => 'DATE',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'zip_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'sex' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'memo' => [
                'type' => 'TEXT',
                'null'=> TRUE
            ],
            'phone_number1' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'phone_number2' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'email1' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'email2' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'put_paid_vacation_month' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'aporan_flag' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'line_id' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'line_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'authority_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'advance_pay_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'notice_mail_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'notice_line_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
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
            'start_month' => [
                'type' => 'VARCHAR',
                'constraint' => '6',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'idm' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'shift_alert_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE,
                'comment' => '0=する　2=しない'
            ],
            'management_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'esna_pay_flag' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'api_output' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'mypage_self' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'area_id' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default'=> NULL,
                'null'=> TRUE,
                'comment' => 'エリアID登録用'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('user_data');

        // $data = [
        //     [
        //         'name_sei'=> 'test',
        //         'name_mei'=> 'test',
        //         'user_id'=> 11111,
        //         'state'=> 1,
        //         'password'=> '$2y$10$sKoJPKjNheQpnv7mFYAYY.z6kUjKTwD3mHqUNv4JkiyBZq/tZQrIO'
        //     ]
        // ];
        // $this->db->insert_batch('user_data', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('user_data');
    }
}