<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- 使用 CSS URL -->
<?php 
$css_url = [
    'izimodal' => 'css/libs/iziModal.min.css',
    'flatpickr' => 'css/libs/flatpickr.min.css',
    'flatpickr_monthSelect' => 'css/libs/flatpickr_monthSelect.min.css',
    'siiimple-toast' => 'css/libs/siimple-toast.min.css',
    'alertify' => 'css/libs/alertify.min.css',
    'alertify-default' => 'css/libs/alertify-default.min.css'
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
    <!-- gateway css -->
    <?php if ($page_id === 'gateway') : ?>
        <style>
            @font-face {
            font-family: "D7MI";
            src: url("fonts/DSEG7Modern-Italic.woff") format('woff');
            }
        </style>
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['alertify'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['alertify-default'] ?>">
    <?php endif; ?>

    <!-- admin_list_day css -->
    <?php if ($page_id === 'admin_list_day') : ?>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
    <?php endif; ?>

    <!-- admin_list_month css -->
    <?php if ($page_id === 'admin_list_month') : ?>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr_monthSelect'] ?>">
    <?php endif; ?>

    <!-- admin_list_user css -->
    <?php if ($page_id === 'admin_list_user') : ?>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr_monthSelect'] ?>">
    <?php endif; ?>

    <!-- admin lists css -->
    <?php if ($page_id === 'admin_lists') : ?>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr_monthSelect'] ?>">
    <?php endif; ?>

    <!-- admin shift css -->
    <?php if ($page_id === 'admin_shift') : ?>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/libs/fullcalendar/core/main.min.css">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/libs/fullcalendar/daygrid/main.min.css">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr_monthSelect'] ?>">
    <?php endif; ?>

    <!-- admin user detail css -->
    <?php if ($page_id === 'admin_user_detail') : ?>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
    <?php endif; ?>

    <!-- admin conf css -->
    <?php if ($page_id === 'admin_conf') : ?>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['siiimple-toast'] ?>">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['izimodal'] ?>">
    <?php endif; ?>

    <!-- admin_list_weekly css -->
    <?php if ($page_id === 'admin_list_weekly') : ?>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $css_url['flatpickr'] ?>">
    <?php endif; ?>

    <!-- 各ページ css -->
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/<?= $page_id ?>.min.css?<?= time() ?>">

    <!-- ページタイトル -->
    <title><?= $page_title ?>｜<?= $site_title ?></title>

</head>