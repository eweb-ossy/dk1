<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

// フックポイント
// display_override
//  CI_Outputクラスによるレスポンス出力処理をユーザ独自のレスポンス出力処理で上書きする。CI_Outputの出力処理は実行されなくなる。

// compress output
$hook['display_override'][] = array(
  'class' => '',
  'function' => 'compress',
  'filename' => 'compress.php',
  'filepath' => 'hooks'
);
