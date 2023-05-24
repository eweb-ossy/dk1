<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol'] = 'smtp';
$config['smtp_host'] = 'sv1114.xserver.jp';
$config['smtp_user'] = 'auto_mail@dakoku-keeper.com';
$config['smtp_pass'] = 'fH4rNb9K';
$config['smtp_port'] = 587;
$config['smtp_crypto'] = 'tls';
$config['crlf'] = "\r\n";
$config['newline'] = "\n";
$config['charset'] = 'UTF-8';
$config['wordwrap'] = FALSE;
// $config['wrapchars'] = 76;

$this->email->initialize($config);
