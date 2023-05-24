<?php
defined('BASEPATH') or exit('No direct script access allowed');
// admin to view 

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
		<!-- 管理画面　表題部分 -->
		<div class="main-title-area" style="height:62px;">
			<div class="title"><?php echo $page_title; ?><span>/ 各種配信の管理をおこないます</div>
		</div>
	</div>
	<!-- 集計表示　テーブル操作　部分 -->
	<div class="date-area date-users-area">
		<div class="filter-box">
			<p>従業員 表示</p>
			<select name="user_state_filter" id="">
				<option value="1">既存者</option>
				<option value="2">退職者</option>
				<option value="0">全て</option>
			</select>
		</div>
	</div>
	<!-- メイン テーブル部分 -->
	<div class="main">
		<div id="data_table" class="table-area"></div>
	</div>
	<?php
  // javascript　読み込み
  $this->load->view('parts/_javascript_view');
  ?>
</body>

</html>