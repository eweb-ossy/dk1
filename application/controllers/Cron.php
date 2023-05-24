<?php
defined('BASEPATH') or exit('No direct script access alllowed');

// /usr/bin/php7.2 /home/eweb/dk-keeper.com/public_html/demo/index.php cron statusCheck "e-web"

class Cron extends CI_Controller
{
    public function statusCheck($system_id = '')
    {
        if (!is_cli()) {
            redirect('/');
        }
        $now = new DateTime();
        $now_date = $now->format('Y-m-d');
        $message = 'cron statusCheck : '.$system_id;
        log_message('info', $message);
        echo $message.PHP_EOL;

        $this->load->model('model_time');
        $result = $this->model_time->gets_now_users($now_date);
        print_r($result);
    }
}