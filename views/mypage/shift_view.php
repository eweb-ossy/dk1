<?php defined('BASEPATH') or exit('No direct script access allowed');
// mypage shift view

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
            <input id="month" class="month" value="">
            <div id="this_month_mark" class="this-month-mark">今月</div>
          </div>
          <div class="inner">
            <div class="bloc">
              <div id="this_month" class="date-btn disable">今月のシフト</div>
            </div>
            <div class="bloc">
              <div id="less_month" class="date-btn less-btn">戻る</div>
            </div>
            <div class="bloc">
              <div id="add_month" class="date-btn add-btn">次へ</div>
            </div>
            <div class="term-area"><i class="fas fa-calendar-alt"></i> <span id="to_from_date"></span></div>
          </div>
        </div>
        <div class="main-area">
          <div class="lists-area">
            <div id="list_date" class="list-date"><i class="fas fa-calendar"></i> カレンダーを選択</div>
            <div id="statue_area" class="list-status">
              <div class="status-title">予定</div>
              <input type="radio" name="status" id="status_0" value="0">
              <label for="status_0" class="radio-text">出勤</label>
              <input type="radio" name="status" id="status_1" value="1">
              <label for="status_1" class="radio-text">公休</label>
              <input type="radio" name="status" id="status_2" value="2">
              <label for="status_2" class="radio-text">有給</label>
              <!-- <input type="radio" name="status" id="status_none" value="99">
              <label for="status_none" class="radio-text">未定</label> -->
            </div>
            <div id="time_area">
              <div class="time-title">出勤時刻</div>
              <div id="in_time_picker" class="list-time"></div>
              <div class="time-title">退勤時刻</div>
              <div id="out_time_picker" class="list-time"></div>
            </div>
            <div class="btn-area">
              <div id="shift_register_btn" class="input-btn disabled"><i class="fas fa-comment-alt"></i> 申請</div>
            </div>
						<div class="btn-area">
              <div id="undata_change_btn" class="input-btn sub-btn disabled"><i class="fas fa-check"></i> 未登録 → 公休</div>
            </div>
						<div class="btn-area">
              <div id="del_btn" class="input-btn sub-btn"><i class="fas fa-times"></i> 全て未登録にする</div>
            </div>
          </div>
          <div class="calendar-area">
            <div id="calendar"></div>
          </div>
        </div>
			</div>
		</div>
	</div>
	<!-- javascript読込 -->
	<?php $this->load->view('parts/_mypage_javascript_view'); ?>
</body>

</html>
