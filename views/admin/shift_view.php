<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// admin shift view

// メタ部　読み込み
$this->load->view('parts/_head_view');
?>
<body>
    <div id="loader">
        <div class="loader-text">ファイル読み込み中</div>
        <div class="loader"></div>
    </div>
    <div class="container">
        <div class="header">
            <?php
            // ヘッダー部上部　読み込み
            $this->load->view('parts/_header_top_view');
            // ヘッダーメニュー　読み込み
            $this->load->view('parts/_header_admin_menu_view');
            ?>
        </div>
        <div class="main-title-area">
            <div class="title"><?= $page_title ?><span>/ 従業員のシフト管理をおこないます</div>
            <div class="btn-area">
                <div class="btn-text"><i class="far fa-sticky-note"></i> シフト一括登録</div>
                <div class="row">
                    <div id="csv_download_btn" class="btn green"><i class="fas fa-file-download"></i> 登録用ファイル出力</div>
                    <div class="btn red up-file"><i class="fas fa-caret-square-up"></i> 登録<input id="csv_upload_btn" type="file" multiple accept=".xlsx, .xls, .csv"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="date-area">
        <div class="inner">
            <div id="date-area-wareki"></div>
            <input id="month" class="month" value="">
            <div id="this_month_mark" class="this-month-mark">今月</div>
            <div class="user-area">
                <div id="user_kana" class="user-kana"></div>
                <div class="user-data">
                    <span class="user-name" id="user_name"></span>
                    <span id="user_id"></span>
                    <span id="group1_name"></span>
                    <span id="group2_name"></span>
                    <span id="group3_name"></span>
                </div>
            </div>
        </div>
        <div class="inner">
            <div class="bloc">
                <div id="this_month" class="date-btn disable">今月のシフト</div>
            </div>
            <div class="bloc">
                <div id="less_month" class="date-btn less-btn">戻る</div>
            </div>
            <div class="bloc">
                <div id="add_month" class="date-btn add-btn">次へ</div>
            </div>
        </div>
        <div class="term-area"><i class="fas fa-calendar-alt"></i> <span id="to_from_date"></span></div>
        <div id="user_time_data" class="user-data-area"></div>
    </div>
    <div class="main">
        <div class="shift-area">
            <div id="users_table_area" class="user-tabel-area">
                <div class="table-title-area">
                    <div class="table-title"><i class="fas fa-user"></i> 従業員一覧</div>
                    <div id="users_table_btn" class="table-top-mark">詳細表示</div>
                </div>
                <div id="users_table" class="table-area"></div>
                <div id="user_select_disable" class="table-top-mark disabled" style="margin-top: 10px;">従業員 選択解除</div>
                <div class="group-select-area">
                    <div class="table-title"><i class="fas fa-filter"></i> 抽出</div>
                    <?php for ($i=0; $i<3; $i++): ?>
                        <?php if (isset($group_title[$i])): ?>
                            <?php $var = $i + 1; ?>
                            <div class="select-row">
                                <label for="select_group_<?= $var ?>"><?= $group_title[$i]->title ?></label>
                                <select name="select_group_<?= $var ?>" id="select_group_<?= $var ?>">
                                    <option value="ALL">ALL</option>
                                    <?php foreach ($group[$var] as $value): ?>
                                        <option value="<?= $value->group_name ?>"><?= $value->group_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <?php if ((int)$auto_shift_flag->value === 0): ?>
                <div class="shift-select">
                    <div class="select-row">
                        <label for="shift_status">登録状況：</label>
                        <select name="" id="shift_status">
                            <option value="ALL">ALL</option>
                            <option value="0">未登録</option>
                            <option value="1">登録途中</option>
                            <option value="2">登録済み</option>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="shift-table-area">
                <div class="shift-table-header">
                    <div id="shift_view_title" class="table-title">
                        <?php if ((int)$shift_view_flag->value === 0): ?>
                            <i class="fas fa-list"></i> リスト
                        <?php endif; ?>
                        <?php if ((int)$shift_view_flag->value === 1): ?>
                            <i class="far fa-calendar"></i> カレンダー
                        <?php endif; ?>
                    </div>
                    <div id="shift_view_change" class="table-top-mark"><i class="fas fa-exchange-alt"></i></div>
                    <?php if ((int)$this->session->authority > 2): ?>
                        <div id="register_submit_btn" class="table-top-mark disabled">申請を反映</div>
                    <?php endif; ?>
                </div>
                <div id="shift_table" class="table-area"></div>
            </div>
        </div>
    </div>
    <?php
    // 時間修正用モーダル
    $this->load->view('parts/_modal_time_edit_view');
    // javascript　読み込み
    $this->load->view('parts/_javascript_view');
    ?>
</body>
</html>
