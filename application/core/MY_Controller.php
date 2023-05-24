<?php 

class MY_Controller extends CI_Controller {
    public $data;
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        // config data を取得
        $query = $this->db->query('SELECT id, config_name, type, value FROM config_values');
        $this->data['configs'] = array_column($query->result(), NULL, 'config_name');

        // user data の チェック
        $query = $this->db->query('SELECT id FROM user_data WHERE state = 1');
        $users = $query->result();
        if ($users > 0) {
            $this->users = count($users);
        } else {
            $this->users = NULL;
        }
    }
}