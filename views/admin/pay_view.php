<?php
defined('BASEPATH') or exit('No direct script access allowed');
// admin pay view
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
    <div class="container">
        <div class="header">
            <?php
            $this->load->view('parts/_header_top_view');
            $this->load->view('parts/_header_admin_menu_view');
            ?>
        </div>
        <!-- 管理画面　表題部分 -->
        <div class="main-title-area">
            <div class="title"><?= $page_title ?><span>/ 従業員の給与管理をおこないます</div>
            <div class="btn-area">
                <!-- <div class="btn-text"><i class="far fa-sticky-note"></i> ファイル出力</div>
                <div class="row">
                    <div id="download_btn_excel" class="btn green"><i class="far fa-file-excel"></i> エクセル</div>
                    <div id="download_btn_pdf" class="btn red"><i class="far fa-file-pdf"></i> PDF</div>
                </div> -->
                <input type="file" name="" id="pay_file_input" class="pay-file-input" accept=".csv">
                <button id="pay_file_upload_btn" class="btn2">給与ファイル読み込み</button>
            </div>
        </div>

        <div class="select-item-area">
            <div class="select-item-left">
                <div class="select-tab">
                    <button class="btn2">給与明細</button>
                    <button class="btn2 disabled">有休</button>
                </div>
                <div class="select-date">
                    <select name="" id="select_date"></select>
                </div>
                <div class="select-date">
                    <select name="" id="select_name"></select>
                </div>
                <div class="select-date">
                    <select name="" id="select_open">
                        <option value="">公開選択</option>
                        <option value="1">公開中</option>
                        <option value="0">非公開</option>
                    </select>
                </div>
            </div>
            <div class="select-item-right"></div>
        </div>
        <div id="table"></div>
    </div>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js2/admin_pay.js?<?= time() ?>"></script>
</body>
</html>