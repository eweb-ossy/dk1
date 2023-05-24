<?php

defined('BASEPATH') or exit('No direct script access alllowed');

class Admin_list_weekly extends CI_Controller
{
  
    public function table_data()
    {
        $start_date = $this->input->post('startDate');
        $end_date = $this->input->post('endDate');

        $interval = new DateInterval('P1D');
        $period = new DatePeriod(new DateTime($start_date), $interval, new DateTime($end_date));

    }
}