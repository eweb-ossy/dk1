<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// mypage dashboard view
?>

<!-- meta部　読み込み -->
<?php $this->load->view('parts/_head_view2'); ?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
<!-- <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/libs/siimple-toast.min.css">
<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/libs/iziModal.min.css">
<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/libs/flatpickr.min.css"> -->
<style>
@font-face {
    font-family: "D7MI";
    src: url("fonts/DSEG7Modern-Italic.woff") format('woff');
}
</style>
<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css2/mypage_dashboard.min.css?<?= time() ?>">
</head>
<body>
    <div class="wrapper">
        <!-- サイドバー読込 -->
        <?php $this->load->view('parts/_mypage_sidebar_view'); ?>
        <div class="main-panel">
            <!-- 上部ナビ　読込 -->
            <?php $this->load->view('parts/_mypage_top_nav_view'); ?>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- 時計 -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <p id="date_view" class="card-category"></p>
                                    <h3 class="card-title time" style="display:flex;">
                                        <div class="time-text">
                                            <div class="time-background">
                                                <div class="time-back">88:88</div>
                                                <div class="second-back">88</div>
                                            </div>
                                            <div class="time-front">
                                                <div id="time_view"></div>
                                                <div id="second" class="second-text"></div>
                                            </div>
                                        </div>
                                    </h3>
                                </div>
                                <div class="card-footer users-status-area">
                                    <?php if ($low_user === 1): ?>
                                    <div class="inner">
                                        <div class="in-list">
                                            <div class="title">出勤中一覧</div>
                                            <ul id="inUserData"></ul>
                                        </div>
                                        <div class="out-list">
                                            <div class="title">退勤一覧</div>
                                            <ul id="outUserData"></ul>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- パーソナルデータ -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <p id="user_id" class="card-category">..</p>
                                    <h3 id="user_name" class="card-title">..</h3>
                                    <p id="user_group" class="card-category">..</p>
                                </div>
                                <div id="user_notice_area" class="card-footer">
                                    <div class="stats">
                                        <i class="fas fa-walking"></i> <span id="user_count"></span><i class="fas fa-history" style="margin-left: 20px;"></i> <span id="user_time"></span>
                                    </div>
                                    <div class="dashboard-notice-btn-area">
                                        <div id="dashboard_status_btn" class="dashboard-status-btn"><i class="fas fa-user-clock"></i></div>
                                        <div id="dashboard_shift_btn" class="dashboard-shift-btn"><i class="fas fa-calendar-alt"></i></div>
                                        <div id="dashboard_notice_btn" class="dashboard-notice-btn"><i class="fas fa-paper-plane"></i> 申請依頼</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- 出退勤 -->
                        <?php if ((int)$mypage_input_flag->value === 1) : ?>
                        <div id="input_area" class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="input-area">
                                        <div class="input-btn-bloc">
                                            <div id="input_btn" class="input-btn in-input disable">出勤</div>
                                        </div>
                                        <?php if ((int)$rest_input_flag->value === 1) : ?>
                                        <div class="input-btn-bloc">
                                            <div id="rest_in_btn" class="input-btn two disable">休憩開始</div>
                                            <div id="rest_out_btn" class="input-btn two disable">休憩終了</div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ((int)$goaway_input_flag->value === 1) : ?>
                                        <div class="input-btn-bloc">
                                            <div class="input-btn two disable">中抜開始</div>
                                            <div class="input-btn two disable">中抜終了</div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="input-btn-bloc">
                                            <div id="output_btn" class="input-btn out-input disable">退勤</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="row notice-dashboard">
                        <!-- 通知 -->
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <!-- <div class="card-header card-header-tabs card-header-dakoku">
                                <div class="nav-tabs-navigation">
                                    <div class="nav-tabs-wrapper">
                                    <span class="nav-tabs-title">通知：</span>
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                        <div class="nav-link active" data-tab="tab01">
                                            <i class="far fa-paper-plane"></i> 申告中
                                            <div class="ripple-container"></div>
                                        </div>
                                        </li>
                                        <li class="nav-item">
                                        <div class="nav-link" data-tab="tab02">
                                            <i class="fas fa-thumbs-up"></i> 承認済み
                                            <div class="ripple-container"></div>
                                        </div>
                                        </li>
                                        <li class="nav-item">
                                        <div class="nav-link" data-tab="tab03">
                                            <i class="fas fa-exclamation-circle"></i> NG
                                            <div class="ripple-container"></div>
                                        </div>
                                        </li>
                                    </ul>
                                    </div>
                                </div>
                                </div> -->
                                <div class="card-body">
                                    <div class="notice-dashboard-body">
                                        <div class="tab-pane notice_area01 active" id="tab01"></div>
                                        <div class="tab-pane notice_area02" id="tab02"></div>
                                        <div class="tab-pane notice_area03" id="tab03"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- 直行・直帰ボタン -->
                        <?php if ((int)$mypage_input_flag->value === 1 && (int)$nonstop_input_flag->value === 1) : ?>
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="input-area">
                                        <div class="input-btn-bloc" style="height:70px">
                                            <div id="nonstop_in" class="input-btn  disable">直行出勤</div>
                                        </div>
                                        <div class="input-btn-bloc" style="height:70px">
                                            <div id="nonstop_out" class="input-btn disable">直帰退勤</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const user_id = <?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8');?>
    </script>
    <!-- javascript　読み込み -->
    <script defer src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js2/<?= $page_id ?>.js"></script>
</body>
</html>