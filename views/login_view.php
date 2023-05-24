<?php
defined('BASEPATH') or exit('No direct script access allowed');
// login view

// メタ部　読み込み
$this->load->view('parts/_head_view');
?>

<body>
	<div class="login-area">
		<div class="inner">
			<div class="form-title">
				<?php if ($logo_uri_login): ?>
				<div style="text-align:center">
					<img src="<?= $logo_uri_login ?>" alt="">
				</div>
				<?php else: ?>
					<img src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>images/logo.svg" alt="">
				<?php endif; ?>
				<div class="sub-title"><?= $company_name->value ?></div>
			</div>
			<form id="login_form" method="post" accept-charset="utf-8">
				<input type="hidden" name="latitude">
				<input type="hidden" name="longitude">
				<input type="hidden" name="gps_info">
				<input type="hidden" name="agent">
				<div class="input-field">
					<input id="login_id" type="text" name="login_id" placeholder="ログインID" autocomplete="off" autocorrect="off"
						autocapitalize="off" value="" required="">
				</div>
				<div class="input-field">
					<input id="password" type="password" name="password" placeholder="パスワード" required="" autocomplete="off">
				</div>
				<button class="form-btn" type="submit">ログイン</button>
			</form>
			<div class="error-message"></div>
			<div id="out" class="system-message">ログインIDとパスワードを入力してください。</div>
		</div>
	</div>
	<div id="global_message" class="global-message"></div>
	<script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js2/<?= $page_id ?>.js"></script>
</body>

</html>
