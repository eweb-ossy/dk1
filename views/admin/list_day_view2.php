<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>css/admin_list_day2.min.css?<?= time() ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <?php
            $this->load->view('parts/_header_top_view');
            $this->load->view('parts/_header_admin_menu_view');
            ?>
        </div>
        <div class="main-title-area">
            <div class="title"><?= $page_title ?><span>/ 日別にて勤務情報を表示します。</span></div>
            <div class="btn-area">
                <div class="btn-text"><i class="far fa-sticky-note"></i> ファイル出力</div>
                <div class="row">
                    <div id="download_btn_excel" class="btn green"><i class="far fa-file-excel"></i> エクセル</div>
                    <div id="download_btn_pdf" class="btn red"><i class="far fa-file-pdf"></i> PDF</div>
                </div>
            </div>
        </div>
        <div class="date-area">
            <div class="inner">
                <div id="date-area-wareki"></div>
                <input id="datepicker" class="date">
                <div id="today_mark" class="today-mark">本日</div>
            </div>
            <div class="inner">
                <div class="bloc">
                    <div id="date_today" class="date-btn disable">本日の集計</div>
                </div>
                <div class="bloc">
                    <div id="less_day" class="date-btn less-btn">戻る</div>
                </div>
                <div class="bloc">
                    <div id="add_day" class="date-btn add-btn">次へ</div>
                </div>
                <div id="table_window_btn" class="table-top-mark">詳細表示</div>
            </div>
            <div class="main">
                <div id="data_table" class="table-area"></div>
            </div>
        </div>
    </div>

    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js2/admin_list_day.js?<?= time() ?>"></script>
</body>
</html>