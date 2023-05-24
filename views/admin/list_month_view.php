<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// admin list_day view

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
            <div class="title"><?= $page_title ?><span>/ 月別にて勤務情報を表示します。</span></div>
            <div class="btn-area">
                <div class="btn-text"><i class="far fa-sticky-note"></i> ファイル出力</div>
                <div class="row">
                    <div id="excel" class="btn green download-btn"><i class="far fa-file-excel"></i> エクセル</div>
					<div id="pdf" class="btn red download-btn"><i class="far fa-file-pdf"></i> PDF</div>
                </div>
            </div>
        </div>
        <div class="date-area">
            <div class="inner">
            <div id="date-area-wareki"></div>
                <input id="month" class="month" value="">
                <div id="this_month_mark" class="this-month-mark">今月</div>
            </div>
            <div class="inner">
                <div class="bloc">
                    <div id="this_month" class="date-btn disable">今月の集計</div>
                </div>
                <div class="bloc">
                    <div id="less_month" class="date-btn less-btn">戻る</div>
                </div>
                <div class="bloc">
                    <div id="add_month" class="date-btn add-btn">次へ</div>
                </div>
            </div>
            <div class="term-area"><i class="fas fa-calendar-alt"></i> <span id="to_from_date"></span></div>
            <div id="table_window_btn" class="table-top-mark">グループ表示</div>
            <div class="detail-area">
                <div id="type_1" class="view-change-btn on">0.00h</div>
                <div id="type_2" class="view-change-btn">00:00-00:00</div>
                <?php if ($system_id->value === 'shelter2' || $system_id->value === 'shelter'): ?>
                <div id="type_3" class="view-change-btn">分割表示</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="main">
            <div id="data_table" class="table-area"></div>
            <!-- <div id="loading_table" class="loading-table">
                <div class="self-building-square-spinner loading">
                    <div class="square"></div>
                    <div class="square"></div>
                    <div class="square"></div>
                    <div class="square clear"></div>
                    <div class="square"></div>
                    <div class="square"></div>
                    <div class="square clear"></div>
                    <div class="square"></div>
                    <div class="square"></div>
                </div>
            </div> -->
        </div>
    </div>

    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js2/admin_list_month.js?<?= time() ?>"></script>
</body>
</html>
