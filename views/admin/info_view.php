<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// admin info view

// メタ部　読み込み
$this->load->view('parts/_head_view');
?>
<body>
    <div class="container">
        <div class="header">
            <?php
            // ヘッダー部上部　読み込み
            $this->load->view('parts/_header_top_view');
            // ヘッダーメニュー　読み込み
            $this->load->view('parts/_header_admin_menu_view');
            ?>
        </div>
        <div class="main-title-area">
            <div class="title"><?= $page_title ?><span>/ 各種状況を表示します。</span></div>
        </div>
        <div class="main">
            <div class="main-box">
                <div class="main-box-header">
                    <div class="title-area">
                        <div class="title">勤務状況</div>
                        <input type="text" class="date-picker">
                    </div>
                </div>
                <div class="main-box-body">
                    <div class="list">
                        <div id="list_table"></div>
                    </div>
                </div>
                <div class="main-box-footer"></div>
            </div>
        </div>
    </div>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js2/admin_info.js"></script>
</body>
</html>