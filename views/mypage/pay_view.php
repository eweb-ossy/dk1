<?php defined('BASEPATH') or exit('No direct script access allowed');
// my page pay view 
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title><?= $page_title ?>｜<?= $site_title ?></title>
    <link rel="icon" href="./favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URI ?>dist/icons/apple-icon-180x180.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/<?= $page_id ?>.min.css?<?= time() ?>">
</head>
<body>
    <div class="wrapper">
        <?php $this->load->view('parts/_mypage_sidebar_view'); ?>
        <div class="main-panel">
            <?php $this->load->view('parts/_mypage_top_nav_view'); ?>
            <div class="content">
                <div class="select-area">
                    <label for="">対象年月</label>
                    <select name="" id="select_date"></select>
                    <!-- <a href="" class="btn disabled">表示する</a> -->
                    <button id="pdf" class="btn red disabled">ダウンロード</button>
                </div>
                <div id="pay_view" class="pay-detail-view"></div>
            </div>
        </div>
    </div>
    <script>
        const userId = <?= (int)$user_id ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js2/mypage_pay.js?<?= time() ?>"></script>
</body>
</html>