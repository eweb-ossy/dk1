<?php
defined('BASEPATH') or exit('No direct script access allowed');
// admin lists view

// メタ部　読み込み
$this->load->view('parts/_head_view');
?>

<body>
  <div class="container">
    <div class="header">
        <!-- ヘッダー部上部　読み込み -->
        <?php $this->load->view('parts/_header_top_view'); ?>
        <!-- ヘッダーメニュー　読み込み -->
        <?php $this->load->view('parts/_header_admin_menu_view'); ?>
    </div>
    <div class="main-title-area">
      <div class="title">
        <?= $page_title ?><span>/ 従業員別に勤務情報を表示します。</div>
      <div class="btn-area">
        <div class="btn-text"><i class="far fa-sticky-note"></i> ファイル出力</div>
        <div class="row">
          <div id="download_btn_excel" class="btn green"><i class="far fa-file-excel"></i> エクセル</div>
          <div id="download_btn_pdf" class="btn red"><i class="far fa-file-pdf"></i> PDF</div>
        </div>
      </div>
    </div>
  </div>
  <div class="date-area">
    <div class="inner" style="height:43px;">
      <div id="date-area-wareki"></div>
      <input id="month" class="month" value="">
      <div id="this_month_mark" class="this-month-mark">今月</div>
      <div class="user-area">
        <div id="user_kana" class="user-kana"></div>
        <div class="user-data"><span class="user-name" id="user_name"></span><span id="user_id"></span><span id="group1_name"></span><span id="group2_name"></span><span id="group3_name"></span></div>
      </div>
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
      <div class="term-area">
        <i class="fas fa-calendar-alt"></i>&nbsp;<span id="to_from_date"></span>
      </div>
      <div class="user-select-area"><i class="fas fa-user"></i>&nbsp;
        従業員変更：
        <select id="user_select" name="user_select">
        </select>
      </div>
      <div class="week-select-area"><i class="fas fa-calendar-week"></i>&nbsp;
        曜日で抽出：
        <select id="week_select" name="week_select">
          <option value="none">全て</option>
          <option value="月">月</option>
          <option value="火">火</option>
          <option value="水">水</option>
          <option value="木">木</option>
          <option value="金">金</option>
          <option value="土">土</option>
          <option value="日">日</option>
          <option value="祝">祝</option>
          <option value="土日祝">土日祝</option>
          <option value="平日">平日</option>
        </select>
      </div>
      <?php if ((int)$end_day->value > 0): ?>
      <div class="end-day-select-area"><i class="fas fa-calendar-times"></i>&nbsp;
        <select name="end_day" id="end_day_select">
          <option value="<?= (int)$end_day->value ?>"><?= (int)$end_day->value ?>日締め</option>
          <option value="0">月末締め</option>
        </select>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="main">
    <div id="data_table" class="table-area"></div>
  </div>
  <!-- 時間修正用モーダル -->
  <?php $this->load->view('parts/_modal_time_edit_view'); ?>
  <!-- javascript　読み込み -->
  <?php $this->load->view('parts/_javascript_view'); ?>
</body>

</html>