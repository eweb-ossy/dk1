<?php
defined('BASEPATH') or exit('No direct script access allowed');
// admin user detail view

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
		<div class="main-title-area" style="height:62px;">
			<div class="title">
				<?php echo $page_title; ?><span>/ 従業員の管理をおこないます</div>
			<!-- <div class="btn-area">
				<div class="btn-text"><i class="far fa-sticky-note"></i> ファイル出力</div>
				<div class="row">
					<div id="download_btn_excel" class="btn green"><i class="far fa-file-excel"></i> エクセル</div>
					<div id="download_btn_pdf" class="btn red"><i class="far fa-file-pdf"></i> PDF</div>
				</div>
			</div> -->
		</div>
	</div>
	<div class="date-area">
		<div class="inner">
			<div class="user-area">
				<div id="user_kana" class="user-kana"></div>
				<div class="user-data"><span class="user-name" id="user_name"></span>　<span id="user_id"></span><span id="group1_name"></span><span id="group2_name"></span><span id="group3_name"></span></div>
			</div>
			<div id="mark" class="mark edit"></div>
		</div>
	</div>
	<div class="main">
		<div class="side-menu">
			<ul>
				<li id="tab_01" class="tab active-menu"><i class="fas fa-chevron-right"></i> 基本設定</li>
				<li id="tab_02" class="tab"><i class="fas fa-chevron-right"></i> グループ設定</li>
				<li id="tab_03" class="tab"><i class="fas fa-chevron-right"></i> 詳細設定</li>
				<li id="tab_04" class="tab"><i class="fas fa-chevron-right"></i> 通知・承認権限<span id="notice_batch" class="badge">0</span></li>
				<?php if ((int)$advance_pay_flag->value === 1 || (int)$aporan_flag->value === 1): ?>
				<li id="tab_05" class="tab"><i class="fas fa-chevron-right"></i> 配信設定</li>
				<?php endif; ?>
				<li class="back-list-btn"><a href="/admin_users"><i class="fas fa-list"></i> 一覧へ戻る</a></li>
				<li id="tab_99" class="tab user-del-btn"><i class="fas fa-user-slash"></i> 削除</li>
			</ul>
		</div>
		<div class="contents">
			<!-- page 01 基本設定 -->
			<div id="page_01" class="tab-page active-page">
				<form action="" autocomplete="off">
					<input type="hidden" name="id">
					<div class="content-header">
						<div class="page-title">基本設定</div>
						<div id="submit_page_01" class="btn green disabled">保存</div>
					</div>
					<p>
						<label for="input__user_id" class="filed-title">従業員ID</label>
						<input type="text" name="user_id" id="input__user_id" class="field_page_01 disabled" maxlength="<?= (int)$id_size->value ?>" disabled>
					</p>
					<p>
						<label for="input__name_sei" class="filed-title">名前</label>
						姓：<input type="text" name="name_sei" id="input__name_sei" class="field_page_01">
						　名：<input type="text" name="name_mei" class="field_page_01">
					</p>
					<p>
						<label class="filed-title">フリガナ</label>
						<label>セイ：<input type="text" name="kana_sei" class="field_page_01"></label><label>メイ：<input type="text" name="kana_mei" class="field_page_01"></label>
					</p>
					<p>
						<label class="filed-title">性別</label>
						<select name="sex" class="field_page_01">
							<option value="1">男性</option>
							<option value="2">女性</option>
							<option value="null" selected></option>
						</select>
					</p>
					<!-- <p>
						<input type="hidden" name="group_history_id">
						<label class="filed-title">グループ</label>
						<label id="group_label1" for="group1"></label>
						<label id="group_label2" for="group2"></label>
						<label id="group_label3" for="group3"></label>
					</p> -->
					<p>
						<label class="filed-title">状況</label>
						<label><input type="radio" name="state" class="field_page_01" value="1" checked="checked">既存者　</label>
						<label id="resign_btn"><input type="radio" name="state" class="field_page_01" value="2">退職者</label>
					</p>
					<p>
						<label class="filed-title">入社日</label>
						<input type="text" name="entry_date" id="entry_date" class="field_page_01">
					</p>
					<p id="resign_date_area">
						<label class="filed-title">退職日</label>
						<input type="text" name="resign_date" id="resign_date" class="field_page_01">
					</p>
					<p id="user_password_area">
						<label id="password_label_text" class="filed-title"></label>
						<input type="password" name="user_password" class="field_page_01">
					</p>
					<?php if ((int)$mypage_shift_alert->value !== 0): ?>
					<p>
						<label class="filed-title">シフト提出警告</label>
						<label><input type="radio" name="shift_alert_flag" class="field_page_01" value="0">する　</label>
						<label><input type="radio" name="shift_alert_flag" class="field_page_01" value="2">しない　</label>
					</p>
					<?php endif; ?>
					<?php if ((int)$input_confirm_flag->value !== 0): ?>
					<p>
						<label class="filed-title">打刻入力時に一時確認（ポップアップ確認）</label>
						<label><input type="radio" name="input_confirm_flag" class="field_page_01" value="0">する　</label>
						<label><input type="radio" name="input_confirm_flag" class="field_page_01" value="2">しない　</label>
					</p>
					<?php endif; ?>
					<p style="margin-top:80px">
						<label class="filed-title">管理のみ対応の従業員 ※こちらを「する」に設定した場合は、出退勤はしない管理のみの従業員となります。</label>
						<label><input type="radio" name="management_flag" class="field_page_01" value="0">しない　</label>
						<label><input type="radio" name="management_flag" class="field_page_01" value="1">する　</label>
					</p>
				</form>
			</div>
			<!-- page 02 グループ設定 -->
			<div id="page_02" class="tab-page active-page">
				<form action="" autocomplete="off">
					<div class="content-header">
						<div class="page-title">グループ設定</div>
						<div id="submit_page_02" class="btn green disabled">保存</div>
					</div>
					<div style="width:100%;margin-bottom:5px;display:flex;justify-content:flex-end;">
	          <div id="group_add_btn" class="login-btn login-btn-blue"><i class="fas fa-plus"></i> 新規グループ追加</div>
	        </div>
					<table>
						<thead>
							<tr style="font-size:9px;">
								<th>開始日</th>
								<th>終了日</th>
								<th id="group_title1"></th>
								<th id="group_title2"></th>
								<th id="group_title3"></th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody id="group_body">
						</tbody>
					</table>
				</form>
			</div>
			<!-- page 03 詳細設定 -->
			<div id="page_03" class="tab-page active-page">
				<form action="" autocomplete="off">
					<div class="content-header">
						<div class="page-title">詳細設定</div>
						<div id="submit_page_03" class="btn green">保存</div>
					</div>
					<p>
						<label class="filed-title">誕生日</label>
						<input type="text" name="birth_date" id="birth_date" class="field_page_03">
						<label id="old" class="old-area"></label>
					</p>
					<p>
						<label class="filed-title">電話番号</label>
						<label>番号１：
							<input type="text" name="phone_number1" class="field_page_03">
						</label>
						<label>番号２：
							<input type="text" name="phone_number2" class="field_page_03">
						</label>
					</p>
					<p>
						<label class="filed-title">メールアドレス</label>
						<label>メール１：
							<input type="text" name="email1" class="field_page_03" style="width:200px;">
						</label>
						<label>メール２：
							<input type="text" name="email2" class="field_page_03" style="width:200px;">
						</label>
					</p>
					<p>
						<label class="filed-title">住所</label>
						<label>〒
							<input type="text" name="zip_code" class="field_page_03" maxlength="8" style="width:80px;" onKeyUp="AjaxZip3.zip2addr(this,'','address','address');">
						</label>
						<label>
							<input type="text" name="address" class="field_page_03" style="width:600px;">
						</label>
					</p>
				</form>
			</div>
			<!-- page 04 承認権限 -->
			<div id="page_04" class="tab-page active-page">
				<div class="content-header">
					<div class="page-title">通知・承認権限</div><span class="tips" data-tippy-content="申請に対し承認可能の従業員を選択。<br>「×」をチェックマークにします。<br>承認の権限の有無は「承認権限」をチェックマークにします。<br>「通知」が来ないと「権限」あっても「承認」できません。「承認権限」ありの場合は必ず「通知」ありにしてください。"><i class="fas fa-question-circle"></i></span>
					<div id="submit_page_04" class="btn green">保存</div>
				</div>
				<div style="width:100%;margin-bottom:5px;display:flex;">
          <div id="all_notice" class="check-btn">
						<div class="check-btn-text">通知を全て</div>
						<svg enable-background="new 0 0 24 24" height="14" width="14" viewBox="0 0 24 24" xml:space="preserve"><path fill="#2DC214" clip-rule="evenodd" d="M21.652,3.211c-0.293-0.295-0.77-0.295-1.061,0L9.41,14.34  c-0.293,0.297-0.771,0.297-1.062,0L3.449,9.351C3.304,9.203,3.114,9.13,2.923,9.129C2.73,9.128,2.534,9.201,2.387,9.351  l-2.165,1.946C0.078,11.445,0,11.63,0,11.823c0,0.194,0.078,0.397,0.223,0.544l4.94,5.184c0.292,0.296,0.771,0.776,1.062,1.07  l2.124,2.141c0.292,0.293,0.769,0.293,1.062,0l14.366-14.34c0.293-0.294,0.293-0.777,0-1.071L21.652,3.211z" fill-rule="evenodd"></path></svg>
					</div>
          <div id="all_none_notice" class="check-btn">
						<div class="check-btn-text">通知を全て</div>
						<svg enable-background="new 0 0 24 24" height="14" width="14" viewBox="0 0 24 24" xml:space="preserve"><path fill="#CE1515" d="M22.245,4.015c0.313,0.313,0.313,0.826,0,1.139l-6.276,6.27c-0.313,0.312-0.313,0.826,0,1.14l6.273,6.272  c0.313,0.313,0.313,0.826,0,1.14l-2.285,2.277c-0.314,0.312-0.828,0.312-1.142,0l-6.271-6.271c-0.313-0.313-0.828-0.313-1.141,0  l-6.276,6.267c-0.313,0.313-0.828,0.313-1.141,0l-2.282-2.28c-0.313-0.313-0.313-0.826,0-1.14l6.278-6.269  c0.313-0.312,0.313-0.826,0-1.14L1.709,5.147c-0.314-0.313-0.314-0.827,0-1.14l2.284-2.278C4.308,1.417,4.821,1.417,5.135,1.73  L11.405,8c0.314,0.314,0.828,0.314,1.141,0.001l6.276-6.267c0.312-0.312,0.826-0.312,1.141,0L22.245,4.015z"></path></svg>
					</div>
					<div id="all_permit" class="check-btn">
						<div class="check-btn-text">承認権限を全て</div>
						<svg enable-background="new 0 0 24 24" height="14" width="14" viewBox="0 0 24 24" xml:space="preserve"><path fill="#2DC214" clip-rule="evenodd" d="M21.652,3.211c-0.293-0.295-0.77-0.295-1.061,0L9.41,14.34  c-0.293,0.297-0.771,0.297-1.062,0L3.449,9.351C3.304,9.203,3.114,9.13,2.923,9.129C2.73,9.128,2.534,9.201,2.387,9.351  l-2.165,1.946C0.078,11.445,0,11.63,0,11.823c0,0.194,0.078,0.397,0.223,0.544l4.94,5.184c0.292,0.296,0.771,0.776,1.062,1.07  l2.124,2.141c0.292,0.293,0.769,0.293,1.062,0l14.366-14.34c0.293-0.294,0.293-0.777,0-1.071L21.652,3.211z" fill-rule="evenodd"></path></svg>
					</div>
          <div id="all_none_permit" class="check-btn">
						<div class="check-btn-text">承認権限を全て</div>
						<svg enable-background="new 0 0 24 24" height="14" width="14" viewBox="0 0 24 24" xml:space="preserve"><path fill="#CE1515" d="M22.245,4.015c0.313,0.313,0.313,0.826,0,1.139l-6.276,6.27c-0.313,0.312-0.313,0.826,0,1.14l6.273,6.272  c0.313,0.313,0.313,0.826,0,1.14l-2.285,2.277c-0.314,0.312-0.828,0.312-1.142,0l-6.271-6.271c-0.313-0.313-0.828-0.313-1.141,0  l-6.276,6.267c-0.313,0.313-0.828,0.313-1.141,0l-2.282-2.28c-0.313-0.313-0.313-0.826,0-1.14l6.278-6.269  c0.313-0.312,0.313-0.826,0-1.14L1.709,5.147c-0.314-0.313-0.314-0.827,0-1.14l2.284-2.278C4.308,1.417,4.821,1.417,5.135,1.73  L11.405,8c0.314,0.314,0.828,0.314,1.141,0.001l6.276-6.267c0.312-0.312,0.826-0.312,1.141,0L22.245,4.015z"></path></svg>
					</div>
        </div>
				<div id="user_notice_table" class="table-area"></div>
			</div>
			<!-- page 05 配信設定 -->
			<div id="page_05" class="tab-page active-page">
				<form action="" autocomplete="off">
					<div class="content-header">
						<div class="page-title">配信設定</div>
						<div id="submit_page_05" class="btn green">保存</div>
					</div>
					<?php if ((int)$aporan_flag->value === 1): ?>
					<p>
						<label class="filed-title">アポラン配信</label>
						<label><input type="radio" name="aporan_flag" class="field_page_05" value="1">する　</label>
						<label><input type="radio" name="aporan_flag" class="field_page_05" value="0">しない</label>
					</p>
					<?php endif; ?>
					<?php if ((int)$advance_pay_flag->value === 1): ?>
					<p>
						<label class="filed-title">前払い配信</label>
						<label><input type="radio" name="advance_pay_flag" class="field_page_05" value="1">する　</label>
						<label><input type="radio" name="advance_pay_flag" class="field_page_05" value="0">しない</label>
					</p>
					<?php endif; ?>
					<?php if ((int)$esna_pay_flag->value === 1): ?>
					<p>
						<label class="filed-title">ESNA時給システム連携</label>
						<label><input type="radio" name="esna_pay_flag" class="field_page_05" value="1">する　</label>
						<label><input type="radio" name="esna_pay_flag" class="field_page_05" value="0">しない</label>
					</p>
					<?php endif; ?>
					<?php if ((int)$user_api_output_flag->value === 1): ?>
					<p>
						<label class="filed-title">API連携</label>
						<label><input type="radio" name="api_output" class="field_page_05" value="1">する　</label>
						<label><input type="radio" name="api_output" class="field_page_05" value="0">しない</label>
					</p>
					<?php endif; ?>
				</form>
			</div>
			<!-- page 99 削除 -->
			<div id="page_99" class="tab-page active-page">
				<div class="content-header">
					<div class="page-title" style="color:#f00;"><i class="fas fa-exclamation-triangle"></i> 削除</div>
				</div>
				<div class="del-text">表示されている従業員を削除します。削除された従業員データは全て無くなりますので注意が必要です。<br>退職されて過去のデータを残したい場合は「基本設定」＞「状況」にて「退職者」に設定し、削除しないことをお勧めします。<br><br>従業員を削除してよろしいですか？<br>
					<div id="submit_page_99" class="btn red" style="margin: 20px 0 0 0;">削除する</div>
				</div>
			</div>

		</div>
	</div>

	<div id="modal_group" class="modal" data-iziModal-fullscreen="false" data-iziModal-title="" data-iziModal-subtitle="" data-iziModal-icon="icon-status">
		<div class="modal-content"></div>
	</div>
	<?php
    // javascript　読み込み
    $this->load->view('parts/_javascript_view');
    ?>
</body>

</html>
