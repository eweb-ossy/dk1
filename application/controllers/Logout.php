<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Logout extends CI_Controller
{
    public function index()
    {
        $this->session->sess_destroy(); // セッション削除
        redirect('/');
    }
}
