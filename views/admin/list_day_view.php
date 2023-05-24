<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// admin list_day view

// メタ部　読み込み
$this->load->view('parts/_head_view');
?>
<body>
  <style>
    body, #datepicker {
      user-select: none;
    }
    .flatpickr-calendar .flatpickr-current-month {
    display: flex;
    justify-content: center;
    flex-direction: row-reverse;
  }
  .iziModal-overlay.comingIn, .iziModal.comingIn {
    /* animation: none !important; */
  }
  .iziModal .iziModal-header {
    z-index: 0 !important;
  }
  .iziModal.transitionIn .iziModal-header {
    animation-delay: 0s !important;
    animation: none !important;
  }
  .iziModal.transitionIn .iziModal-header .iziModal-header-icon, .iziModal.transitionIn .iziModal-header .iziModal-header-title {
    animation-delay: 0s !important;
  }
  .iziModal.transitionIn .iziModal-header .iziModal-header-subtitle {
    animation-delay: 0s !important;
  }
  </style>
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
      <div class="title"><?= $page_title ?><span>/ 日別にて勤務情報を表示します。</span></div>
      <div class="btn-area">
        <div class="btn-text"><i class="far fa-sticky-note"></i> ファイル出力</div>
        <div class="row">
          <div id="download_btn_excel" class="btn green"><i class="far fa-file-excel"></i> エクセル</div>
          <div id="download_btn_pdf" class="btn red"><i class="far fa-file-pdf"></i> PDF</div>
        </div>
      </div>
    </div>
    <div class="date-area">
      <div class="inner">
        <div id="date-area-wareki"></div>
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
  <?php
  // 時間修正用モーダル
  $this->load->view('parts/_modal_time_edit_view');
  // アラート
  // $this->load->view('parts/_alert_view');
  // javascript　読み込み
  $this->load->view('parts/_javascript_view');
  ?>

</body>
</html>
