<?php
defined('BASEPATH') or exit('No direct script access allowed');
// admin conf view

// メタ部　読み込み
$this->load->view('parts/_head_view');
?>

<body>
    <div class="container">
        <div class="header">
            <?php
            // ヘッダー部上部　読み込み
            $this->load->view('parts/_header_top_view');
            // ヘッダーメニュー　読み込み
            $this->load->view('parts/_header_admin_menu_view');
            ?>
        </div>
        <div class="main-title-area" style="height:62px;">
            <div class="title">
            <?= $page_title; ?><span>/ 各種設定をおこないます</div>
        </div>
    </div>
    <div class="main">
        <div class="side-menu">
            <ul>
                <li id="tab_01" class="tab active-menu"><i class="fas fa-chevron-right"></i> 基本設定</li>
                <li id="tab_02" class="tab"><i class="fas fa-chevron-right"></i> ルール設定</li>
                <li id="tab_03" class="tab"><i class="fas fa-chevron-right"></i> 出退勤設定</li>
                <li id="tab_04" class="tab"><i class="fas fa-chevron-right"></i> グループ設定</li>
                <li id="tab_05" class="tab"><i class="fas fa-chevron-right"></i> ログインユーザー設定</li>
                <?php if ((int)$area_flag->value === 1): ?>
                <li id="tab_06" class="tab"><i class="fas fa-chevron-right"></i> エリア設定</li>
                <?php endif; ?>
                <?php if ($login_name === 'システム管理者' && $login_id === 'ossy'): ?>
                <li id="tab_07" class="tab"><i class="fas fa-chevron-right"></i> 連携設定（ベータ）</li>
                <?php endif; ?>
                <li id="tab_08" class="tab"><i class="fas fa-chevron-right"></i> マイページ設定</li>
                <li id="tab_09" class="tab"><i class="fas fa-chevron-right"></i> 通知設定</li>
                <li id="tab_10" class="tab"><i class="fas fa-chevron-right"></i> 申請設定</li>
                <li id="tab_11" class="tab"><i class="fas fa-chevron-right"></i> 各種データ出力</li>
                <li id="tab_12" class="tab"><i class="fas fa-chevron-right"></i> シフト設定</li>
                <?php if ($login_name === 'システム管理者' && $login_id === 'ossy'): ?>
                <li id="tab_13" class="tab"><i class="fas fa-chevron-right"></i> 就業規則（ベータ）</li>
                <?php endif; ?>
                <li id="tab_14" class="tab"><i class="fas fa-chevron-right"></i> メッセージ機能設定</li>
                <?php if ($login_name === 'システム管理者' && $login_id === 'ossy'): ?>
                <li id="tab_15" class="tab"><i class="fas fa-chevron-right"></i> データ登録（ベータ）</li>
                <?php endif; ?>
                <?php if ($login_name === 'システム管理者' && $login_id === 'ossy'): ?>
                <li id="tab_16" class="tab"><i class="fas fa-chevron-right"></i> 給与管理設定（ベータ）</li>
                <?php endif; ?>
                <?php if ($login_name === 'システム管理者' && $login_id === 'ossy'): ?>
                <li id="tab_17" class="tab"><i class="fas fa-chevron-right"></i> 従業員管理設定（ベータ）</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="contents">
            <!-- page 01 基本設定 -->
            <div id="page_01" class="tab-page active-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">基本設定</div>
                        <div id="submit_page_01" class="btn green disabled">保存</div>
                    </div>
                    <div class="content-main">
                        <p>
                            <label>システムID：<input type="text" value="<?= $system_id->value ?>" disabled></label>
                        </p>
                        <p>
                            <label>登録会社名：<input type="text" name="page01_company_name" class="field_page_01" style="width:250px;" value="<?= $company_name->value ?>" data-id="<?= (int)$company_name->id ?>"></label>
                        </p>
                        <p>
                            <label for="">従業員ID桁数：</label>
                            <select name="page01_id_size" class="field_page_01" data-id="<?= (int)$id_size->id ?>">
                                <option value="3" <?= (int)$id_size->value === 3 ? ' selected' : '' ; ?>>3桁</option>
                                <option value="4" <?= (int)$id_size->value === 4 ? ' selected' : '' ; ?>>4桁</option>
                                <option value="5" <?= (int)$id_size->value === 5 ? ' selected' : '' ; ?>>5桁</option>
                                <option value="6" <?= (int)$id_size->value === 6 ? ' selected' : '' ; ?>>6桁</option>
                                <option value="7" <?= (int)$id_size->value === 7 ? ' selected' : '' ; ?>>7桁</option>
                            </select>
                        </p>
                        <p>
                            <label for="">登録用ファイルタイプ：</label>
                            <select name="page01_filetype" class="field_page_01" data-id="<?= (int)$download_filetype->id ?>">
                                <option value="1" <?= (int)$download_filetype->value === 1 ? ' selected' : '' ; ?>>Excel 2007(Xlsx)</option>
                                <option value="2" <?= (int)$download_filetype->value === 2 ? ' selected' : '' ; ?>>Excel97-2003(xls)</option>
                                <option value="3" <?= (int)$download_filetype->value === 3 ? ' selected' : '' ; ?>>CSV</option>
                            </select>
                        </p>
                        <p>
                            <label for="">締め日設定：</label>
                            <select name="page01_end_day" class="field_page_01" data-id="<?= (int)$end_day->id ?>">
                                <option value="0" <?= (int)$end_day->value === 0 ? ' selected' : '' ; ?>>月末締め</option>
                                <?php for ($i = 5; $i < 27; $i++): ?>
                                <option value="<?= $i ?>" <?= (int)$end_day->value === $i ? ' selected' : '' ; ?>>
                                <?= $i ?>日締め</option>
                                <?php endfor; ?>
                            </select>
                        </p>
                        <p>残業時間表示：
                            <label><input type="radio" name="page01_over_time_flag" class="field_page_01" value="1" data-id="<?= (int)$over_time_view_flag->id ?>" <?= (int)$over_time_view_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                            <label><input type="radio" name="page01_over_time_flag" class="field_page_01" value="0" data-id="<?= (int)$over_time_view_flag->id ?>" <?= (int)$over_time_view_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                        <p>深夜時間表示：
                            <label><input type="radio" name="page01_night_time_flag" class="field_page_01" value="1" data-id="<?= (int)$night_time_view_flag->id ?>" <?= (int)$night_time_view_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                            <label><input type="radio" name="page01_night_time_flag" class="field_page_01" value="0" data-id="<?= (int)$night_time_view_flag->id ?>" <?= (int)$night_time_view_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                        <p>通常時間表示：
                            <label><input type="radio" name="page01_normal_time_flag" class="field_page_01" value="1" data-id="<?= (int)$normal_time_flag->id ?>" <?= (int)$normal_time_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                            <label><input type="radio" name="page01_normal_time_flag" class="field_page_01" value="0" data-id="<?= (int)$normal_time_flag->id ?>" <?= (int)$normal_time_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                        <p>分 表示：
                            <label><input type="radio" name="page01_minute_time_flag" class="field_page_01" value="0" data-id="<?= (int)$minute_time_flag->id ?>" <?= (int)$minute_time_flag->value === 0 ? ' checked' : '' ; ?>>しない　</label>
                            <label><input type="radio" name="page01_minute_time_flag" class="field_page_01" value="1" data-id="<?= (int)$minute_time_flag->id ?>" <?= (int)$minute_time_flag->value === 1 ? ' checked' : '' ; ?>>する（時間 分）</label>
                            <label><input type="radio" name="page01_minute_time_flag" class="field_page_01" value="2" data-id="<?= (int)$minute_time_flag->id ?>" <?= (int)$minute_time_flag->value === 2 ? ' checked' : '' ; ?>>する（分 のみ）</label>
                        </p>
                        <p>出退勤入力エリア設定：
                            <label><input type="radio" name="page01_area_flag" class="field_page_01" value="1" data-id="<?= (int)$area_flag->id ?>" <?= (int)$area_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                            <label><input type="radio" name="page01_area_flag" class="field_page_01" value="0" data-id="<?= (int)$area_flag->id ?>" <?= (int)$area_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                        <p>位置情報（GPS）の取得設定：
                            <label><input type="radio" name="page01_gps_flag" class="field_page_01" value="0" data-id="<?= (int)$gps_flag->id ?>" <?= (int)$gps_flag->value === 0 ? ' checked' : '' ; ?>>取得しない　</label>
                            <label><input type="radio" name="page01_gps_flag" class="field_page_01" value="1" data-id="<?= (int)$gps_flag->id ?>" <?= (int)$gps_flag->value === 1 ? ' checked' : '' ; ?>>取得する</label>
                            <label><input type="radio" name="page01_gps_flag" class="field_page_01" value="2" data-id="<?= (int)$gps_flag->id ?>" <?= (int)$gps_flag->value === 2 ? ' checked' : '' ; ?>>PC以外取得する</label>
                        </p>
                        <p style="margin-top:30px;">
                            <label for="">時刻修正設定 出勤時刻：</label>
                            <span>
                            <input class="field_page_01" style="width:50px;" type="text" name="edit_in_time_h" data-id="<?= (int)$edit_in_time->id ?>" value="<?= substr($edit_in_time->value, 0, 2) ?>">時
                            <input class="field_page_01" style="width:50px;" type="text" name="edit_in_time_m" value="<?= substr($edit_in_time->value, 3, 2) ?>">分
                            </span>
                        </p>
                        <p>
                            <label for="">時刻修正設定 退勤時刻：</label>
                            <span>
                            <input class="field_page_01" style="width:50px;" type="text" name="edit_out_time_h" data-id="<?= (int)$edit_out_time->id ?>" value="<?= substr($edit_out_time->value, 0, 2) ?>">時
                            <input class="field_page_01" style="width:50px;" type="text" name="edit_out_time_m" value="<?= substr($edit_out_time->value, 3, 2) ?>">分
                            </span>
                        </p>
                        <p>
                            <label for="">時刻修正設定 分単位：</label>
                            <select name="edit_min" class="field_page_01" data-id="<?= (int)$edit_min->id ?>">
                                <option value="1" <?= (int)$edit_min->value === 1 ? ' selected' : '' ; ?>>1分</option>
                                <option value="5" <?= (int)$edit_min->value === 5 ? ' selected' : '' ; ?>>5分</option>
                                <option value="15" <?= (int)$edit_min->value === 15 ? ' selected' : '' ; ?>>15分</option>
                                <option value="30" <?= (int)$edit_min->value === 30 ? ' selected' : '' ; ?>>30分</option>
                            </select>
                        </p>
                    </div>
                </form>
            </div>
            <!-- page 02 ルール設定 -->
            <div id="page_02" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">ルール設定</div>
                        <div id="submit_page_02" class="btn green disabled">保存</div>
                        <div id="new_rule_page_02" class="btn red">新規ルール作成</div>
                        <div id="window_open" class="click-text" style="margin-left:80px;"><i class="far fa-window-maximize"></i> 全て開く</div>
                        <div id="window_close" class="click-text"><i class="fas fa-window-minimize"></i> 全て閉じる</div>
                    </div>
                    <script>var rule_num = {};</script>
                    <script>var rule_type_data = {};</script>
                    <ul id="rules_list" class="content-main connectedSortable">
                        <?php if (!$rules_data) {
                            echo '<span class="rule-none-text">定義されたルールはありません。</span>';
                        } ?>
                        <?php foreach ($rules_data as $key => $value): ?>
                        <li id="rule_<?= $value->id ?>" class="rule-block">
                            <table class="rule-table">
                                <tbody>
                                    <tr>
                                        <td class="rule-title" colspan="2">
                                            <div class="rule-title-input">
                                                <i class="fas fa-sort rule-sort-btn"></i>
                                                <input type="text" name="rule_title_<?= $value->id ?>" class="field_page_02" placeholder="ルールタイトル" value="<?= $value->title ?>">
                                                <div id="rule_type_summary_<?= $value->id ?>" class="rule-sub-text">
                                                    適応：
                                                    <?php
                                                    $rule_type = '';
                                                    if ($value->all_flag == 1) {
                                                        echo '全体';
                                                        $rule_type = 'all';
                                                    }
                                                    if ($value->group_id) {
                                                        echo 'グループ : ';
                                                        $id = '';
                                                        $no = '';
                                                        foreach ($group_title as $title) {
                                                            if ($title->group_id == $value->group_id) {
                                                                echo $title->title .' ';
                                                                $id = $title->group_id;
                                                            }
                                                        }
                                                        foreach ($group[$id] as $name) {
                                                            if ($name->id == $value->group_no) {
                                                                echo $name->group_name;
                                                                $no = $name->id;
                                                            }
                                                        }
                                                        $rule_type = 'group-'.$id.'-'.$no;
                                                    }
                                                    if ($value->user_id) {
                                                        foreach ($user_name_list as $user) {
                                                            if ($user->user_id == $value->user_id) {
                                                                echo '個人 : ';
                                                                echo $user->user_id.' ';
                                                                echo $user->name_sei.' '.$user->name_mei;
                                                                $user_id = $user->user_id;
                                                            }
                                                        }
                                                        $rule_type = 'user-'.$user_id;
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <span><i class="fas fa-bars rule-window-btn" data-ruleid="<?= $value->id ?>"></i></span>
                                            <!-- <div class="rule-copy-btn btn green" data-ruleid="<?= $value->id ?>">複製</div> -->
                                            <div class="rule-del-btn btn red" data-ruleid="<?= $value->id ?>">削除</div>
                                        </td>
                                    </tr>
                                    <!-- 適応エリア -->
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <th colspan="2">適応</th>
                                    </tr>
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <td colspan="2">
                                            <label>
                                                <input type="radio" name="rule_type_<?= $value->id ?>" class="field_page_02 rule_type" value="1" data-ruleid="<?= $value->id ?>" <?= $value->all_flag == 1 ? ' checked' : '' ?>>全体　
                                            </label>
                                            <label>
                                                <input type="radio" name="rule_type_<?= $value->id ?>" class="field_page_02 rule_type" value="2" data-ruleid="<?= $value->id ?>" <?= $value->group_id ? ' checked' : '' ?>>グループ　
                                            </label>
                                            <label>
                                                <input type="radio" name="rule_type_<?= $value->id ?>" class="field_page_02 rule_type" value="3" data-ruleid="<?= $value->id ?>" <?= $value->user_id ? ' checked' : '' ?>>個人　
                                            </label>
                                            <!-- 適応がグループの場合 -->
                                            <div id="rule_type_2_<?= $value->id ?>" class="rule_type_option_<?= $value->id ?><?= $value->group_id ? '' : ' rule-hide' ?>">
                                                <?php $i = 1; ?>
                                                <?php foreach ($group_title as $title): ?>
                                                    <input type="radio" name="rule_group_title_<?= $value->id ?>" style="margin-left:1em;" class="field_page_02 rule_type_group" value="<?= $title->group_id ?>" data-ruleid="<?= $value->id ?>"  data-group-id="<?= $i ?>"<?= $title->group_id == $value->group_id ? ' checked' : '' ?>>
                                                    <label id="group_title_<?= $i ?>"><?php echo $title->title; ?></label>
                                                    <select name="rule_group_id_<?= $value->id ?>_<?= $title->group_id ?>" class="field_page_02 group-select rule_group_id_<?= $value->id ?>_<?= $title->group_id ?>" data-ruleid="<?= $value->id ?>" data-group-id="<?= $i ?>"<?= $title->group_id == $value->group_id ? '' : ' disabled' ?>>
                                                    <?php foreach ($group[$i] as $name): ?>
                                                        <?php if ($name->state == 1): ?>
                                                            <option value="<?= $name->id ?>"<?= $title->group_id == $value->group_id && $name->id == $value->group_no ? ' selected' : '' ?>><?= $name->group_name ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                    </select>
                                                    <?php $i++; ?>
                                                <?php endforeach; ?>
                                            </div>
                                            <!-- 適応が個人の場合 -->
                                            <div id="rule_type_3_<?= $value->id ?>" class="rule_type_option_<?= $value->id ?><?= $value->user_id ? '' : ' rule-hide' ?>">
                                                <label>対象従業員名：</label>
                                                <select class="field_page_02 user-select" name="rule_user_id_<?= $value->id ?>" data-ruleid="<?= $value->id ?>">
                                                    <?php foreach ($user_name_list as $user): ?>
                                                        <option value="<?= $user->user_id ?>"<?= $user->user_id == $value->user_id ? ' selected' : '' ?>><?= sprintf('%0'.$id_size->value.'d', $user->user_id) ?>　<?= $user->name_sei ?> <?= $user->name_mei ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 出退勤ルールエリア -->
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <th>出勤ルール</th>
                                        <th>退勤ルール</th>
                                    </tr>
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <!-- 出勤ルール -->
                                        <td>
                                            <label>
                                                <input type="radio" name="in_marume_flag_<?= $value->id ?>" class="field_page_02 in_marume_flag" value="0" data-ruleid="<?= $value->id ?>"<?= $value->in_marume_flag == 0 ? ' checked' : '' ?>>なし　
                                            </label>
                                            <label>
                                                <input type="radio" name="in_marume_flag_<?= $value->id ?>" class="field_page_02 in_marume_flag" value="1" data-ruleid="<?= $value->id ?>"<?= $value->in_marume_flag == 1 ? ' checked' : '' ?>>まるめ　
                                            </label>
                                            <label>
                                                <input type="radio" name="in_marume_flag_<?= $value->id ?>" class="field_page_02 in_marume_flag" value="2" data-ruleid="<?= $value->id ?>"<?= $value->in_marume_flag == 2 ? ' checked' : '' ?>>時刻合せ　
                                            </label>
                                            <label>
                                                <input type="radio" name="in_marume_flag_<?= $value->id ?>" class="field_page_02 in_marume_flag" value="3" data-ruleid="<?= $value->id ?>"<?= $value->in_marume_flag == 3 ? ' checked' : '' ?>>時刻合せ＋まるめ　
                                            </label><br><br>
                                            <label>
                                                <input type="radio" name="in_marume_flag_<?= $value->id ?>" class="field_page_02 in_marume_flag" value="4" data-ruleid="<?= $value->id ?>"<?= $value->in_marume_flag == 4 ? ' checked' : '' ?>>シフト合せ　
                                            </label>
                                            <label>
                                                <input type="radio" name="in_marume_flag_<?= $value->id ?>" class="field_page_02 in_marume_flag" value="5" data-ruleid="<?= $value->id ?>"<?= $value->in_marume_flag == 5 ? ' checked' : '' ?>>シフト合せ＋まるめ　
                                            </label>
                                            <!-- 出勤　まるめ -->
                                            <div id="in_marume_1_<?= $value->id ?>" class="rule_in_marume_option_<?= $value->id ?><?= $value->in_marume_flag == 1 ? '' : ' rule-hide' ?>">
                                                <label for="">まるめ時間（分）：</label>
                                                <select class="field_page_02" name="in_marume1_hour_<?= $value->id ?>" class="field_page_02">
                                                    <option value="5"<?= $value->in_marume_hour == 5 ? ' selected' : '' ?>>5分</option>
                                                    <option value="15"<?= $value->in_marume_hour == 15 ? ' selected' : '' ?>>15分</option>
                                                    <option value="30"<?= $value->in_marume_hour == 30 ? ' selected' : '' ?>>30分</option>
                                                </select>
                                            </div>
                                            <!-- 出勤　時刻合わせ -->
                                            <div id="in_marume_2_<?= $value->id ?>" class="rule_in_marume_option_<?= $value->id ?><?= $value->in_marume_flag == 2 ? '' : ' rule-hide' ?>">
                                                <label for="">出勤合わせ時刻：</label>
                                                <span>
                                                    <input class="field_page_02 rule-time-input" type="text" name="in_marume2_h_<?= $value->id ?>" value="<?= substr($value->in_marume_time, 0, 2) ?>">時
                                                    <input class="field_page_02 rule-time-input" type="text" name="in_marume2_m_<?= $value->id ?>" value="<?= substr($value->in_marume_time, 3, 2) ?>">分
                                                </span>
                                            </div>
                                            <!-- 出勤　時刻合せ＋まるめ -->
                                            <div id="in_marume_3_<?= $value->id ?>" class="rule_in_marume_option_<?= $value->id ?><?= $value->in_marume_flag == 3 ? '' : ' rule-hide' ?>">
                                                <label for="">出勤合わせ時刻：</label>
                                                <span>
                                                    <input class="field_page_02 rule-time-input" type="text" name="in_marume3_h_<?= $value->id ?>" value="<?= substr($value->in_marume_time, 0, 2) ?>">時
                                                    <input class="field_page_02 rule-time-input" type="text" name="in_marume3_m_<?= $value->id ?>" value="<?= substr($value->in_marume_time, 3, 2) ?>">分
                                                </span>
                                                <label for="">まるめ時間（分）：</label>
                                                <select class="field_page_02" name="in_marume3_hour_<?= $value->id ?>" class="field_page_02">
                                                    <option value="5"<?= $value->in_marume_hour == 5 ? ' selected' : '' ?>>5分</option>
                                                    <option value="15"<?= $value->in_marume_hour == 15 ? ' selected' : '' ?>>15分</option>
                                                    <option value="30"<?= $value->in_marume_hour == 30 ? ' selected' : '' ?>>30分</option>
                                                </select>
                                            </div>
                                            <!-- 出勤　シフト合せ＋まるめ -->
                                            <div id="in_marume_5_<?= $value->id ?>" class="rule_in_marume_option_<?= $value->id ?><?= $value->in_marume_flag == 5 ? '' : ' rule-hide' ?>">
                                                <label for="">シフト合わせ</label>
                                                <label for="">まるめ時間（分）：</label>
                                                <select class="field_page_02" name="in_marume5_hour_<?= $value->id ?>" class="field_page_02">
                                                    <option value="5"<?= $value->in_marume_hour == 5 ? ' selected' : '' ?>>5分</option>
                                                    <option value="15"<?= $value->in_marume_hour == 15 ? ' selected' : '' ?>>15分</option>
                                                    <option value="30"<?= $value->in_marume_hour == 30 ? ' selected' : '' ?>>30分</option>
                                                </select>
                                            </div>
                                        </td>
                                        <!-- 退勤ルール -->
                                        <td>
                                            <label>
                                                <input type="radio" name="out_marume_flag_<?= $value->id ?>" class="field_page_02 out_marume_flag" value="0" data-ruleid="<?= $value->id ?>"<?= $value->out_marume_flag == 0 ? ' checked' : '' ?>>なし　
                                            </label>
                                            <label>
                                                <input type="radio" name="out_marume_flag_<?= $value->id ?>" class="field_page_02 out_marume_flag" value="1" data-ruleid="<?= $value->id ?>"<?= $value->out_marume_flag == 1 ? ' checked' : '' ?>>まるめ　
                                            </label>
                                            <label>
                                                <input type="radio" name="out_marume_flag_<?= $value->id ?>" class="field_page_02 out_marume_flag" value="2" data-ruleid="<?= $value->id ?>"<?= $value->out_marume_flag == 2 ? ' checked' : '' ?>>時刻合せ　
                                            </label>
                                            <label>
                                                <input type="radio" name="out_marume_flag_<?= $value->id ?>" class="field_page_02 out_marume_flag" value="3" data-ruleid="<?= $value->id ?>"<?= $value->out_marume_flag == 3 ? ' checked' : '' ?>>時刻合せ＋まるめ　
                                            </label><br><br>
                                            <label>
                                                <input type="radio" name="out_marume_flag_<?= $value->id ?>" class="field_page_02 out_marume_flag" value="4" data-ruleid="<?= $value->id ?>"<?= $value->out_marume_flag == 4 ? ' checked' : '' ?>>シフト合せ　
                                            </label>
                                            <label>
                                                <input type="radio" name="out_marume_flag_<?= $value->id ?>" class="field_page_02 out_marume_flag" value="5" data-ruleid="<?= $value->id ?>"<?= $value->out_marume_flag == 5 ? ' checked' : '' ?>>シフト合せ＋まるめ　
                                            </label><br><br>
                                            <label>
                                                <input type="radio" name="out_marume_flag_<?= $value->id ?>" class="field_page_02 out_marume_flag" value="6" data-ruleid="<?= $value->id ?>"<?= $value->out_marume_flag == 6 ? ' checked' : '' ?>>自動退勤＋定時退勤時刻　
                                            </label>
                                            <label>
                                                <input type="radio" name="out_marume_flag_<?= $value->id ?>" class="field_page_02 out_marume_flag" value="7" data-ruleid="<?= $value->id ?>"<?= $value->out_marume_flag == 7 ? ' checked' : '' ?>>自動退勤＋シフト退勤時刻　
                                            </label>
                                            <!-- 退勤　まるめ -->
                                            <div id="out_marume_1_<?= $value->id ?>" class="rule_out_marume_option_<?= $value->id ?><?= $value->out_marume_flag == 1 ? '' : ' rule-hide' ?>">
                                                <label for="">まるめ時間（分）：</label>
                                                <select class="field_page_02" name="out_marume1_hour_<?= $value->id ?>" class="field_page_02">
                                                    <option value="5"<?= $value->out_marume_hour == 5 ? ' selected' : '' ?>>5分</option>
                                                    <option value="15"<?= $value->out_marume_hour == 15 ? ' selected' : '' ?>>15分</option>
                                                    <option value="30"<?= $value->out_marume_hour == 30 ? ' selected' : '' ?>>30分</option>
                                                </select>
                                            </div>
                                            <!-- 退勤　時刻合わせ -->
                                            <div id="out_marume_2_<?= $value->id ?>" class="rule_out_marume_option_<?= $value->id ?><?= $value->out_marume_flag == 2 ? '' : ' rule-hide' ?>">
                                                <label for="">出勤合わせ時刻：</label>
                                                <span>
                                                    <input class="field_page_02 rule-time-input" type="text" name="out_marume2_h_<?= $value->id ?>" value="<?= substr($value->out_marume_time, 0, 2) ?>">時
                                                    <input class="field_page_02 rule-time-input" type="text" name="out_marume2_m_<?= $value->id ?>" value="<?= substr($value->out_marume_time, 3, 2) ?>">分
                                                </span>
                                            </div>
                                            <!-- 退勤　時刻合せ＋まるめ -->
                                            <div id="out_marume_3_<?= $value->id ?>" class="rule_out_marume_option_<?= $value->id ?><?= $value->out_marume_flag == 3 ? '' : ' rule-hide' ?>">
                                                <label for="">出勤合わせ時刻：</label>
                                                <span>
                                                    <input class="field_page_02 rule-time-input" type="text" name="out_marume3_h_<?= $value->id ?>" value="<?= substr($value->out_marume_time, 0, 2) ?>">時
                                                    <input class="field_page_02 rule-time-input" type="text" name="out_marume3_m_<?= $value->id ?>" value="<?= substr($value->out_marume_time, 3, 2) ?>">分
                                                </span>
                                                <label for="">まるめ時間（分）：</label>
                                                <select class="field_page_02" name="out_marume3_hour_<?= $value->id ?>" class="field_page_02">
                                                    <option value="5"<?= $value->out_marume_hour == 5 ? ' selected' : '' ?>>5分</option>
                                                    <option value="15"<?= $value->out_marume_hour == 15 ? ' selected' : '' ?>>15分</option>
                                                    <option value="30"<?= $value->out_marume_hour == 30 ? ' selected' : '' ?>>30分</option>
                                                </select>
                                            </div>
                                            <!-- 退勤　シフト合せ＋まるめ -->
                                            <div id="out_marume_5_<?= $value->id ?>" class="rule_out_marume_option_<?= $value->id ?><?= $value->out_marume_flag == 5 ? '' : ' rule-hide' ?>">
                                                <label for="">シフト合わせ</label>
                                                <label for="">まるめ時間（分）：</label>
                                                <select class="field_page_02" name="out_marume5_hour_<?= $value->id ?>" class="field_page_02">
                                                    <option value="5"<?= $value->out_marume_hour == 5 ? ' selected' : '' ?>>5分</option>
                                                    <option value="15"<?= $value->out_marume_hour == 15 ? ' selected' : '' ?>>15分</option>
                                                    <option value="30"<?= $value->out_marume_hour == 30 ? ' selected' : '' ?>>30分</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 定時　時刻エリア -->
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <th>定時　出勤時刻</th>
                                        <th>定時　退勤時刻</th>
                                    </tr>
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <td>
                                            <input type="radio" name="basic_in_<?= $value->id ?>" class="field_page_02 basic-in-flag" value="0" data-ruleid="<?= $value->id ?>"<?= !$value->basic_in_time ? ' checked' : '' ?>>しない　
                                            <input type="radio" name="basic_in_<?= $value->id ?>" class="field_page_02 basic-in-flag" value="1" data-ruleid="<?= $value->id ?>"<?= $value->basic_in_time ? ' checked' : '' ?>>
                                            <span>
                                                <input class="field_page_02 rule-time-input" type="text" name="in_basic_h_<?= $value->id ?>" class="field_page_02" value="<?= substr($value->basic_in_time, 0, 2) ?>"<?= $value->basic_in_time ? '' : ' disabled' ?>>時
                                                <input class="field_page_02 rule-time-input" type="text" name="in_basic_m_<?= $value->id ?>" class="field_page_02" value="<?= substr($value->basic_in_time, 3, 2) ?>"<?= $value->basic_in_time ? '' : ' disabled' ?>>分
                                            </span>
                                        </td>
                                        <td>
                                            <input type="radio" name="basic_out_<?= $value->id ?>" value="0" class="field_page_02 basic-out-flag" data-ruleid="<?= $value->id ?>"<?= !$value->basic_out_time ? ' checked' : '' ?>>しない　
                                            <input type="radio" name="basic_out_<?= $value->id ?>" value="1" class="field_page_02 basic-out-flag" data-ruleid="<?= $value->id ?>"<?= $value->basic_out_time ? ' checked' : '' ?>>
                                            <span>
                                                <input class="field_page_02 rule-time-input" type="text" name="out_basic_h_<?= $value->id ?>" value="<?= substr($value->basic_out_time, 0, 2) ?>"<?= $value->basic_out_time ? '' : ' disabled' ?>>時
                                                <input class="field_page_02 rule-time-input" type="text" name="out_basic_m_<?= $value->id ?>" value="<?= substr($value->basic_out_time, 3, 2) ?>"<?= $value->basic_out_time ? '' : ' disabled' ?>>分
                                            </span>
                                        </td>
                                    </tr>
                                    <!-- 定休　稼働時間　エリア -->
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <th>定休　曜日</th>
                                        <th>稼働時間（分）</th>
                                    </tr>
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <td>
                                            <label><input type="checkbox" name="basic_rest_weekday_<?= $value->id ?>" class="field_page_02" value="0"<?= substr($value->basic_rest_weekday, 0, 1) == 1 ? ' checked' : '' ?>>日　</label>
                                            <label><input type="checkbox" name="basic_rest_weekday_<?= $value->id ?>" class="field_page_02" value="1"<?= substr($value->basic_rest_weekday, 1, 1) == 1 ? ' checked' : '' ?>>月　</label>
                                            <label><input type="checkbox" name="basic_rest_weekday_<?= $value->id ?>" class="field_page_02" value="2"<?= substr($value->basic_rest_weekday, 2, 1) == 1 ? ' checked' : '' ?>>火　</label>
                                            <label><input type="checkbox" name="basic_rest_weekday_<?= $value->id ?>" class="field_page_02" value="3"<?= substr($value->basic_rest_weekday, 3, 1) == 1 ? ' checked' : '' ?>>水　</label>
                                            <label><input type="checkbox" name="basic_rest_weekday_<?= $value->id ?>" class="field_page_02" value="4"<?= substr($value->basic_rest_weekday, 4, 1) == 1 ? ' checked' : '' ?>>木　</label>
                                            <label><input type="checkbox" name="basic_rest_weekday_<?= $value->id ?>" class="field_page_02" value="5"<?= substr($value->basic_rest_weekday, 5, 1) == 1 ? ' checked' : '' ?>>金　</label>
                                            <label><input type="checkbox" name="basic_rest_weekday_<?= $value->id ?>" class="field_page_02" value="6"<?= substr($value->basic_rest_weekday, 6, 1) == 1 ? ' checked' : '' ?>>土　</label>
                                            <label><input type="checkbox" name="basic_rest_weekday_<?= $value->id ?>" class="field_page_02" value="7"<?= substr($value->basic_rest_weekday, 7, 1) == 1 ? ' checked' : '' ?>>祝　</label>
                                        </td>
                                        <td>
                                            <input type="text" name="over_limit_hour_<?= $value->id ?>" class="field_page_02 rule-time-input" value="<?= $value->over_limit_hour ?>">分
                                            <span class="rule-time-h">( <?= round((int)$value->over_limit_hour / 60, 2) ?> 時間 )</span>
                                        </td>
                                    </tr>
                                    <!-- 休憩　エリア -->
                                    <input type="hidden" name="rest_id_<?= $value->id ?>" value="<?= $rest_rules_data[$value->id]->id ?>">
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <th>自動休憩</th>
                                        <th>休憩定義</th>
                                    </tr>
                                    <tr class="rule-main rule_main_id_<?= $value->id ?>">
                                        <td>
                                            <input type="radio" name="rest_flag_<?= $value->id ?>" class="field_page_02 rest-flag" value="0" data-ruleid="<?= $value->id ?>"<?= $value->rest_rule_flag == 0 ? ' checked' : '' ?>>しない　
                                            <input type="radio" name="rest_flag_<?= $value->id ?>" class="field_page_02 rest-flag" value="1" data-ruleid="<?= $value->id ?>"<?= $value->rest_rule_flag == 1 ? ' checked' : '' ?>>する
                                        </td>
                                        <td>
                                            <div class="rest_content_<?= $value->id ?>" style="<?= $value->rest_rule_flag == 1 ? 'display:block' : 'display:none' ?>">
                                                <input type="radio" name="rest_type_<?= $value->id ?>" class="field_page_02 rest-type" value="1" data-ruleid="<?= $value->id ?>"<?= $rest_rules_data[$value->id]->rest_type == 1 ? ' checked' : '' ?>>時間適応　
                                                <input type="radio" name="rest_type_<?= $value->id ?>" class="field_page_02 rest-type" value="2" data-ruleid="<?= $value->id ?>"<?= $rest_rules_data[$value->id]->rest_type == 2 ? ' checked' : '' ?>>指定時刻
                                                <div id="rest_rule_type1_<?= $value->id ?>" class="rest-rule-type_<?= $value->id ?>" style="<?= $rest_rules_data[$value->id]->rest_type == 1 ? 'display:block' : 'display:none' ?>">
                                                    <span>
                                                        労働時間：
                                                        <input class="field_page_02 rule-time-input" type="text" name="rest_limit_work_<?= $value->id ?>" value="<?= $rest_rules_data[$value->id]->limit_work_hour ?>">分以上で
                                                        <input class="field_page_02 rule-time-input" type="text" name="rest_time_<?= $value->id ?>" value="<?= $rest_rules_data[$value->id]->rest_time ?>">分休憩
                                                    </span>
                                                </div>
                                                <div id="rest_rule_type2_<?= $value->id ?>" class="rest-rule-type_<?= $value->id ?>" style="<?= $rest_rules_data[$value->id]->rest_type == 2 ? 'display:block' : 'display:none' ?>">
                                                    <span>
                                                        <input class="field_page_02 rule-time-input" type="text" name="rest_in_time_h_<?= $value->id ?>" value="<?= substr($rest_rules_data[$value->id]->rest_in_time, 0, 2) ?>">時
                                                        <input class="field_page_02 rule-time-input" type="text" name="rest_in_time_m_<?= $value->id ?>" value="<?= substr($rest_rules_data[$value->id]->rest_in_time, 3, 2) ?>">分から
                                                        <input class="field_page_02 rule-time-input" type="text" name="rest_out_time_h_<?= $value->id ?>" value="<?= substr($rest_rules_data[$value->id]->rest_out_time, 0, 2) ?>">時
                                                        <input class="field_page_02 rule-time-input" type="text" name="rest_out_time_m_<?= $value->id ?>" value="<?= substr($rest_rules_data[$value->id]->rest_out_time, 3, 2) ?>">分までは休憩
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <script>
                            rule_num[<?= (int)$key ?>] = <?= (int)$value->id ?>;
                            rule_type_data[<?= (int)$value->id ?>] = '<?= $rule_type ?>';
                        </script>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </form>
                <script>
                    var new_rule_html = '
                        <li id="rule_new" class="rule-block">
                        <table class="rule-table new-table">
                        <tbody>
                        <tr>
                        <td class="rule-title" colspan="2">
                        <div class="rule-title-input">
                        <i class="fas fa-sort rule-sort-btn"></i>
                        <input type="text" name="rule_title_new" class="field_page_02" placeholder="ルールタイトル" value="">
                        <div id="rule_type_summary_new" class="rule-sub-text">
                        適応：全体
                        </div>
                        </div>
                        <span><i class="fas fa-bars rule-window-btn" data-ruleid="new"></i></span>
                        <div class="rule-new-del-btn btn red" data-ruleid="new">削除</div>
                        </td>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <th colspan="2">適応</th>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <td colspan="2">
                        <label>
                        <input type="radio" name="rule_type_new" class="field_page_02 rule_type" value="1" data-ruleid="new" checked>全体　
                        </label>
                        <label>
                        <input type="radio" name="rule_type_new" class="field_page_02 rule_type" value="2" data-ruleid="new">グループ　
                        </label>
                        <label>
                        <input type="radio" name="rule_type_new" class="field_page_02 rule_type" value="3" data-ruleid="new">個人　
                        </label>
                        <div id="rule_type_2_new" class="rule_type_option_new rule-hide">
                        <?php $i = 1; ?>
                        <?php foreach ($group_title as $title): ?>
                        <input type="radio" name="rule_group_title_new" style="margin-left:1em;" class="field_page_02 rule_type_group" value="<?= $title->group_id ?>" data-ruleid="new"  data-group-id="<?= $i ?>">
                        <label id="group_title_<?= $i ?>"><?= $title->title ?></label>
                        <select name="rule_group_id_new" class="field_page_02 group-select rule_group_id_new_<?= $title->group_id ?>" data-ruleid="new" data-group-id="<?= $i ?>" disabled>
                        <?php foreach ($group[$i] as $name): ?>
                        <option value="<?= $name->id ?>"<?= $name === reset($group[$i]) ? ' selected' : '' ?>><?= $name->group_name ?></option>
                        <?php endforeach; ?>
                        </select>
                        <?php $i++; ?>
                        <?php endforeach; ?>
                        </div>
                        <div id="rule_type_3_new" class="rule_type_option_new rule-hide">
                        <label>対象従業員名：</label>
                        <select class="field_page_02 user-select" name="rule_user_id_new" data-ruleid="new">
                        <?php foreach ($user_name_list as $user): ?>
                        <option value="<?= $user->user_id ?>"<?= $user === reset($user_name_list) ? ' selected' : '' ?>><?= sprintf('%0'.$id_size->value.'d', $user->user_id) ?>　<?= $user->name_sei ?> <?= $user->name_mei ?></option>
                        <?php endforeach; ?>
                        </select>
                        </div>
                        </td>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <th>出勤ルール</th>
                        <th>退勤ルール</th>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <td>
                        <label>
                        <input type="radio" name="in_marume_flag_new" class="field_page_02 in_marume_flag" value="0" data-ruleid="new" checked>なし　
                        </label>
                        <label>
                        <input type="radio" name="in_marume_flag_new" class="field_page_02 in_marume_flag" value="1" data-ruleid="new">まるめ　
                        </label>
                        <label>
                        <input type="radio" name="in_marume_flag_new" class="field_page_02 in_marume_flag" value="2" data-ruleid="new">時刻合せ　
                        </label>
                        <label>
                        <input type="radio" name="in_marume_flag_new" class="field_page_02 in_marume_flag" value="3" data-ruleid="new">時刻合せ＋まるめ　
                        </label><br><br>
                        <label>
                        <input type="radio" name="in_marume_flag_new" class="field_page_02 in_marume_flag" value="4" data-ruleid="new">シフト合せ　
                        </label>
                        <label>
                        <input type="radio" name="in_marume_flag_new" class="field_page_02 in_marume_flag" value="5" data-ruleid="new">シフト合せ＋まるめ　
                        </label>
                        <div id="in_marume_1_new" class="rule_in_marume_option_new rule-hide">
                        <label for="">まるめ時間（分）：</label>
                        <select class="field_page_02" name="in_marume1_hour_new" class="field_page_02">
                        <option value="5" selected>5分</option>
                        <option value="15">15分</option>
                        <option value="30">30分</option>
                        </select>
                        </div>
                        <div id="in_marume_2_new" class="rule_in_marume_option_new rule-hide">
                        <label for="">出勤合わせ時刻：</label>
                        <span>
                        <input class="field_page_02 rule-time-input" type="text" name="in_marume2_h_new" value="">時
                        <input class="field_page_02 rule-time-input" type="text" name="in_marume2_m_new" value="">分
                        </span>
                        </div>
                        <div id="in_marume_3_new" class="rule_in_marume_option_new rule-hide">
                        <label for="">出勤合わせ時刻：</label>
                        <span>
                        <input class="field_page_02 rule-time-input" type="text" name="in_marume3_h_new" value="">時
                        <input class="field_page_02 rule-time-input" type="text" name="in_marume3_m_new" value="">分
                        </span>
                        <label for="">まるめ時間（分）：</label>
                        <select class="field_page_02" name="in_marume3_hour_new" class="field_page_02">
                        <option value="5" selected>5分</option>
                        <option value="15">15分</option>
                        <option value="30">30分</option>
                        </select>
                        </div>
                        <div id="in_marume_5_new" class="rule_in_marume_option_new rule-hide">
                        <label for="">シフト合わせ</label>
                        <label for="">まるめ時間（分）：</label>
                        <select class="field_page_02" name="in_marume5_hour_new" class="field_page_02">
                        <option value="5" selected>5分</option>
                        <option value="15">15分</option>
                        <option value="30">30分</option>
                        </select>
                        </div>
                        </td>
                        <td>
                        <label>
                        <input type="radio" name="out_marume_flag_new" class="field_page_02 out_marume_flag" value="0" data-ruleid="new" checked>なし　
                        </label>
                        <label>
                        <input type="radio" name="out_marume_flag_new" class="field_page_02 out_marume_flag" value="1" data-ruleid="new">まるめ　
                        </label>
                        <label>
                        <input type="radio" name="out_marume_flag_new" class="field_page_02 out_marume_flag" value="2" data-ruleid="new">時刻合せ　
                        </label>
                        <label>
                        <input type="radio" name="out_marume_flag_new" class="field_page_02 out_marume_flag" value="3" data-ruleid="new">時刻合せ＋まるめ　
                        </label><br><br>
                        <label>
                        <input type="radio" name="out_marume_flag_new" class="field_page_02 out_marume_flag" value="4" data-ruleid="new">シフト合せ　
                        </label>
                        <label>
                        <input type="radio" name="out_marume_flag_new" class="field_page_02 out_marume_flag" value="5" data-ruleid="new">シフト合せ＋まるめ　
                        </label><br><br>
                        <label>
                        <input type="radio" name="out_marume_flag_new" class="field_page_02 out_marume_flag" value="6" data-ruleid="new">自動退勤＋定時退勤時刻　
                        </label>
                        <label>
                        <input type="radio" name="out_marume_flag_new" class="field_page_02 out_marume_flag" value="7" data-ruleid="new">自動退勤＋シフト退勤時刻　
                        </label>
                        <div id="out_marume_1_new" class="rule_out_marume_option_new rule-hide">
                        <label for="">まるめ時間（分）：</label>
                        <select class="field_page_02" name="out_marume1_hour_new" class="field_page_02">
                        <option value="5" selected>5分</option>
                        <option value="15">15分</option>
                        <option value="30">30分</option>
                        </select>
                        </div>
                        <div id="out_marume_2_new" class="rule_out_marume_option_new rule-hide">
                        <label for="">出勤合わせ時刻：</label>
                        <span>
                        <input class="field_page_02 rule-time-input" type="text" name="out_marume2_h_new" value="">時
                        <input class="field_page_02 rule-time-input" type="text" name="out_marume2_m_new" value="">分
                        </span>
                        </div>
                        <div id="out_marume_3_new" class="rule_out_marume_option_new rule-hide">
                        <label for="">出勤合わせ時刻：</label>
                        <span>
                        <input class="field_page_02 rule-time-input" type="text" name="out_marume3_h_new" value="">時
                        <input class="field_page_02 rule-time-input" type="text" name="out_marume3_m_new" value="">分
                        </span>
                        <label for="">まるめ時間（分）：</label>
                        <select class="field_page_02" name="out_marume3_hour_new" class="field_page_02">
                        <option value="5" selected>5分</option>
                        <option value="15">15分</option>
                        <option value="30">30分</option>
                        </select>
                        </div>
                        <div id="out_marume_5_new" class="rule_out_marume_option_new rule-hide">
                        <label for="">シフト合わせ</label>
                        <label for="">まるめ時間（分）：</label>
                        <select class="field_page_02" name="out_marume5_hour_new" class="field_page_02">
                        <option value="5" selected>5分</option>
                        <option value="15">15分</option>
                        <option value="30">30分</option>
                        </select>
                        </div>
                        </td>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <th>定時　出勤時刻</th>
                        <th>定時　退勤時刻</th>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <td>
                        <input type="radio" name="basic_in_new" class="field_page_02 basic-in-flag" value="0" data-ruleid="new" checked>しない　
                        <input type="radio" name="basic_in_new" class="field_page_02 basic-in-flag" value="1" data-ruleid="new">
                        <span>
                        <input class="field_page_02 rule-time-input" type="text" name="in_basic_h_new" class="field_page_02" value="" disabled>時
                        <input class="field_page_02 rule-time-input" type="text" name="in_basic_m_new" class="field_page_02" value="" disabled>分
                        </span>
                        </td>
                        <td>
                        <input type="radio" name="basic_out_new" value="0" class="field_page_02 basic-out-flag" data-ruleid="new" checked>しない　
                        <input type="radio" name="basic_out_new" value="1" class="field_page_02 basic-out-flag" data-ruleid="new">
                        <span>
                        <input class="field_page_02 rule-time-input" type="text" name="out_basic_h_new" value="" disabled>時
                        <input class="field_page_02 rule-time-input" type="text" name="out_basic_m_new" value="" disabled>分
                        </span>
                        </td>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <th>定休　曜日</th>
                        <th>稼働時間（分）</th>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <td>
                        <label><input type="checkbox" name="basic_rest_weekday_new" class="field_page_02" value="0">日　</label>
                        <label><input type="checkbox" name="basic_rest_weekday_new" class="field_page_02" value="1">月　</label>
                        <label><input type="checkbox" name="basic_rest_weekday_new" class="field_page_02" value="2">火　</label>
                        <label><input type="checkbox" name="basic_rest_weekday_new" class="field_page_02" value="3">水　</label>
                        <label><input type="checkbox" name="basic_rest_weekday_new" class="field_page_02" value="4">木　</label>
                        <label><input type="checkbox" name="basic_rest_weekday_new" class="field_page_02" value="5">金　</label>
                        <label><input type="checkbox" name="basic_rest_weekday_new" class="field_page_02" value="6">土　</label>
                        <label><input type="checkbox" name="basic_rest_weekday_new" class="field_page_02" value="7">祝　</label>
                        </td>
                        <td>
                        <input type="text" name="over_limit_hour_new" class="field_page_02 rule-time-input" value="">分
                        <span class="rule-time-h"></span>
                        </td>
                        </tr>
                        <!-- 休憩　エリア -->
                        <input type="hidden" name="rest_id_new" value="new">
                        <tr class="rule-main rule_main_id_new">
                        <th>自動休憩</th>
                        <th>休憩定義</th>
                        </tr>
                        <tr class="rule-main rule_main_id_new">
                        <td>
                        <input type="radio" name="rest_flag_new" class="field_page_02 rest-flag" value="0" data-ruleid="new" checked>しない　
                        <input type="radio" name="rest_flag_new" class="field_page_02 rest-flag" value="1" data-ruleid="new">する
                        </td>
                        <td>
                        <div class="rest_content_new" style="display:none">
                        <input type="radio" name="rest_type_new" class="field_page_02 rest-type" value="1" data-ruleid="new">時間適応　
                        <input type="radio" name="rest_type_new" class="field_page_02 rest-type" value="2" data-ruleid="new">指定時刻
                        <div id="rest_rule_type1_new" class="rest-rule-type_new" style="display:none">
                        <span>
                        労働時間：
                        <input class="field_page_02 rule-time-input" type="text" name="rest_limit_work_new" value="">分以上で
                        <input class="field_page_02 rule-time-input" type="text" name="rest_time_new" value="">分休憩
                        </span>
                        </div>
                        <div id="rest_rule_type2_new" class="rest-rule-type_new" style="display:none">
                        <span>
                        <input class="field_page_02 rule-time-input" type="text" name="rest_in_time_h_new" value="">時
                        <input class="field_page_02 rule-time-input" type="text" name="rest_in_time_m_new" value="">分から
                        <input class="field_page_02 rule-time-input" type="text" name="rest_out_time_h_new" value="">時
                        <input class="field_page_02 rule-time-input" type="text" name="rest_out_time_m_new" value="">分までは休憩
                        </span>
                        </div>
                        </div>
                        </td>
                        </tr>
                        </tbody>
                        </table>
                        </li>
                    ';
                </script>
            </div>

            <!-- page 03 出退勤設定 -->
            <div id="page_03" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">出退勤設定</div>
                        <div id="submit_page_03" class="btn green disabled">保存</div>
                    </div>
                    <div class="content-main">
                        <?php if (isset($message['gateway'])): ?>
                            <input type="hidden" name="message_title_data_id" value="<?= $message['gateway']->id ?>">
                        <?php endif; ?>
                        <p style="margin-bottom:30px;">共通出退勤画面 - 申請機能：
                            <label><input type="radio" name="page03_gateway_mail_flag" class="field_page_03" value="1" <?= (int)$gateway_mail_flag->value === 1 ? ' checked' : '' ; ?> data-id="<?= (int)$gateway_mail_flag->id ?>">する　</label>
                            <label><input type="radio" name="page03_gateway_mail_flag" class="field_page_03" value="0" <?= (int)$gateway_mail_flag->value === 0 ? ' checked' : '' ; ?> data-id="<?= (int)$gateway_mail_flag->id ?>">しない</label>
                        </p>
                        <?php if (isset($message['gateway'])): ?>
                            <p>出退勤画面メッセージ機能：
                                <label><input type="radio" name="page03_public_message1_flag" class="field_page_03" value="1" <?= $message['gateway']->flag == 1 ? ' checked' : '' ; ?>>する　</label>
                                <label><input type="radio" name="page03_public_message1_flag" class="field_page_03" value="0" <?= $message['gateway']->flag == 0 ? ' checked' : '' ; ?>>しない</label>
                            </p>
                            <p>
                                <label>メッセージタイトル：<input type="text" name="page03_public_message1_title" class="field_page_03 message-title-input disabled" value="<?=  $message['gateway']->title ?>"></label>
                            </p>
                            <p>
                                <textarea name="page03_public_message1" class="field_page_03 message-input disabled" cols="60" rows="10"><?=  $message['gateway']->detail ?></textarea>
                            </p>
                        <?php endif; ?>
                        <p>勤務状況：
                            <label><input type="radio" name="gateway_status_view_flag" class="field_page_03" value="0" data-id="<?= (int)$gateway_status_view_flag->id ?>" <?= (int)$gateway_status_view_flag->value === 0 ? ' checked' : '' ; ?>>実 - 出退勤時刻表示　</label>
                            <label><input type="radio" name="gateway_status_view_flag" class="field_page_03" value="1" data-id="<?= (int)$gateway_status_view_flag->id ?>" <?= (int)$gateway_status_view_flag->value === 1 ? ' checked' : '' ; ?>>出退勤時刻表示</label>
                        </p>
                        <p style="margin-top:20px">休憩入力機能：
                            <label><input type="radio" name="rest_input_flag" class="field_page_03" value="1" data-id="<?= (int)$rest_input_flag->id ?>" <?= (int)$rest_input_flag->value == 1 ? ' checked' : '' ; ?>>する</label>
                            <label><input type="radio" name="rest_input_flag" class="field_page_03" value="0" data-id="<?= (int)$rest_input_flag->id ?>" <?= (int)$rest_input_flag->value == 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                        <p style="margin-top:20px">打刻入力時に一時確認（ポップアップ確認）を：
                            <label><input type="radio" name="input_confirm_flag" class="field_page_03" value="1" data-id="<?= (int)$input_confirm_flag->id ?>" <?= (int)$input_confirm_flag->value == 1 ? ' checked' : '' ; ?>>する</label>
                            <label><input type="radio" name="input_confirm_flag" class="field_page_03" value="0" data-id="<?= (int)$input_confirm_flag->id ?>" <?= (int)$input_confirm_flag->value == 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                    </div>
                </form>
            </div>

            <!-- page 04 グループ設定 -->
            <div id="page_04" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">グループ設定</div>
                        <div id="submit_page_04" class="btn green disabled">保存</div>
                    </div>
                    <div class="content-main">
                        <p class="comment">グループは最大3つまで作成できます。名称は自由に変更できます。<br>グループ内の項目は <i class="far fa-plus-square"></i> ボタンで増やすことができます。また、項目はドラッグして並び替えることで表示の順番を変更、<i class="fas fa-times-circle"></i> ボタンで削除できます。<br><br>考え方としては、グループ1が大項目、グループ2が中項目、グループ3が小項目です。<br>ルールを設定する際は、優先順位は、グループ3 > グループ2 > グループ1 となります。<br>例えば、雇用形態：社員、部署：営業、役職：課長 の従業員がいた場合、課長ルールがあれば適応 → 営業ルールがあれば適応 → 社員ルールがあれば適応 となります。<br><br>一般企業であれば、「雇用形態」「部署」「役職」や「配属先」「所属」など、<br>飲食店などは「店舗」などで設定するとよいです。</p>
                        <script>var num_group = [];</script>
                        <?php $i = 1; ?>
                        <?php foreach ($group_title as $key => $value): ?>
                            <p>
                                <label>
                                    グループ<?= $value->group_id ?>：
                                    <input type="text" name="page04_group<?= $value->group_id ?>-title" class="field_page_04" style="width:200px;" value="<?= $value->title ?>">
                                </label>
                                <span id="item_add_group<?= $value->group_id ?>" class="group-item-add"><i class="far fa-plus-square"></i></span>
                            </p>
                            <ul id="sortable_<?= $i ?>" class="group-item-area">
                                <?php foreach ($group[$i] as $key => $val): ?>
                                    <?php if ((int)$val->state === 1): ?>
                                        <li id="<?= $val->id ?>" class="ui-state-default group-item">
                                            <span class="group-item-sort"><i class="fas fa-sort"></i></span>
                                            <input type="text" name="group<?= $i.'_id_'.$val->id ?>" value="<?= $val->group_name ?>" class="field_page_04" style="width:180px;">
                                            <span class="group-item-del" data-groupid="<?= $i ?>" data-itemid="<?= $val->id ?>"><i class="fas fa-times-circle"></i></span>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                            <script>num_group[<?= $i ?>] = <?= (int)$group_max[$i] ?>;</script>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>

            <!-- page 05 ログインユーザー設定 -->
            <div id="page_05" class="tab-page">
                <div class="content-header">
                    <div class="page-title">ログインユーザー設定</div>
                </div>
                <div class="content-main">
                    <div style="width:100%;margin-bottom:5px;display:flex;justify-content:flex-end;">
                        <div id="login_add_user_btn" class="login-btn login-btn-blue"><i class="fas fa-plus"></i> 新規ログインユーザー追加</div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:150px;">表示名</th>
                                <th style="width:150px;">ログインID</th>
                                <th style="width:150px;">権限</th>
                                <?php if ($area_flag === 1): ?>
                                    <th>エリア名</th>
                                <?php endif; ?>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($login_data as $value): ?>
                                <tr>
                                    <td><?= $value->user_name ?></td>
                                    <td><?=$value->login_id ?></td>
                                    <td><?= $value->authority_name ?></td>
                                    <?php if ($area_flag === 1): ?>
                                        <td>
                                            <?php foreach ($area_data as $val) {
                                                if ($val->id === $value->area_id) {
                                                    echo $val->area_name;
                                                }
                                            } ?>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <span class="login-btn login-btn-green login-edit-btn" data-loginid="<?= $value->id ?>"><i class="fas fa-pen"></i> 編集</span>
                                        <?php if ((int)$value->id !== 1): ?>
                                            <span class="login-btn login-btn-red login-del-btn" data-loginid="<?= $value->id ?>"><i class="fas fa-trash"></i> 削除</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- page 06 エリア設定 -->
            <div id="page_06" class="tab-page">
                <div class="content-header">
                    <div class="page-title">エリア設定</div>
                    <div id="submit_page_06" class="btn green">保存</div>
                </div>
                <div class="content-main">
                    <div style="width:100%;margin-bottom:5px;display:flex;justify-content:flex-end;">
                        <div id="area_add_btn" class="login-btn login-btn-blue"><i class="fas fa-plus"></i> 新規エリア追加</div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>エリア名</th>
                                <th>ログインID</th>
                                <th>IPアドレス</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($area_data as $value): ?>
                                <tr>
                                    <td><?= $value->area_name ?></td>
                                    <td>
                                        <?php foreach ($login_data as $val) {
                                            if ($val->area_id === $value->id) {
                                                echo $val->login_id.' ';
                                            }
                                        } ?>
                                    </td>
                                    <td><?= $value->host_ip ?></td>
                                    <td>
                                        <span class="area-btn area-btn-green area-edit-btn" data-areaid="<?= $value->id ?>"><i class="fas fa-pen"></i> 編集</span>
                                        <span class="area-btn area-btn-red area-del-btn" data-areaid="<?= $value->id ?>"><i class="fas fa-trash"></i> 削除</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- page 07 連携設定 -->
            <div id="page_07" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">連携設定</div>
                        <div id="submit_page_07" class="btn green">保存</div>
                    </div>
                    <div class="content-main">
                        <p>レスQ料 連携：
                            <label><input type="radio" name="resq_flag" class="field_page_07" value="1" data-id="<?= (int)$resq_flag->id ?>" <?= (int)$resq_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                            <label><input type="radio" name="resq_flag" class="field_page_07" value="0" data-id="<?= (int)$resq_flag->id ?>" <?= (int)$resq_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            <span class="tips" data-tippy-content="連携を行うには事前登録が必要となります。"><i class="fas fa-question-circle"></i></span>
                        </p>
                        <p>
                            <label>レスQ料 会社コード：<input type="text" name="resq_company_code" class="field_page_07" value="<?= $resq_company_code->value; ?>" data-id="<?= $resq_company_code->id ?>"></label>
                        </p>
                    </div>
                </form>
            </div>

            <!-- page 08 マイページ設定 -->
            <div id="page_08" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">マイページ設定</div>
                        <div id="submit_page_08" class="btn green">保存</div>
                    </div>
                    <div class="content-main">
                        <p>マイページを利用する：
                            <label><input type="radio" name="mypage_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_flag->id ?>" <?= (int)$mypage_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                            <label><input type="radio" name="mypage_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_flag->id ?>" <?= (int)$mypage_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                        <div id="mypage_active">
                            <p>出退勤を許可：
                                <label><input type="radio" name="mypage_input_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_input_flag->id ?>" <?= (int)$mypage_input_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                                <label><input type="radio" name="mypage_input_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_input_flag->id ?>" <?= (int)$mypage_input_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            </p>
                            <p>プロフィール編集を許可：
                                <label><input type="radio" name="mypage_profile_edit_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_profile_edit_flag->id ?>" <?= (int)$mypage_profile_edit_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                                <label><input type="radio" name="mypage_profile_edit_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_profile_edit_flag->id ?>" <?= (int)$mypage_profile_edit_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            </p>
                            <p>パスワード変更を許可：
                                <label><input type="radio" name="mypage_password_edit_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_password_edit_flag->id ?>" <?= (int)$mypage_password_edit_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                                <label><input type="radio" name="mypage_password_edit_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_password_edit_flag->id ?>" <?= (int)$mypage_password_edit_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            </p>
                            <p>
                                <label for="">マイページ　締め日設定：</label>
                                <select name="mypage_end_day" class="field_page_08" data-id="<?= (int)$mypage_end_day->id ?>">
                                    <option value="0" <?= (int)$mypage_end_day->value === 0 ? ' selected' : '' ; ?>>月末締め</option>
                                    <?php for ($i = 5; $i < 27; $i++): ?>
                                    <option value="<?= $i ?>" <?= (int)$mypage_end_day->value === $i ? ' selected' : '' ; ?>><?= $i; ?>日締め</option>
                                <?php endfor; ?>
                                </select>
                            </p>
                            <p>上長による部下の勤怠編集を許可：
                                <label><input type="radio" name="mypage_user_edit_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_user_edit_flag->id ?>" <?= (int)$mypage_user_edit_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                                <label><input type="radio" name="mypage_user_edit_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_user_edit_flag->id ?>" <?= (int)$mypage_user_edit_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            </p>
                            <p>ダッシュボード 勤務状況：
                                <label><input type="radio" name="mypage_status_view_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_status_view_flag->id ?>" <?= (int)$mypage_status_view_flag->value === 0 ? ' checked' : '' ; ?>>実 - 出退勤時刻表示　</label>
                                <label><input type="radio" name="mypage_status_view_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_status_view_flag->id ?>" <?= (int)$mypage_status_view_flag->value === 1 ? ' checked' : '' ; ?>>出退勤時刻表示</label>
                            </p>
                            <p>マイ勤務状況 出退勤時刻表示：
                                <label><input type="radio" name="mypage_my_inout_view_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_my_inout_view_flag->id ?>" <?= (int)$mypage_my_inout_view_flag->value === 1 ? ' checked' : '' ; ?>>実出退勤時刻のみ表示　</label>
                                <label><input type="radio" name="mypage_my_inout_view_flag" class="field_page_08" value="2" data-id="<?= (int)$mypage_my_inout_view_flag->id ?>" <?= (int)$mypage_my_inout_view_flag->value === 2 ? ' checked' : '' ; ?>>出退勤時刻のみ表示</label>
                                <label><input type="radio" name="mypage_my_inout_view_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_my_inout_view_flag->id ?>" <?= (int)$mypage_my_inout_view_flag->value === 0 ? ' checked' : '' ; ?>>両方を表示</label>
                            </p>
                            <p>従業員勤務状況 出退勤時刻表示：
                                <label><input type="radio" name="mypage_status_inout_view_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_status_inout_view_flag->id ?>" <?= (int)$mypage_status_inout_view_flag->value === 1 ? ' checked' : '' ; ?>>実出退勤時刻のみ表示　</label>
                                <label><input type="radio" name="mypage_status_inout_view_flag" class="field_page_08" value="2" data-id="<?= (int)$mypage_status_inout_view_flag->id ?>" <?= (int)$mypage_status_inout_view_flag->value === 2 ? ' checked' : '' ; ?>>出退勤時刻のみ表示</label>
                                <label><input type="radio" name="mypage_status_inout_view_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_status_inout_view_flag->id ?>" <?= (int)$mypage_status_inout_view_flag->value === 0 ? ' checked' : '' ; ?>>両方を表示</label>
                            </p>
                            <p>シフト申請を許可：
                                <label><input type="radio" name="mypage_shift_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_shift_flag->id ?>" <?= (int)$mypage_shift_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                                <label><input type="radio" name="mypage_shift_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_shift_flag->id ?>" <?= (int)$mypage_shift_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            </p>
                            <p>特定の従業員のみ自身の勤怠編集を許可：
                                <label><input type="radio" name="mypage_self_edit_flag" class="field_page_08" value="1" data-id="<?= (int)$mypage_self_edit_flag->id ?>" <?= (int)$mypage_self_edit_flag->value === 1 ? ' checked' : '' ; ?>>する　</label>
                                <label><input type="radio" name="mypage_self_edit_flag" class="field_page_08" value="0" data-id="<?= (int)$mypage_self_edit_flag->id ?>" <?= (int)$mypage_self_edit_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            </p>
                        </div>
                    </div>
                </form>
            </div>

            <!-- page 09 通知設定 -->
            <div id="page_09" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">通知設定</div>
                        <div id="submit_page_09" class="btn green">保存</div>
                    </div>
                    <div class="content-main">
                        <p>メール通知：
                            <label><input type="radio" name="notice_mail_flag" class="field_page_09" value="0" data-id="<?= (int)$notice_mail_flag->id ?>" <?= (int)$notice_mail_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            <label><input type="radio" name="notice_mail_flag" class="field_page_09" value="1" data-id="<?= (int)$notice_mail_flag->id ?>" <?= (int)$notice_mail_flag->value === 1 ? ' checked' : '' ; ?>>出退勤通知　</label>
                            <label><input type="radio" name="notice_mail_flag" class="field_page_09" value="2" data-id="<?= (int)$notice_mail_flag->id ?>" <?= (int)$notice_mail_flag->value === 2 ? ' checked' : '' ; ?>>申請通知　</label>
                            <label><input type="radio" name="notice_mail_flag" class="field_page_09" value="9" data-id="<?= (int)$notice_mail_flag->id ?>" <?= (int)$notice_mail_flag->value === 9 ? ' checked' : '' ; ?>>すべて通知</label>
                        </p>
                        <p>
                            <label>通知先メールアドレス：<input type="text" name="notice_mailaddress1" class="field_page_09" style="width:250px;" value="<?= $notice_mailaddress1->value; ?>" data-id="<?= (int)$notice_mailaddress1->id ?>"></label>
                        </p>
                        <p>
                            <label>通知先メールアドレス：<input type="text" name="notice_mailaddress2" class="field_page_09" style="width:250px;" value="<?= $notice_mailaddress2->value; ?>" data-id="<?= (int)$notice_mailaddress2->id ?>"></label>
                        </p>
                        <p>
                            <label>通知先メールアドレス：<input type="text" name="notice_mailaddress3" class="field_page_09" style="width:250px;" value="<?= $notice_mailaddress3->value; ?>" data-id="<?= (int)$notice_mailaddress3->id ?>"></label>
                        </p>
                        <p>
                            <label>通知先メールアドレス：<input type="text" name="notice_mailaddress4" class="field_page_09" style="width:250px;" value="<?= $notice_mailaddress4->value; ?>" data-id="<?= (int)$notice_mailaddress4->id ?>"></label>
                        </p>
                        <p>
                            <label>通知先メールアドレス：<input type="text" name="notice_mailaddress5" class="field_page_09" style="width:250px;" value="<?= $notice_mailaddress5->value; ?>" data-id="<?= (int)$notice_mailaddress5->id ?>"></label>
                        </p>
                    </div>
                </form>
            </div>

            <!-- page 10 申請設定 -->
            <div id="page_10" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">申請設定</div>
                        <div id="submit_page_10" class="btn green disabled">保存</div>
                    </div>
                    <div class="content-main">
                        <div class="notice-title">上段表示</div>
                        <ul id="noticetable_1" class="notice-item-area connectedSortable">
                            <?php foreach ($notice_status_data as $key => $value): ?>
                                <?php if ((int)$value->group === 1): ?>
                                    <li id="notice_<?= (int)$value->id ?>" class="notice-item">
                                        <div class="inner">
                                            <span class="notice-item-sort"><i class="fas fa-sort"></i></span>
                                            <?= $value->notice_status_title ?>
                                        </div>
                                        <div class="inner">
                                            <div class="notice-status<?php if ((int)$value->status === 1): ?> active<?php endif; ?>" data-noticeid="<?= $value->id ?>" data-noticestatus="<?= $value->status ?>">
                                                <?php if ((int)$value->status === 1) {
                                                    echo '使用';
                                                } else {
                                                    echo '未使用';
                                                } ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <div class="notice-title">中段表示</div>
                        <ul id="noticetable_2" class="notice-item-area connectedSortable">
                            <?php foreach ($notice_status_data as $key => $value): ?>
                                <?php if ((int)$value->group === 2): ?>
                                    <li id="notice_<?= (int)$value->id ?>" class="notice-item">
                                        <div class="inner">
                                            <span class="notice-item-sort"><i class="fas fa-sort"></i></span>
                                            <?= $value->notice_status_title ?>
                                        </div>
                                        <div class="inner">
                                            <div class="notice-status<?php if ((int)$value->status === 1): ?> active<?php endif; ?>" data-noticeid="<?= $value->id ?>" data-noticestatus="<?= $value->status ?>">
                                                <?php if ((int)$value->status === 1) {
                                                    echo '使用'; 
                                                    } else {
                                                    echo '未使用';
                                                } ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <div class="notice-title">下段表示</div>
                        <ul id="noticetable_3" class="notice-item-area connectedSortable">
                            <?php foreach ($notice_status_data as $key => $value): ?>
                                <?php if ((int)$value->group === 3): ?>
                                    <li id="notice_<?= (int)$value->id ?>" class="notice-item">
                                        <div class="inner">
                                            <span class="notice-item-sort"><i class="fas fa-sort"></i></span>
                                            <?= $value->notice_status_title ?>
                                        </div>
                                        <div class="inner">
                                            <div class="notice-status<?php if ((int)$value->status === 1): ?> active<?php endif; ?>" data-noticeid="<?= $value->id ?>" data-noticestatus="<?= $value->status ?>">
                                                <?php if ((int)$value->status === 1) {
                                                    echo '使用';
                                                } else {
                                                    echo '未使用';
                                                } ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </form>
            </div>

            <!-- page 11 各種データ出力 -->
            <div id="page_11" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">各種データ出力</div>
                    </div>
                    <div class="content-main">
                        <p style="margin-bottom: 30px;">
                            <label for="">給与らくだ９　勤怠集計データ用CSV：</label>
                            <select name="output_csv_001" class="field_page_11">
                                <?php
                                for ($i = 0; $i < 12; $i++) {
                                    $today = (new DateTime())->format('Y-m-01');
                                    $now = new DateTime($today);
                                    $now->sub(DateInterval::createFromDateString($i.' month'));
                                    $date = $now->format('Ym');
                                    $date_w = $now->format('Y年m月');
                                    echo '<option value="'.$date.'" >'.$date_w.'</option>';
                                }
                                ?>
                            </select>
                            <span id="output_csv_001" class="btn green"><i class="fas fa-file-csv"></i> 出力</span>
                        </p>
                    </div>
                </form>
            </div>

            <!-- page 12 シフト設定 -->
            <div id="page_12" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">シフト設定</div>
                        <div id="submit_page_12" class="btn green disabled">保存</div>
                    </div>
                    <div class="content-main">
                        <p>デフォルト表示：
                            <label><input type="radio" name="shift_view_flag" class="field_page_12" value="0" data-id="<?= (int)$shift_view_flag->id ?>" <?= (int)$shift_view_flag->value === 0 ? ' checked' : '' ; ?>>リスト表示　</label>
                            <label><input type="radio" name="shift_view_flag" class="field_page_12" value="1" data-id="<?= (int)$shift_view_flag->id ?>" <?= (int)$shift_view_flag->value === 1 ? ' checked' : '' ; ?>>カレンダー表示</label>
                        </p>
                        <p>カレンダー始まりの曜日：
                            <label><input type="radio" name="shift_cal_first_day" class="field_page_12" value="0" data-id="<?= (int)$shift_cal_first_day->id ?>" <?= (int)$shift_cal_first_day->value === 0 ? ' checked' : '' ; ?>>日　</label>
                            <label><input type="radio" name="shift_cal_first_day" class="field_page_12" value="1" data-id="<?= (int)$shift_cal_first_day->id ?>" <?= (int)$shift_cal_first_day->value === 1 ? ' checked' : '' ; ?>>月　</label>
                            <label><input type="radio" name="shift_cal_first_day" class="field_page_12" value="2" data-id="<?= (int)$shift_cal_first_day->id ?>" <?= (int)$shift_cal_first_day->value === 2 ? ' checked' : '' ; ?>>火　</label>
                            <label><input type="radio" name="shift_cal_first_day" class="field_page_12" value="3" data-id="<?= (int)$shift_cal_first_day->id ?>" <?= (int)$shift_cal_first_day->value === 3 ? ' checked' : '' ; ?>>水　</label>
                            <label><input type="radio" name="shift_cal_first_day" class="field_page_12" value="4" data-id="<?= (int)$shift_cal_first_day->id ?>" <?= (int)$shift_cal_first_day->value === 4 ? ' checked' : '' ; ?>>木　</label>
                            <label><input type="radio" name="shift_cal_first_day" class="field_page_12" value="5" data-id="<?= (int)$shift_cal_first_day->id ?>" <?= (int)$shift_cal_first_day->value === 5 ? ' checked' : '' ; ?>>金　</label>
                            <label><input type="radio" name="shift_cal_first_day" class="field_page_12" value="6" data-id="<?= (int)$shift_cal_first_day->id ?>" <?= (int)$shift_cal_first_day->value === 6 ? ' checked' : '' ; ?>>土　</label>
                        </p>
                        <p style="margin-top:30px;">自動シフト：
                            <label><input type="radio" name="auto_shift_flag" class="field_page_12" value="0" data-id="<?= (int)$auto_shift_flag->id ?>" <?= (int)$auto_shift_flag->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            <label><input type="radio" name="auto_shift_flag" class="field_page_12" value="1" data-id="<?= (int)$auto_shift_flag->id ?>" <?= (int)$auto_shift_flag->value === 1 ? ' checked' : '' ; ?>>する</label>
                        </p>
                        <p style="margin-top:30px;">シフト提出の警告（マイページのみ対応）：
                            <label><input type="radio" name="mypage_shift_alert" class="field_page_12" value="0" data-id="<?= (int)$mypage_shift_alert->id ?>" <?= (int)$mypage_shift_alert->value === 0 ? ' checked' : '' ; ?>>しない</label>
                            <label><input type="radio" name="mypage_shift_alert" class="field_page_12" value="1" data-id="<?= (int)$mypage_shift_alert->id ?>" <?= (int)$mypage_shift_alert->value === 1 ? ' checked' : '' ; ?>>する</label>
                        </p>
                        <p>
                            <label for="">シフト提出締め切り日：</label>
                            <select name="shift_closing_day" class="field_page_12" data-id="<?= (int)$shift_closing_day->id ?>">
                                <option value=""<?= $shift_closing_day->value == '' ? ' selected' : '' ; ?>>--</option>
                                <option value="1"<?= $shift_closing_day->value == 1 ? ' selected' : '' ; ?>>1日</option>
                                <option value="2"<?= $shift_closing_day->value == 2 ? ' selected' : '' ; ?>>2日</option>
                                <option value="3"<?= $shift_closing_day->value == 3 ? ' selected' : '' ; ?>>3日</option>
                                <option value="4"<?= $shift_closing_day->value == 4 ? ' selected' : '' ; ?>>4日</option>
                                <option value="5"<?= $shift_closing_day->value == 5 ? ' selected' : '' ; ?>>5日</option>
                                <option value="6"<?= $shift_closing_day->value == 6 ? ' selected' : '' ; ?>>6日</option>
                                <option value="7"<?= $shift_closing_day->value == 7 ? ' selected' : '' ; ?>>7日</option>
                                <option value="8"<?= $shift_closing_day->value == 8 ? ' selected' : '' ; ?>>8日</option>
                                <option value="9"<?= $shift_closing_day->value == 9 ? ' selected' : '' ; ?>>9日</option>
                                <option value="10"<?= $shift_closing_day->value == 10 ? ' selected' : '' ; ?>>10日</option>
                                <option value="11"<?= $shift_closing_day->value == 11 ? ' selected' : '' ; ?>>11日</option>
                                <option value="12"<?= $shift_closing_day->value == 12 ? ' selected' : '' ; ?>>12日</option>
                                <option value="13"<?= $shift_closing_day->value == 13 ? ' selected' : '' ; ?>>13日</option>
                                <option value="14"<?= $shift_closing_day->value == 14 ? ' selected' : '' ; ?>>14日</option>
                                <option value="15"<?= $shift_closing_day->value == 15 ? ' selected' : '' ; ?>>15日</option>
                                <option value="16"<?= $shift_closing_day->value == 16 ? ' selected' : '' ; ?>>16日</option>
                                <option value="17"<?= $shift_closing_day->value == 17 ? ' selected' : '' ; ?>>17日</option>
                                <option value="18"<?= $shift_closing_day->value == 18 ? ' selected' : '' ; ?>>18日</option>
                                <option value="19"<?= $shift_closing_day->value == 19 ? ' selected' : '' ; ?>>19日</option>
                                <option value="20"<?= $shift_closing_day->value == 20 ? ' selected' : '' ; ?>>20日</option>
                                <option value="21"<?= $shift_closing_day->value == 21 ? ' selected' : '' ; ?>>21日</option>
                                <option value="22"<?= $shift_closing_day->value == 22 ? ' selected' : '' ; ?>>22日</option>
                                <option value="23"<?= $shift_closing_day->value == 23 ? ' selected' : '' ; ?>>23日</option>
                                <option value="24"<?= $shift_closing_day->value == 24 ? ' selected' : '' ; ?>>24日</option>
                                <option value="25"<?= $shift_closing_day->value == 25 ? ' selected' : '' ; ?>>25日</option>
                                <option value="26"<?= $shift_closing_day->value == 26 ? ' selected' : '' ; ?>>26日</option>
                                <option value="27"<?= $shift_closing_day->value == 27 ? ' selected' : '' ; ?>>27日</option>
                                <option value="28"<?= $shift_closing_day->value == 28 ? ' selected' : '' ; ?>>28日</option>
                                <option value="29"<?= $shift_closing_day->value == 29 ? ' selected' : '' ; ?>>29日</option>
                                <option value="30"<?= $shift_closing_day->value ==30 ? ' selected' : '' ; ?>>30日</option>
                                <option value="0"<?= $shift_closing_day->value === '0' ? ' selected' : '' ; ?>>月末</option>
                            </select>
                        </p>
                    </div>
                </form>
            </div>

            <!-- page 13 就業規則 -->
            <div id="page_13" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">就業規則</div>
                        <div id="submit_page_13" class="btn green disabled">保存</div>
                    </div>
                    <div class="content-main">
                        <p>１週間の定義　始まりの曜日：
                            <label><input type="radio" name="company_first_day" class="field_page_13" value="0" data-id="<?= (int)$company_data['company_week_start']->id ?>" <?= (int)$company_data['company_week_start']->value === 0 ? ' checked' : '' ; ?>>日　</label>
                            <label><input type="radio" name="company_first_day" class="field_page_13" value="1" data-id="<?= (int)$company_data['company_week_start']->id ?>" <?= (int)$company_data['company_week_start']->value === 1 ? ' checked' : '' ; ?>>月　</label>
                            <label><input type="radio" name="company_first_day" class="field_page_13" value="2" data-id="<?= (int)$company_data['company_week_start']->id ?>" <?= (int)$company_data['company_week_start']->value === 2 ? ' checked' : '' ; ?>>火　</label>
                            <label><input type="radio" name="company_first_day" class="field_page_13" value="3" data-id="<?= (int)$company_data['company_week_start']->id ?>" <?= (int)$company_data['company_week_start']->value === 3 ? ' checked' : '' ; ?>>水　</label>
                            <label><input type="radio" name="company_first_day" class="field_page_13" value="4" data-id="<?= (int)$company_data['company_week_start']->id ?>" <?= (int)$company_data['company_week_start']->value === 4 ? ' checked' : '' ; ?>>木　</label>
                            <label><input type="radio" name="company_first_day" class="field_page_13" value="5" data-id="<?= (int)$company_data['company_week_start']->id ?>" <?= (int)$company_data['company_week_start']->value === 5 ? ' checked' : '' ; ?>>金　</label>
                            <label><input type="radio" name="company_first_day" class="field_page_13" value="6" data-id="<?= (int)$company_data['company_week_start']->id ?>" <?= (int)$company_data['company_week_start']->value === 6 ? ' checked' : '' ; ?>>土　</label>
                        </p>
                    </div>
                </form>
            </div>

            <!-- page 14 メッセージ機能設定 -->
            <div id="page_14" class="tab-page">
                <form action="" autocomplete="off">
                    <?php $message_in_id = isset($message['in']) ? $message['in']->id : '' ?>
                    <input type="hidden" name="page14_message_in_id" value="<?= $message_in_id ?>">
                    <?php $message_out_id = isset($message['out']) ? $message['out']->id : '' ?>
                    <input type="hidden" name="page14_message_out_id" value="<?= $message_out_id ?>">
                    <div class="content-header">
                        <div class="page-title">メッセージ機能設定</div>
                        <div id="submit_page_14" class="btn green disabled">保存</div>
                    </div>
                    <div class="content-main">
                        <p>基本メッセージ　出勤時利用：
                            <?php $message_in_flag = isset($message['in']) ? $message['in']->flag : 0; ?>
                            <label><input type="radio" name="page14_message_in_flag" class="field_page_14" value="1" <?= $message_in_flag == 1 ? ' checked' : '' ; ?>>する　</label>
                            <label><input type="radio" name="page14_message_in_flag" class="field_page_14" value="0" <?= $message_in_flag == 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                        <p>
                            <?php $message_in_title = isset($message['in']) ? $message['in']->title : ""; ?>
                            <label>メッセージタイトル：<input type="text" name="page14_message_in_title" class="field_page_14 message-title-input<?= $message_in_flag == 0 ? ' disabled' : '' ?>" value="<?= $message_in_title ?>"></label>
                        </p>
                        <p>
                            <?php $message_in_detail = isset($message['in']) ? $message['in']->detail : ""; ?>
                            <textarea name="page14_massage_in_detail" class="field_page_14 message-input<?= $message_in_flag == 0 ? ' disabled' : '' ?>"><?= $message_in_detail ?></textarea>
                        </p>
                        <p>基本メッセージ　退勤時利用：
                            <?php $message_out_flag = isset($message['out']) ? $message['out']->flag : 0; ?>
                            <label><input type="radio" name="page14_message_out_flag" class="field_page_14" value="1" <?= $message_out_flag == 1 ? ' checked' : '' ; ?>>する　</label>
                            <label><input type="radio" name="page14_message_out_flag" class="field_page_14" value="0" <?= $message_out_flag == 0 ? ' checked' : '' ; ?>>しない</label>
                        </p>
                        <p>
                            <?php $message_out_title = isset($message['out']) ? $message['out']->title : ""; ?>
                            <label>メッセージタイトル：<input type="text" name="page14_message_out_title" class="field_page_14 message-title-input<?= $message_out_flag == 0 ? ' disabled' : '' ?>" value="<?= $message_out_title ?>"></label>
                        </p>
                        <p>
                            <?php $message_out_detail = isset($message['out']) ? $message['out']->detail : ""; ?>
                            <textarea name="page14_massage_out_detail" class="field_page_14 message-input<?= $message_out_flag == 0 ? ' disabled' : '' ?>"><?= $message_out_detail ?></textarea>
                        </p>
                    </div>
                </form>
            </div>

            <!-- 設計中 page 15 データ登録 -->
            <div id="page_15" class="tab-page">
                <form action="">
                    <div class="content-header">
                        <div class="page-title">データ登録</div>                
                    </div>
                    <div class="content-main">
                        <p>出退勤一括修正：<input id="work_time_register" type="file" accept=".xlsx, .xls, .csv">
                        </p>
                    </div>
                </form>
            </div>

            <!-- 設計中 page 16 給与管理設定 -->
            <div id="page_16" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">給与管理設定</div>    
                        <div id="submit_page_16" class="btn green disabled">保存</div>            
                    </div>
                    <div class="content-main">
                        <p>給与管理の使用：
                            <label><input type="radio" name="page16_pay_flag" class="field_page_16" value="1" <?= $pay_flag->value == 1 ? ' checked' : '' ?>>する　</label>
                            <label><input type="radio" name="page16_pay_flag" class="field_page_16" value="0" <?= $pay_flag->value == 0 ? ' checked' : '' ?>>しない</label>
                        </p>
                    </div>
                </form>
            </div>

            <!-- 設計中 page 17 従業員管理設定 -->
            <div id="page_17" class="tab-page">
                <form action="" autocomplete="off">
                    <div class="content-header">
                        <div class="page-title">従業員管理設定</div>    
                        <div id="submit_page_17" class="btn green disabled">保存</div>            
                    </div>
                    <div class="content-main">
                        <p>従業員IDの定義：
                            <label><input type="radio" name="page17_user_id_define" class="field_page_17" value="0" <?= $user_id_define->value == 0 ? ' checked' : '' ?>>任意で設定　</label>
                            <label><input type="radio" name="page17_user_id_define" class="field_page_17" value="1" <?= $user_id_define->value == 1 ? ' checked' : '' ?>>自動連番</label>
                        </p>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- モーダル ログインユーザ -->
    <div id="page05_modal" data-iziModal-fullscreen="true" data-iziModal-title="" data-iziModal-subtitle="">
        <div class="modal-content">
            <div class="inner">
                <form action="" autocomplete="off">
                    <div class="row">
                        <div class="form-title">表示名</div><input type="text" name="login_user_name" class="field_page_05">
                    </div>
                    <div class="row">
                        <div class="form-title">ログインID</div><input type="text" name="login_user_id" class="field_page_05">
                    </div>
                    <div class="row">
                        <div class="form-title">パスワード</div><input type="password" name="login_user_password" class="field_page_05" autocomplete="off">
                    </div>
                    <div class="pass-text">※ パスワードは変更するときのみ記入してください。変更しない場合は空白のまま。</div>
                    <div class="row">
                        <div class="form-title">権限</div>
                        <select name="login_authority" class="field_page_05">
                            <option value="1" class="authority1">一般</option>
                            <option value="2" class="authority1">回覧者</option>
                            <option value="5" class="authority1">シフト管理者</option>
                            <option value="3" class="authority1">編集者</option>
                            <option value="4">管理者</option>
                        </select>
                    </div>
                    <div id="area_select" class="row">
                        <div class="form-title">エリア</div>
                        <select name="area_name" class="field_page_05">
                            <option value=""></option>
                            <?php foreach ($area_data as $value): ?>
                            <option value="<?= $value->id ?>"><?= $value->area_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div id="modal-error-message"></div>
                    </div>
                    <div class="btn-area">
                        <div class="row" style="margin-top:30px;">
                            <div id="login_modal_submit" class="btn login-btn-blue disabled" style="margin-right:20px;" data-id="">保存</div>
                            <div id="login_modal_cancel" class="btn login-btn-red">キャンセル</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- モーダル エリア -->
    <div id="page06_modal" data-iziModal-fullscreen="true" data-iziModal-title="" data-iziModal-subtitle="">
        <div class="modal-content">
            <div class="inner">
                <form action="" autocomplete="off">
                    <div class="row">
                        <div class="form-title">エリア名</div><input type="text" name="area_name" class="field_page_06">
                    </div>
                    <div class="row">
                        <div class="form-title">IPアドレス</div><input type="text" name="host_ip" class="field_page_06">
                    </div>
                    <div class="row">
                        <div id="modal-error-message2"></div>
                    </div>
                    <div class="btn-area">
                        <div class="row" style="margin-top:30px;">
                            <div id="area_modal_submit" class="btn login-btn-blue disabled" style="margin-right:20px;" data-id="">保存</div>
                            <div id="area_modal_cancel" class="btn login-btn-red">キャンセル</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- javascript読込 -->
    <?php $this->load->view('parts/_javascript_view'); ?>
</body>

</html>
