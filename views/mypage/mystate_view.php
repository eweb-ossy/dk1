<?php defined('BASEPATH') or exit('No direct script access allowed');
// mypage mystate view マイ 勤務状況

// メタ部　読み込み
$this->load->view('parts/_mypage_head_view');
?>

<body>
  <div class="wrapper ">
    <!-- サイドバー読込 -->
    <?php $this->load->view('parts/_mypage_sidebar_view'); ?>
    <div class="main-panel">
      <!-- 上部ナビ　読込 -->
      <?php $this->load->view('parts/_mypage_top_nav_view'); ?>
      <div class="content">
          <div class="date-area">
            <div class="inner">
              <div id="date-area-wareki" style="font-size:11px;"></div>
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
              <div class="term-area" style="bottom:0;"><i class="fas fa-calendar-alt"></i> <span id="to_from_date"></span></div>
            </div>
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
    var mypage_my_inout_view_flag = <?= (int)$mypage_my_inout_view_flag->value ?>;
  </script>
</body>

</html>