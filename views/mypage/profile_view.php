<?php defined('BASEPATH') or exit('No direct script access allowed');
// mypage profile view

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
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header card-header-dakoku">
									<h4 class="card-title">プロフィール</h4>
									<p class="card-category">
										<?php if ((int)$mypage_profile_edit_flag->value === 1): ?>
										プロフィールの編集ができます。
										<?php endif; ?>
										<?php if ((int)$mypage_password_edit_flag->value === 1): ?>
										パスワードの変更ができます。
										<?php endif; ?>
										<?php if ((int)$mypage_password_edit_flag->value === 0 && (int)$mypage_profile_edit_flag->value === 0): ?>
										変更はできません。
										<?php endif; ?>
									</p>
								</div>
								<div class="card-body">
									<form>
										<input type="hidden" name="id">
										<div class="row">
											<div class="col-md-2">
												<div class="form-group">
													<label class="bmd-label-floating">従業員ID</label>
													<input type="text" class="form-control disabled" name="user_id" disabled>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="bmd-label-floating">姓</label>
													<input type="text" class="form-control" name="name_sei" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="bmd-label-floating">名</label>
													<input type="text" class="form-control" name="name_mei" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="bmd-label-floating">セイ</label>
													<input type="text" class="form-control" name="kana_sei" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="bmd-label-floating">メイ</label>
													<input type="text" class="form-control" name="kana_mei" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label class="bmd-label-floating">性別</label>
													<div>
														<input class="form-radio" type="radio" name="sex" id="sex_man" value="1" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
														<label class="form-radio-label" for="sex_man">男性</label>
														<input class="form-radio" type="radio" name="sex" id="sex_woman" value="2" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
														<label class="form-radio-label" for="sex_woman">女性</label>
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="bmd-label-floating">誕生日</label>
													<input type="text" name="birth_date" id="birth_date" class="form-control" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class="bmd-label-floating">年齢</label>
													<input type="text" name="old" id="old" class="form-control" disabled>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-2">
												<div class="form-group">
													<label class="bmd-label-floating">郵便番号</label>
													<input type="text" class="form-control" name="zip_code" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
											<div class="col-md-10">
												<div class="form-group">
													<label class="bmd-label-floating">住所</label>
													<input type="text" class="form-control" name="address" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="bmd-label-floating">電話番号１</label>
													<input type="text" name="phone_number1" class="form-control" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="bmd-label-floating">電話番号２</label>
													<input type="text" name="phone_number2" class="form-control" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="bmd-label-floating">メールアドレス１</label>
													<input type="text" name="email1" class="form-control" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="bmd-label-floating">メールアドレス２</label>
													<input type="text" name="email2" class="form-control" <?php if ((int)$mypage_profile_edit_flag->value === 0): ?>disabled<?php endif; ?>>
												</div>
											</div>
										</div>
										<?php if ((int)$mypage_password_edit_flag->value === 1): ?>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="inputPassword6">パスワード変更</label>
													<input type="password" name="user_password" class="form-control mx-sm-3" aria-describedby="passwordHelpInline">
													<small id="passwordHelpInline" class="text-muted">※ パスワードを変更する場合は入力</small>
												</div>
											</div>
										</div>
										<?php endif; ?>
										<?php if ((int)$mypage_profile_edit_flag->value === 1 || (int)$mypage_password_edit_flag->value === 1): ?>
										<button id="profile_submit" type="submit" class="btn btn-info pull-right">保存</button>
										<?php endif; ?>
										<div class="clearfix"></div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- javascript読込 -->
	<?php $this->load->view('parts/_mypage_javascript_view'); ?>
</body>

</html>