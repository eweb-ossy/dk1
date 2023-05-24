<?php
defined('BASEPATH') or exit('No direct script access allowed');
// admin users view

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
		<div class="main-title-area">
			<div class="title"><?php echo $page_title; ?><span>/ 従業員の管理をおこないます</div>
			<div class="btn-area">
				<div class="btn-text"><i class="far fa-sticky-note"></i> ファイル出力</div>
				<div class="row">
					<div id="excel" class="btn green download-btn"><i class="far fa-file-excel"></i> エクセル</div>
					<div id="pdf" class="btn red download-btn"><i class="far fa-file-pdf"></i> PDF</div>
				</div>
			</div>
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
		<div class="data-area">
			<div class="box">
				<p>従業員 全て：<span id="user_all_num"></span>名</p>
				<p>従業員 既存：<span id="user_state_num"></span>名 （<span id="user_state_rate">-</span>%）</p>
				<p>従業員 退職：<span id="user_resign_num"></span>名 （<span id="user_resign_rate">-</span>%）</p>
			</div>
			<div class="box">
				<p>性別 男性：<span id="sex_man"></span>名 （<span id="sex_man_rate">-</span>%）</p>
				<p>性別 女性：<span id="sex_woman"></span>名 （<span id="sex_woman_rate">-</span>%）</p>
				<p>性別 不明：<span id="sex_no"></span>名 （<span id="sex_no_rate">-</span>%）</p>
			</div>
		</div>
		<div class="btn-area">
			<div class="row">
				<!-- 新規従業員登録ボタン -->
				<div id="create_user" class="btn red"><i class="fas fa-user-plus"></i> 新規従業員登録</div>
				<!-- <div id="create_user_all" class="btn blue"><i class="fas fa-user-plus"></i> 一括登録</div>
				<input type="file" name="" id="users_file_upload" class="input-file" accept=".csv"> -->
			</div>
		</div>
	</div>
	<!-- メイン テーブル部分 -->
	<div class="main">
		<div id="data_table" class="table-area"></div>
	</div>
  	<script>
        const authority = <?= (int)$this->session->authority ?>;
    </script>
  	<script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js2/admin_user.js?<?= time() ?>"></script>
</body>

</html>