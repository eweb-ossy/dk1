<?php
defined('BASEPATH') or exit('No direct script access allowed');
// Mypage 共通パーツ　上部ナビ
?>
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
	<div class="container-fluid">
		<div class="navbar-wrapper">
			<?= $mypage_title ?>
		</div>
		<button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
			aria-expanded="false" aria-label="Toggle navigation">
			<span class="sr-only">Toggle navigation</span>
			<span class="navbar-toggler-icon icon-bar"></span>
			<span class="navbar-toggler-icon icon-bar"></span>
			<span class="navbar-toggler-icon icon-bar"></span>
		</button>
		<div class="collapse navbar-collapse justify-content-end">
			<!-- 検索 -->
			<!-- <form class="navbar-form">
				<div class="input-group no-border">
					<input type="text" value="" class="form-control" placeholder="Search...">
					<button type="submit" class="btn btn-white btn-round btn-just-icon">
						<i class="fas fa-search"></i>
						<div class="ripple-container"></div>
					</button>
				</div>
			</form> -->
			<ul class="navbar-nav">
				<!-- <li class="nav-item">
					<a class="nav-link" href="#pablo">
						<i class="fas fa-th-large"></i>
						<p class="d-lg-none d-md-block">
							Stats
						</p>
					</a>
				</li> -->
				<!-- <li class="nav-item dropdown">
					<div class="header-menu-btn">
						<i class="fas fa-bell"></i>
						<span class="notification" style="display:none;"></span>
					</div>
					<div id="header_notice" class="dropdown-menu dropdown-menu-right">
						<div class="no-data">未読通知はありません</div>
					</div>
				</li> -->
				<!-- <li class="nav-item dropdown">
					<a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true"
						aria-expanded="false">
						<i class="fas fa-user"></i>
						<p class="d-lg-none d-md-block">
							Account
						</p>
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
						<a class="dropdown-item" href="#">Profile</a>
						<a class="dropdown-item" href="#">Settings</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">Log out</a>
					</div>
				</li> -->
				<li class="nav-item">
					<a href="./logout" class="nav-link">
						<i class="fas fa-sign-out-alt"></i> ログアウト
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>