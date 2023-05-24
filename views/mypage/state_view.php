<?php defined('BASEPATH') or exit('No direct script access allowed');
// mypage state view 従業員 勤務状況（日別）

// メタ部　読み込み
$this->load->view('parts/_mypage_head_view');
?>

<body>
    <div class="wrapper ">
        <!-- サイドバー読込 -->
        <?php $this->load->view('parts/_mypage_sidebar_view'); ?>
        <div class="main-panel">
            <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <?= $mypage_title ?>

                        <?php if ($system_id->value === 'kaku'): ?>
                        <div class="btn-area">
                            <div class="btn-text"><i class="far fa-sticky-note"></i> ファイル出力</div>
                            <div class="row">
                                <div id="download_btn_excel" class="btn green"><i class="far fa-file-excel"></i> エクセル</div>
                                <div id="download_btn_pdf" class="btn red"><i class="far fa-file-pdf"></i> PDF</div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a href="./logout" class="nav-link">
                                    <i class="fas fa-sign-out-alt"></i> ログアウト
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="content">
                <div class="date-area">
                    <div class="inner">
                        <div id="date-area-wareki" style="font-size:11px;"></div>
                        <input id="datepicker" class="date">
                        <div id="today_mark" class="today-mark">本日</div>
                    </div>
                    <div class="inner">
                        <div class="bloc">
                            <div id="date_today" class="date-btn disable">本日の集計</div>
                        </div>
                        <div class="bloc">
                            <div id="less_day" class="date-btn less-btn">戻る</div>
                        </div>
                        <div class="bloc">
                            <div id="add_day" class="date-btn add-btn">次へ</div>
                        </div>
                    </div>
                    <div id="table_window_btn" class="table-top-mark">詳細表示</div>
                </div>
                <div class="main">
                    <div id="data_table" class="table-area"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- 時間修正用モーダル -->
    <?php $this->load->view('parts/_modal_time_edit_view'); ?>
    <!-- javascript読込 -->
    <?php $this->load->view('parts/_mypage_javascript_view'); ?>
    <script>
    var mypage_status_inout_view_flag = <?= (int)$mypage_status_inout_view_flag->value ?>;
    </script>
</body>

</html>