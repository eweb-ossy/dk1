<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- 使用 CSS URL -->
<?php 
$css_url = [
  'izimodal' => 'css/libs/iziModal.min.css',
  'flatpickr' => 'css/libs/flatpickr.min.css',
  'flatpickr_monthSelect' => 'css/libs/flatpickr_monthSelect.min.css',
  'siiimple-toast' => 'css/libs/siimple-toast.min.css'
];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="icon" href="./favicon.ico">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URI ?>dist/icons/apple-icon-180x180.png">

	<!-- 共通 CSS -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">


	<!-- ダッシュボード　dashboard CSS -->
	<?php if ($page_id === 'mypage_dashboard'): ?>
		<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
		<style>
	    @font-face {
	      font-family: "D7MI";
	      src: url("fonts/DSEG7Modern-Italic.woff") format('woff');
	    }
	  </style>
	<?php endif; ?>
	
	<!-- マイ 勤務状況 mystate CSS -->
	<?php if ($page_id === 'mypage_mystate'): ?>
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr_monthSelect'] ?>">
	<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
	  <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
	<?php endif; ?>
	
	<!-- 従業員 勤務状況（日別） state CSS -->
	<?php if ($page_id === 'mypage_state'): ?>
		<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
	  <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
	  <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
	<?php endif; ?>
  
  <!-- 従業員 勤務状況（集計） statelist JS -->
  <?php if ($page_id === 'mypage_statelist'): ?>
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr_monthSelect'] ?>">
	<?php endif; ?>
  
  <!-- 従業員 勤務状況（個人） list CSS -->
	<?php if ($page_id === 'mypage_list'): ?>
		<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
	  <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr_monthSelect'] ?>">
	<?php endif; ?>
	
	<!-- 申請　apply CSS -->
	<?php if ($page_id === 'mypage_apply'): ?>
		<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
		<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
	<?php endif; ?>
	
	<!-- 通知　notice CSS -->
	<?php if ($page_id === 'mypage_notice'): ?>
	<?php endif; ?>
  
  <!-- シフト　shift CSS -->
	<?php if ($page_id === 'mypage_shift'): ?>
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/libs/fullcalendar/core/main.min.css">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/libs/fullcalendar/daygrid/main.min.css">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/libs/tui-time-picker.css">
	<?php endif; ?>
	
	<!-- プロフィール profile CSS -->
	<?php if ($page_id === 'mypage_profile'): ?>
		<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
		<link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
	<?php endif; ?>


	<!-- 各ページ CSS -->
	<link rel="stylesheet"
		href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/<?= $page_id ?>.min.css?<?= time() ?>">

	<!-- ページタイトル -->
	<title><?= $page_title ?>｜<?= $site_title ?></title>
	
</head>
