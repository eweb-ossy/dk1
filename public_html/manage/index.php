<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>打刻keeperシステム管理</title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.min.css">
    <style>
        body {margin: 0;padding: 1vw;width: 100%;}
        h1, h2 {font-weight: 300;line-height: 1;margin: 0;}
        h1 {font-size: 18px;}
        h2 {font-size: 16px;}
        p {font-size: 16px;font-weight: 300;line-height: 1.5;margin: 0;}
        .text-s {font-size: 9px;}
        .bloc {margin-bottom: 20px;}
        .bloc:last-child {margin: 0;}
        .row {margin: 20px 0;}
        .row:last-child {margin-bottom: 0;}
        .field {display: inline-block;margin-right: 10px;}
        .field:last-child {margin: 0;}
        .long {width: 350px;}
        .short {width: 100px;}
    </style>
</head>
<body>
    <div class="bloc">
        <h1>打刻keeperシステム管理</h1>
        <p class="text-s">version: 20211020</p>
    </div>
    <div class="bloc">
        <h2>請求書発行</h2>
        <form action="output.php">
            <div class="row">
                <div class="field">
                    <label for="select_date">利用月</label>
                    <select id="select_date" class="select">
                        <?php 
                        $now = new DateTimeImmutable();
                        for ($i=1; $i <= 12; $i++) {
                            $month = $now->sub(DateInterval::createFromDateString($i.' month'));
                            echo '<option value="'.$month->format('Ym').'">'.$month->format('Y年m月分').'</option>';
                        } 
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label for="select_company">請求先</label>
                    <select id="select_company" class="select">
                        <option value="">選択して下さい</option>
                        <?php 
                        $db = 'eweb_dakoku';
                        require('../../db.php'); // 本番
                        // require_once(dirname(__FILE__) . '/db.php'); // ローカル
                        $sql = 'SELECT id, company_name FROM global_data WHERE bill = 1';
                        foreach ($pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
                            echo '<option value="'.$row['id'].'">'.$row['company_name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <p id="note" style="color:red;font-size:12px;">注意事項がここに表示されます。</p>
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label for="field_no">NO</label>
                    <input id="field_no" class="field-value" name="field_no" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_outputdate">発行日</label>
                    <input id="field_outputdate" class="field-value" name="field_outputdate" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_limitdate">お振込期限</label>
                    <input id="field_limitdate" class="field-value" name="field_limitdate" type="text" value="">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label for="field_to">宛名</label>
                    <input id="field_to" class="field-value long" name="field_to" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_title">タイトル</label>
                    <input id="field_title" class="field-value long" name="field_title" type="text" value="">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label for="field_item1">項目１</label>
                    <input id="field_item1" class="field-value long" name="field_item1" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_item_detail1">項目説明１</label>
                    <input id="field_item_detail1" class="field-value long" name="field_item_detail1" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_price1">単価</label>
                    <input id="field_price1" class="field-value short" name="field_price1" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_num1">数量</label>
                    <input id="field_num1" class="field-value short" name="field_num1" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_unit1">単位</label>
                    <input id="field_unit1" class="field-value short" name="field_unit1" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_item_price1">金額</label>
                    <input id="field_item_price1" class="field-value short" name="field_item_price1" type="text" value="">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label for="field_item2">項目２</label>
                    <input id="field_item2" class="field-value long" name="field_item2" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_item_detail2">項目説明２</label>
                    <input id="field_item_detail2" class="field-value long" name="field_item_detail2" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_price2">単価</label>
                    <input id="field_price2" class="field-value short" name="field_price2" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_num2">数量</label>
                    <input id="field_num2" class="field-value short" name="field_num2" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_unit2">単位</label>
                    <input id="field_unit2" class="field-value short" name="field_unit2" type="text" value="">
                </div>
                <div class="field">
                    <label for="field_item_price2">金額</label>
                    <input id="field_item_price2" class="field-value short" name="field_item_price2" type="text" value="">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label for="field_price">小計</label>
                    <input id="field_price" class="field-value" name="field_price" type="text">
                </div>
                <div class="field">
                    <label for="field_tax">消費税</label>
                    <input id="field_tax" class="field-value" name="field_tax" type="text">
                </div>
                <div class="field">
                    <label for="field_total_price">ご請求金額（消費税込）</label>
                    <input id="field_total_price" class="field-value" name="field_total_price" type="text">
                </div>
            </div>
            <div class="row">
                <button type="submit">請求書作成</button>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="script.js?2012102501"></script>
</body>
</html>