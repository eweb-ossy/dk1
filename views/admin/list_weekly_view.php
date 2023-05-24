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
      <div class="title"><?= $page_title ?><span>/ 週別にて勤務情報を表示します。</span></div>
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
        <input id="datepicker" class="date">
        <div id="today_mark" class="today-mark">今週</div>
      </div>
      <div class="inner">
        <div class="bloc">
          <div id="date_weekly" class="date-btn disable">今週の集計</div>
        </div>
        <div class="bloc">
          <div id="less_weekly" class="date-btn less-btn">戻る</div>
        </div>
        <div class="bloc">
          <div id="add_weekey" class="date-btn add-btn">次へ</div>
        </div>
      </div>
      <div class="term-area"><i class="fas fa-calendar-alt"></i> <span id="to_from_date"></span></div>
      <div id="table_window_btn" class="table-top-mark">詳細表示</div>
    </div>
    <div class="main">
      <div id="data_table" class="table-area"></div>
    </div>

  </div>
  <?php
  // javascript　読み込み
  $this->load->view('parts/_javascript_view');
  ?>
</body>
</html>
