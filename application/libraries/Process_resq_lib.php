<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_resq_lib
{
    protected $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    public function resq_send($post)
    {
        $url  = "https://api.resqryo.com/attendance/update";
        $options = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query($post),
        CURLOPT_FOLLOWLOCATION => true,
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $output = curl_exec($curl);
        $recult = json_decode($output, true);
        log_message('info', 'resq: '.$recult['result_code'].':'.$recult['message']);
    }
}
