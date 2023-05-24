<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Time_data extends CI_Migration
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
            'dk_date' => [
                'type' => 'DATE',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 15,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'in_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'in_work_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'out_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'out_work_time' => [
                'type' => 'TIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'rest' => [
                'type' => 'INT',
                'constraint' => 5,
                'default'=> 0
            ],
            'revision' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 0
            ],
            'in_flag' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 0
            ],
            'out_flag' => [
                'type' => 'INT',
                'constraint' => 1,
                'default'=> 0
            ],
            'fact_hour' => [
                'type' => 'INT',
                'constraint' => 5,
                'default'=> 0
            ],
            'fact_work_hour' => [
                'type' => 'INT',
                'constraint' => 5,
                'default'=> 0
            ],
            'status' => [
                'type' => 'TEXT',
                'null'=> TRUE
            ],
            'status_flag' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0
            ],
            'over_hour' => [
                'type' => 'INT',
                'constraint' => 5,
                'default'=> 0
            ],
            'night_hour' => [
                'type' => 'INT',
                'constraint' => 5,
                'default'=> 0
            ],
            'left_hour' => [
                'type' => 'INT',
                'constraint' => 5,
                'default'=> 0
            ],
            'late_hour' => [
                'type' => 'INT',
                'constraint' => 5,
                'default'=> 0
            ],
            'holiday' => [
                'type' => 'FLOAT',
                'default'=> 0
            ],
            'holiday2' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0
            ],
            'memo' => [
                'type' => 'TEXT',
                'null'=> TRUE
            ],
            'shift_in_hour' => [
                'type' => 'INT',
                'constraint' => 3,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'shift_out_hour' => [
                'type' => 'INT',
                'constraint' => 3,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'series_work' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0,
                'null'=> TRUE
            ],
            'series_holiday' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> 0,
                'null'=> TRUE
            ],
            'area_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'data_overlap_flag' => [
                'type' => 'INT',
                'constraint' => 2,
                'default'=> NULL,
                'null'=> TRUE
            ],
            'revision_user' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'revision_datetime' => [
                'type' => 'DATETIME',
                'default'=> NULL,
                'null'=> TRUE
            ],
            'notice_memo' => [
                'type' => 'TEXT'
            ],
            'revision_in' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE
            ],
            'revision_out' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
                'null'=> TRUE,
                'comment' => '退勤修正フラグ'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('time_data');
    }

    public function down()
    {
        $this->dbforge->drop_table('time_data');
    }
}