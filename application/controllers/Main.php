<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Main extends CI_Controller
{

  // 最初のアクセスコントロール
    public function index()
    {
        // セッションチェック
        if ($this->session->is_logged_in) {
            $authority = (int)$this->session->authority; // 権限
            if ($authority === 1) {
                redirect('/gateway'); // 出退勤ページ
            }
            if ($authority > 1) {
                redirect('/admin_list_day'); // 管理ページ
            }
            if ($authority === 0) {
                redirect('/mypage_dashboard'); // マイページ
            }
        } else {
            redirect('/login'); // ログインページ
        }
    }
}
