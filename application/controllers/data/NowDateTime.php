<?php
defined('BASEPATH') or exit('No direct script access alllowed');

header('Access-Control-Allow-Origin: *');

class NowDateTime extends CI_Controller
{
    // 
    public function index()
    {
        $now = new DateTime();
        $this->load->helper('holiday_date');
        $holiday_datetime = new HolidayDateTime($now->format('Y-m-d'));
        $holiday_datetime->holiday() ? $w = 7 : $w = (int)$now->format('w');
        $week = array("日", "月", "火", "水", "木", "金", "土", "祝");

        $data = [
            'now' => $now->format(DateTime::W3C),
            'date' => $now->format('Y-m-d'),
            'date_ja' => $now->format('Y年m月d日'),
            'year' => (int)$now->format('Y'),
            'month' => (int)$now->format('m'),
            'day' => (int)$now->format('d'),
            'week' => $week[$w],
            'holiday' => $holiday_datetime->holiday() ? $holiday_datetime->holiday() : '',
            'time' => $now->format('H:i:s'),
            'time_w' => $now->format('H:i'),
            'time_w_ja' => $now->format('H時i分'),
            'hour' => (int)$now->format('H'),
            'minute' => (int)$now->format('i'),
            'second' => (int)$now->format('s'),
            'hour_w' => $now->format('H'),
            'minute_w' => $now->format('i'),
            'second_w' => $now->format('s'),
            'msec' => (int)$now->format('v')
        ];

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}