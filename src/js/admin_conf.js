(function() {

    var key = Cookies.get('confTab') ? Cookies.get('confTab') : '01';

    var group_order = [];
    var login_flag;
    var area_flag;
    var notice_order = {};
    var notice_status_data = {};
    var rule_order = [];


    /** 配列内で値が重複してないか調べる **/
    function existsSameValue(a){
        var s = new Set(a);
        return s.size != a.length;
    }
    // str: 時刻文字列（HH:mm）
    function isTime (str) {
        return str.match(/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/) !== null;
    };

    // MODELS
    const model = {
        // model page 01 保存
        savePage01: function() {
            var edit_in_time_h = $('input[name="edit_in_time_h"]').val();
            var edit_in_time_m = $('input[name="edit_in_time_m"]').val();
            if (!edit_in_time_h) {
                var edit_in_time = '';
            }
            if (edit_in_time_h && !edit_in_time_m) {
                var edit_in_time = ('0' + edit_in_time_h).slice(-2)+':00';
                if (isTime(edit_in_time) === false) {
                    edit_in_time = '';
                }
            }
            if (edit_in_time_h && edit_in_time_m) {
                var edit_in_time = ('0' + edit_in_time_h).slice(-2)+':'+('0' + edit_in_time_m).slice(-2);
                if (isTime(edit_in_time) === false) {
                    edit_in_time = '';
                }
            }
            var edit_out_time_h = $('input[name="edit_out_time_h"]').val();
            var edit_out_time_m = $('input[name="edit_out_time_m"]').val();
            if (!edit_out_time_h) {
                var edit_out_time = '';
            }
            if (edit_out_time_h && !edit_out_time_m) {
                var edit_out_time = ('0' + edit_out_time_h).slice(-2)+':00';
                if (isTime(edit_out_time) === false) {
                    edit_out_time = '';
                }
            }
            if (edit_out_time_h && edit_out_time_m) {
                var edit_out_time = ('0' + edit_out_time_h).slice(-2)+':'+('0' + edit_out_time_m).slice(-2);
                if (isTime(edit_out_time) === false) {
                    edit_out_time = '';
                }
            }
            var saveData = {
                'company_name': {
                    'id': $('input[name="page01_company_name"]').attr('data-id'),
                    'value': $('input[name="page01_company_name"]').val()
                },
                'id_size': {
                    'id': $('select[name="page01_id_size"]').attr('data-id'),
                    'value': $('select[name="page01_id_size"] option:selected').val()
                },
                'download_filetype': {
                    'id': $('[name="page01_filetype"]').attr('data-id'),
                    'value': $('select[name="page01_filetype"] option:selected').val()
                },
                'end_day': {
                    'id': $('[name="page01_end_day"]').attr('data-id'),
                    'value': $('select[name="page01_end_day"] option:selected').val()
                },
                'over_time_flag': {
                    'id': $('[name="page01_over_time_flag"]').attr('data-id'),
                    'value': $('input[name="page01_over_time_flag"]:checked').val()
                },
                'night_time_flag': {
                    'id': $('[name="page01_night_time_flag"]').attr('data-id'),
                    'value': $('input[name="page01_night_time_flag"]:checked').val()
                },
                'area_flag': {
                    'id': $('[name="page01_area_flag"]').attr('data-id'),
                    'value': $('input[name="page01_area_flag"]:checked').val()
                },
                'gps_flag': {
                    'id': $('[name="page01_gps_flag"]').attr('data-id'),
                    'value': $('input[name="page01_gps_flag"]:checked').val()
                },
                'minute_time_flag': {
                    'id': $('[name="page01_minute_time_flag"]').attr('data-id'),
                    'value': $('input[name="page01_minute_time_flag"]:checked').val()
                },
                'normal_time_flag': {
                    'id': $('[name="page01_normal_time_flag"]').attr('data-id'),
                    'value': $('input[name="page01_normal_time_flag"]:checked').val()
                },
                'edit_in_time': {
                    'id': $('[name="edit_in_time_h"]').attr('data-id'),
                    'value': edit_in_time
                },
                'edit_out_time': {
                    'id': $('[name="edit_out_time_h"]').attr('data-id'),
                    'value': edit_out_time
                },
                'edit_min': {
                    'id': $('[name="edit_min"]').attr('data-id'),
                    'value': $('select[name="edit_min"]  option:selected').val()
                }
            };
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_01',
                data: {
                    saveData: saveData
                }
            })
        },
        // model page 02 データチェック
        checkPage02: function() {
            // 適応の重複チェック
            var rule_type_arr = Object.keys(rule_type_data).map(function (key) {return rule_type_data[key]});
            if (existsSameValue(rule_type_arr)) {
                alert('エラー　ルール適応に重複があります！');
                return;
            }
            // order
            rule_order = $('#rules_list').sortable('toArray');

            var rule_data = {};
            for (var rule = 0; rule < Object.keys(rule_num).length; rule++) {
                var rule_type = Number($('[name="rule_type_' + rule_num[rule] + '"]:checked').val());
                var rule_group_id = '';
                var rule_group_no = '';
                if (rule_type === 2) {
                    rule_group_id = Number($('[name="rule_group_title_' + rule_num[rule] + '"]:checked').attr('data-group-id'));
                    rule_group_no = Number($('[name="rule_group_id_' + rule_num[rule] + '_'+rule_group_id+'"] option:selected').val());
                    if (!rule_group_id || !rule_group_no) {
                        alert('エラー　入力に誤りがあります！グループ未設定');
                        return;
                    }
                }
                var rule_user_id = '';
                if (rule_type === 3) {
                    rule_user_id = Number($('[name="rule_user_id_' + rule_num[rule] + '"] option:selected').val());
                }
                // in_marume
                var in_marume_flag = Number($('[name="in_marume_flag_' + rule_num[rule] + '"]:checked').val());
                var in_marume_hour = '';
                var in_marume_time = '';
                if (in_marume_flag === 1) {
                    in_marume_hour = Number($('[name="in_marume1_hour_' + rule_num[rule] + '"] option:selected').val());
                }
                if (in_marume_flag === 2) {
                    var minute = ('0' + $('[name="in_marume2_m_' + rule_num[rule] + '"]').val()).slice(-2);
                    in_marume_time = $('[name="in_marume2_h_' + rule_num[rule] + '"]').val()+':'+minute;
                }
                if (in_marume_flag === 3) {
                    in_marume_hour = Number($('[name="in_marume3_hour_' + rule_num[rule] + '"] option:selected').val());
                    var minute = ('0' + $('[name="in_marume3_m_' + rule_num[rule] + '"]').val()).slice(-2);
                    in_marume_time = $('[name="in_marume3_h_' + rule_num[rule] + '"]').val()+':'+minute;
                }
                if (in_marume_flag === 5) {
                    in_marume_hour = Number($('[name="in_marume5_hour_' + rule_num[rule] + '"] option:selected').val());
                }
                if (in_marume_flag === 2 || in_marume_flag === 3) {
                    if (isTime(in_marume_time) === false) {
                        alert('エラー　入力に誤りがあります！出勤合わせ時刻');
                        return;
                    }
                    in_marume_time += ':00';
                }
                // out_marume
                var out_marume_flag = Number($('[name="out_marume_flag_' + rule_num[rule] + '"]:checked').val());
                var out_marume_hour = '';
                var out_marume_time = '';
                if (out_marume_flag === 1) {
                    out_marume_hour = Number($('[name="out_marume1_hour_' + rule_num[rule] + '"] option:selected').val());
                }
                if (out_marume_flag === 2) {
                    var minute = ('0' + $('[name="out_marume2_m_' + rule_num[rule] + '"]').val()).slice(-2);
                    out_marume_time = $('[name="out_marume2_h_' + rule_num[rule] + '"]').val()+':'+minute;
                }
                if (out_marume_flag === 3) {
                    out_marume_hour = Number($('[name="out_marume3_hour_' + rule_num[rule] + '"] option:selected').val());
                    var minute = ('0' + $('[name="out_marume3_m_' + rule_num[rule] + '"]').val()).slice(-2);
                    out_marume_time = $('[name="out_marume3_h_' + rule_num[rule] + '"]').val()+':'+minute;
                }
                if (out_marume_flag === 5) {
                    out_marume_hour = Number($('[name="out_marume5_hour_' + rule_num[rule] + '"] option:selected').val());
                }
                if (out_marume_flag === 2 || out_marume_flag === 3) {
                    if (isTime(out_marume_time) === false) {
                        alert('エラー　入力に誤りがあります！出勤合わせ時刻');
                        return;
                    }
                    out_marume_time += ':00';
                }
                // basic_in_time
                var basic_in_time_flag = Number($('[name="basic_in_' + rule_num[rule] + '"]:checked').val());
                var basic_in_time = '';
                if (basic_in_time_flag === 1) {
                    var minute = ('0' + $('[name="in_basic_m_' + rule_num[rule] + '"]').val()).slice(-2);
                    basic_in_time = $('[name="in_basic_h_' + rule_num[rule] + '"]').val()+':'+minute;
                    if (isTime(basic_in_time) === false) {
                        alert('エラー　入力に誤りがあります！定時　出勤時刻');
                        return;
                    }
                    basic_in_time += ':00';
                    var basicInTime = new Date('2000-01-01 '+basic_in_time);
                }
                // basic_out_time
                var basic_out_time_flag = Number($('[name="basic_out_' + rule_num[rule] + '"]:checked').val());
                var basic_out_time = '';
                if (basic_out_time_flag === 1) {
                    var minute = ('0' + $('[name="out_basic_m_' + rule_num[rule] + '"]').val()).slice(-2);
                    basic_out_time = $('[name="out_basic_h_' + rule_num[rule] + '"]').val()+':'+minute;
                    if (isTime(basic_out_time) === false) {
                        alert('エラー　入力に誤りがあります！定時　退勤時刻');
                        return;
                    }
                    basic_out_time += ':00';
                    var basicOutTime = new Date('2000-01-01 '+basic_out_time);
                }
                // basic time
                if (basic_in_time_flag === 1 && basic_out_time_flag === 1) {
                    if (basicInTime.getTime() > basicOutTime.getTime()) {
                        alert('エラー　入力に誤りがあります！定時時刻');
                        return;
                    }
                }
                if (basic_in_time_flag === 1 || basic_out_time_flag === 1) {
                    if (!basic_in_time || !basic_out_time) {
                        alert('エラー　入力に誤りがあります！定時時刻');
                        return;
                    }
                }
                // basic_rest_weekday
                var basic_rest_weekday = $('input[name=basic_rest_weekday_' + rule_num[rule] + ']:checked').map(function(){
                    return $(this).val();
                }).get();
                // over_limit_hour
                var over_limit_hour = Number($('[name="over_limit_hour_' + rule_num[rule] + '"]').val());
                // rest_rule
                var rest_rule_flag = Number($('[name="rest_flag_' + rule_num[rule] + '"]:checked').val());
                var rest_rule_id = Number($('[name="rest_id_' + rule_num[rule] + '"]').val());
                if (!rest_rule_id) {
                    rest_rule_id = 'new';
                }
                var limit_work_hour = '';
                var rest_time = '';
                var rest_in_time = '';
                var rest_out_time = '';
                var rest_type = '';
                if (rest_rule_flag === 1) {
                    rest_type = Number($('[name="rest_type_' + rule_num[rule] + '"]:checked').val());
                    if (!rest_type) {
                        alert('エラー　入力に誤りがあります！休憩定義');
                        return;
                    }
                    if (rest_type === 1) {
                        limit_work_hour = Number($('[name="rest_limit_work_' + rule_num[rule] + '"]').val());
                        rest_time = Number($('[name="rest_time_' + rule_num[rule] + '"]').val());
                        if (!limit_work_hour > 0 || !rest_time > 0) {
                            alert('エラー　入力に誤りがあります！休憩定義 時間適応');
                            return;
                        }
                        if (limit_work_hour < rest_time) {
                            alert('エラー　入力に誤りがあります！休憩定義 時間適応');
                            return;
                        }
                    }
                    if (rest_type === 2) {
                        var minute = ('0' + $('[name="rest_in_time_m_' + rule_num[rule] + '"]').val()).slice(-2);
                        rest_in_time = $('[name="rest_in_time_h_' + rule_num[rule] + '"]').val()+':'+minute;
                        if (isTime(rest_in_time) === false) {
                            alert('エラー　入力に誤りがあります！休憩定義 指定時刻in');
                            return;
                        }
                        rest_in_time += ':00';
                        var restInTime = new Date('2000-01-01 '+rest_in_time);
                        var minute = ('0' + $('[name="rest_out_time_m_' + rule_num[rule] + '"]').val()).slice(-2);
                        rest_out_time = $('[name="rest_out_time_h_' + rule_num[rule] + '"]').val()+':'+minute;
                        if (isTime(rest_out_time) === false) {
                            alert('エラー　入力に誤りがあります！休憩定義 指定時刻out');
                            return;
                        }
                        rest_out_time += ':00';
                        var restOutTime = new Date('2000-01-01 '+rest_out_time);
                        if (restInTime.getTime() > restOutTime.getTime()) {
                            alert('エラー　入力に誤りがあります！休憩定義 指定時刻');
                            return;
                        }
                    }
                }
                // order
                var order = rule_order.indexOf('rule_'+rule_num[rule]);

                rule_data[rule] = {
                    'rule_id': rule_num[rule],
                    'rule_title' : $('[name="rule_title_' + rule_num[rule] + '"]').val(),
                    'rule_type': rule_type,
                    'rule_group_id': rule_group_id,
                    'rule_group_no': rule_group_no,
                    'rule_user_id': rule_user_id,
                    'in_marume_flag': in_marume_flag,
                    'out_marume_flag': out_marume_flag,
                    'in_marume_hour': in_marume_hour,
                    'out_marume_hour': out_marume_hour,
                    'in_marume_time': in_marume_time,
                    'out_marume_time': out_marume_time,
                    'basic_in_time_flag': basic_in_time_flag,
                    'basic_out_time_flag': basic_out_time_flag,
                    'basic_in_time': basic_in_time,
                    'basic_out_time': basic_out_time,
                    'basic_rest_weekday': basic_rest_weekday,
                    'over_limit_hour': over_limit_hour,
                    'rest_rule_flag': rest_rule_flag,
                    'rest_type': rest_type,
                    'rest_rule_id': rest_rule_id,
                    'limit_work_hour': limit_work_hour,
                    'rest_time': rest_time,
                    'rest_in_time': rest_in_time,
                    'rest_out_time': rest_out_time,
                    'order': order
                }
            }
            model.savePage02(rule_data).done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            }).fail(function(data) {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                setTimeout("location.reload()", 3000);
            })

        },
        // model page 02 保存
        savePage02: function(rule_data) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_02',
                data: {
                    rule_data: rule_data
                }
            })
        },
        // model page 02 削除
        delPage02: function(rule_id, rest_id) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/del_page_02',
                data: {
                    rule_id: rule_id,
                    rest_id: rest_id
                }
            })
        },
        // model page 03 保存
        savePage03: function() {
            var saveData = {
                'gateway_mail_flag': {
                    'id':  $('input[name="page03_gateway_mail_flag"]').attr('data-id'),
                    'value': $('input[name="page03_gateway_mail_flag"]:checked').val()
                },
                'gateway_status_view_flag': {
                    'id': $('input[name="gateway_status_view_flag"]').attr('data-id'),
                    'value': $('input[name="gateway_status_view_flag"]:checked').val()
                },
                'rest_input_flag': {
                    'id': $('input[name="rest_input_flag"]').attr('data-id'),
                    'value': $('input[name="rest_input_flag"]:checked').val()
                },
                'input_confirm_flag': {
                    'id': $('input[name="input_confirm_flag"]').attr('data-id'),
                    'value': $('input[name="input_confirm_flag"]:checked').val()
                }
            };
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_03',
                data: {
                    gateway_mail_flag: $('input[name="page03_gateway_mail_flag"]:checked').val(),
                    message_title_data_id: $('input[name="message_title_data_id"]').val(),
                    public_message1_flag: $('input[name="page03_public_message1_flag"]:checked').val(),
                    public_message1_title: $('input[name="page03_public_message1_title"]').val(),
                    public_message1: $('textarea[name="page03_public_message1"]').val(),
                    saveData: saveData
                }
            })
        },
        // model page 04 グループ 保存
        savePage04: function() {
            var group_title = [];
            for (var i = 1; i <= 3; i++) {
                group_title[i - 1] = $('input[name="page04_group' + i + '-title"]').val();
            }
            var group_item = [];
            for (var i = 1; i <= 3; i++) {
                var item = [];
                for (var n = 1; n <= num_group[i]; n++) {
                    item[n - 1] = $('input[name="group' + i + '_id_' + n + '"]').val();
                }
                group_item[i - 1] = item;
            }
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_04',
                data: {
                    group_title: group_title,
                    group_order: group_order,
                    group_item: group_item
                }
            })
        },
        // model page 04 グループitem 削除
        delGroupItem: function(group_id, item_id) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/del_item_page_04',
                data: {
                    group_id: group_id,
                    item_id: item_id
                }
            })
        },
        // model page 05 ログインユーザーデータ取得
        getLoginUserData: function(login_id) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/get_loginuser_data_page_05',
                data: {
                    id: login_id
                }
            })
        },
        // model page 05 ログインユーザ　保存
        savePage05: function() {
            if ($('select[name="login_authority"] option:selected').val() == 1) {
                var area_id = $('select[name="area_name"] option:selected').val()
            } else {
                var area_id = '';
            }
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_05',
                data: {
                    id: $('#login_modal_submit').attr('data-id'),
                    login_id: $('input[name="login_user_id"]').val(),
                    password: $('input[name="login_user_password"]').val(),
                    user_name: $('input[name="login_user_name"]').val(),
                    authority: $('select[name="login_authority"] option:selected').val(),
                    area_id: area_id,
                    flag: login_flag
                }
            })
        },
        delPage05: function(id) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/del_page_05',
                data: {
                    id: id
                }
            })
        },
        // model page 06 エリアデータ取得
        getArearData: function(area_id) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/get_area_data_page_06',
                data: {
                    id: area_id
                }
            })
        },
        // model page 06 エリアデータ　保存
        savePage06: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_06',
                data: {
                    id: $('#area_modal_submit').attr('data-id'),
                    area_name: $('input[name="area_name"]').val(),
                    flag: area_flag,
                    host_ip: $('input[name="host_ip"]').val()
                }
            })
        },
        delPage06: function(id) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/del_page_06',
                data: {
                    id: id
                }
            })
        },
        // model page 07 連携設定　保存
        savePage07: function() {
            var saveData = {
                'resq_flag': {
                    'id': $('input[name="resq_flag"]').attr('data-id'),
                    'value': $('input[name="resq_flag"]:checked').val()
                },
                'resq_company_code': {
                    'id': $('input[name="resq_company_code"]').attr('data-id'),
                    'value': $('input[name="resq_company_code"]').val()
                }
            };
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_07',
                data: {
                    saveData: saveData
                }
            })
        },
        // model page 08 マイページ設定　保存
        savePage08: function() {
            var saveData = {
                'mypage_flag': {
                    'id': $('input[name="mypage_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_flag"]:checked').val()
                },
                'mypage_input_flag': {
                    'id': $('input[name="mypage_input_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_input_flag"]:checked').val()
                },
                'mypage_profile_edit_flag': {
                    'id': $('input[name="mypage_profile_edit_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_profile_edit_flag"]:checked').val()
                },
                'mypage_password_edit_flag': {
                    'id': $('input[name="mypage_password_edit_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_password_edit_flag"]:checked').val()
                },
                'mypage_end_day': {
                    'id': $('select[name="mypage_end_day"]').attr('data-id'),
                    'value': $('select[name="mypage_end_day"] option:selected').val()
                },
                'mypage_user_edit_flag': {
                    'id': $('input[name="mypage_user_edit_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_user_edit_flag"]:checked').val()
                },
                'mypage_my_inout_view_flag': {
                    'id': $('input[name="mypage_my_inout_view_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_my_inout_view_flag"]:checked').val()
                },
                'mypage_status_inout_view_flag': {
                    'id': $('input[name="mypage_status_inout_view_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_status_inout_view_flag"]:checked').val()
                },
                'mypage_status_view_flag': {
                    'id': $('input[name="mypage_status_view_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_status_view_flag"]:checked').val()
                },
                'mypage_shift_flag': {
                    'id': $('input[name="mypage_shift_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_shift_flag"]:checked').val()
                },
                'mypage_self_edit_flag': {
                    'id': $('input[name="mypage_self_edit_flag"]').attr('data-id'),
                    'value': $('input[name="mypage_self_edit_flag"]:checked').val()
                },
            };
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_08',
                data: {
                    saveData: saveData
                }
            })
        },
        // model page 09 通知設定　保存
        savePage09: function() {
            var saveData = {
                'notice_mail_flag': {
                    'id': $('input[name="notice_mail_flag"]').attr('data-id'),
                    'value': $('input[name="notice_mail_flag"]:checked').val()
                },
                'notice_mailaddress1': {
                    'id': $('input[name="notice_mailaddress1"]').attr('data-id'),
                    'value': $('input[name="notice_mailaddress1"]').val()
                },
                'notice_mailaddress2': {
                    'id': $('input[name="notice_mailaddress2"]').attr('data-id'),
                    'value': $('input[name="notice_mailaddress2"]').val()
                },
                'notice_mailaddress3': {
                    'id': $('input[name="notice_mailaddress3"]').attr('data-id'),
                    'value': $('input[name="notice_mailaddress3"]').val()
                },
                'notice_mailaddress4': {
                    'id': $('input[name="notice_mailaddress4"]').attr('data-id'),
                    'value': $('input[name="notice_mailaddress4"]').val()
                },
                'notice_mailaddress5': {
                    'id': $('input[name="notice_mailaddress5"]').attr('data-id'),
                    'value': $('input[name="notice_mailaddress5"]').val()
                },
            };
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_09',
                data: {
                    saveData: saveData
                }
            })
        },
        // model page 10 申請 保存
        savePage10: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_10',
                data: {
                    notice_order: notice_order,
                    notice_status_data: notice_status_data
                }
            })
        },
        // model page 11 CSVダウンロード
        csv_download_page_11: function(type, csv_date) {
            var action = '../../data/admin_conf_download';
            var form = document.createElement('form');
            form.setAttribute('action', action);
            form.setAttribute('method', 'post');
            form.style.display = "none";
            document.body.appendChild(form);
            var input1 = document.createElement('input');
            input1.setAttribute('type', 'hidden');
            input1.setAttribute('name', 'type');
            input1.setAttribute('value', type);
            form.appendChild(input1);
            var input2 = document.createElement('input');
            input2.setAttribute('type', 'hidden');
            input2.setAttribute('name', 'csv_date');
            input2.setAttribute('value', csv_date);
            form.appendChild(input2);
            form.submit();
        },
        // model page 12 シスト設定　保存
        savePage12: function() {
            var saveData = {
                'shift_view_flag': {
                    'id': $('input[name="shift_view_flag"]').attr('data-id'),
                    'value': $('input[name="shift_view_flag"]:checked').val()
                },
                'shift_cal_first_day': {
                    'id': $('input[name="shift_cal_first_day"]').attr('data-id'),
                    'value': $('input[name="shift_cal_first_day"]:checked').val()
                },
                'auto_shift_flag': {
                    'id': $('input[name="auto_shift_flag"]').attr('data-id'),
                    'value': $('input[name="auto_shift_flag"]:checked').val()
                },
                'mypage_shift_alert': {
                    'id': $('input[name="mypage_shift_alert"]').attr('data-id'),
                    'value': $('input[name="mypage_shift_alert"]:checked').val()
                },
                'shift_closing_day': {
                    'id': $('select[name="shift_closing_day"]').attr('data-id'),
                    'value': $('select[name="shift_closing_day"] option:selected').val()
                },
            };
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_12',
                data: {
                    saveData: saveData
                }
            })
        },
        // model page 14 メッセージ機能設定 保存
        savePage14: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_conf/save_page_14',
                data: {
                    message_in_id: $('[name="page14_message_in_id"]').val(),
                    message_in_flag: $('[name="page14_message_in_flag"]:checked').val(),
                    message_in_title: $('[name="page14_message_in_title"]').val(),
                    message_in_detail: $('[name="page14_massage_in_detail"]').val(),
                    message_out_id: $('[name="page14_message_out_id"]').val(),
                    message_out_flag: $('[name="page14_message_out_flag"]:checked').val(),
                    message_out_title: $('[name="page14_message_out_title"]').val(),
                    message_out_detail: $('[name="page14_massage_out_detail"]').val()
                }
            })
        },
        // model page 16 給与管理設定　保存
        savePage16: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../data/admin_conf/save_page_16',
                data: {
                    pay_flag: $('[name="page16_pay_flag"]:checked').val()
                }
            })
        }
    }

    // VIEWS
    const view = {
        // view タブ表示
        renderTab: function(key) {
            $('.tab-page').removeClass('active-page');
            $('#page_' + key).addClass('active-page');
            $('.tab').removeClass('active-menu');
            $('#tab_' + key).addClass('active-menu');
        },
        // view グループ　item 追加
        addGroupItem: function(group_id) {
            num_group[group_id]++;
            $('#sortable_' + group_id).append('<li id="' + num_group[group_id] + '" class="ui-state-default group-item"><span class="group-item-sort"><i class="fas fa-sort"></i></span><input type="text" name="group' + group_id + '_id_' + num_group[group_id] + '" class="field_page_04" style="width:180px;"><span class="group-item-del"><i class="fas fa-times-circle"></i></span></li>');
            group_order[group_id - 1] = $('#sortable_' + group_id).sortable('toArray');
        },
        // view ログイン　モーダル表示
        renderLoginModal: function(data, flag) {
            $('#modal-error-message').hide();
            $('.field_page_05').val('');
            $('#login_modal_submit').addClass('disabled');
            var title = flag === 'edit' ? 'ログインユーザー編集' : '新規ログインユーザー追加';
            var subtitle = flag === 'edit' ? '「' + data.user_name + '」の編集をします' : '新しくログインユーザーを追加します';
            $('.iziModal-header-title').text(title);
            $('.iziModal-header-subtitle').text(subtitle);
            if (flag === 'edit') {
                $('input[name="login_user_name"]').val(data.user_name);
                $('input[name="login_user_id"]').val(data.login_id);
                $('[name="login_authority"] option[value="' + data.authority + '"]').prop('selected', true);
                if (data.authority === '1') {
                    $('#area_select').show();
                    $('[name="area_name"] option[value="' + data.area_id + '"]').prop('selected', true);
                } else {
                    $('#area_select').hide();
                }
                $('.pass-text').show();
                if (data.id === '1') {
                    $('.authority1').hide();
                } else {
                    $('.authority1').show();
                }
                $('#login_modal_submit').attr('data-id', data.id);
            } else {
                $('input[name="login_user_name"]').val('');
                $('input[name="login_user_id"]').val('');
                $('[name="login_authority"] option[value="1"]').prop('selected', true);
                $('.pass-text').hide();
                $('.authority1').show();
                $('#login_modal_submit').attr('data-id', '');
                $('#area_select').show();
            }
            $("#page05_modal").iziModal('open');
        },
        // view マイページ　アクティブ表示
        renderMypageActive: function() {
            if ($('input[name="mypage_flag"]:checked').val() == 1) {
                $('#mypage_active').show();
            } else {
                $('#mypage_active').hide();
            }
        },
        // view エリア　モーダル表示
        renderAreaModal: function(data, flag) {
            $('#modal-error-message2').hide();
            $('.field_page_06').val('');
            $('#area_modal_submit').addClass('disabled');
            var title = flag === 'edit' ? 'エリア編集' : '新規エリア追加';
            var subtitle = flag === 'edit' ? '「' + data.area_name + '」の編集をします' : '新しいエリアを追加します';
            $('.iziModal-header-title').text(title);
            $('.iziModal-header-subtitle').text(subtitle);
            if (flag === 'edit') {
                $('input[name="area_name"]').val(data.area_name);
                $('input[name="host_ip"]').val(data.host_ip);
                $('#area_modal_submit').attr('data-id', data.id);
            } else {
                $('input[name="area_name"]').val('');
                $('input[name="host_ip"]').val('');
                $('#area_modal_submit').attr('data-id', '');
            }
            $("#page06_modal").iziModal('open');
        },
        // view page03 メッセージ表示
        renderMessage: function() {
            var message_flag = $('[name="page03_public_message1_flag"]:checked').val();
            if (message_flag == 0) {
                $('.field_page_03.message-title-input, .field_page_03.message-input').addClass('disabled');
            } else {
                $('.field_page_03.message-title-input, .field_page_03.message-input').removeClass('disabled');
            }
        },
        // view page02 適応表示
        renderSummary: function(rule_id, group_id) {
            var rule_type_id = $('[name="rule_type_' + rule_id + '"]:checked').val();
            var group_title = '';
            var group_name = '';
            var user_name = '';
            if (rule_type_id == 1) {
                var rule_type = '全体';
                rule_type_data[rule_id] = 'all';
            }
            if (rule_type_id == 2) {
                var rule_type = 'グループ : ';
                group_title = $('#group_title_' + group_id).text();
                group_name = $('.rule_group_id_' + rule_id + '_' + group_id + ' option:selected').text();
                group_no = $('.rule_group_id_' + rule_id + '_' + group_id + ' option:selected').val();
                rule_type_data[rule_id] = 'group-'+group_id+'-'+group_no;
            }
            if (rule_type_id == 3) {
                var rule_type = '個人 : ';
                user_name = $('[name="rule_user_id_' + rule_id + '"] option:selected').text();
                user_id = $('[name="rule_user_id_' + rule_id + '"] option:selected').val();
                rule_type_data[rule_id] = 'user-'+user_id;
            }
            $('#rule_type_summary_' + rule_id).text('適応：' + rule_type + group_title + user_name + ' ' + group_name);
        },
        // view page02 new rule
        renderNewRule: function() {
            $('#rules_list').prepend(new_rule_html);
            $('.rule-none-text').hide();
        }
    }

    $(function() {
        // // tooltip
        // tippy('.tips', {
        //   arrow: true,
        //   size: 'small'
        // });
        // タブ表示
        view.renderTab(key);
        // タブ操作
        $('.tab').on('click', function() {
            key = $(this).attr('id').substr(-2);
            Cookies.set('confTab', key);
            view.renderTab(key);
        });
        // グループ設定用　テーブルソート設定
        $("#sortable_1, #sortable_2, #sortable_3").sortable();
        $("#sortable_1, #sortable_2, #sortable_3").disableSelection();
        group_order[0] = $('#sortable_1').sortable('toArray');
        group_order[1] = $('#sortable_2').sortable('toArray');
        group_order[2] = $('#sortable_3').sortable('toArray');
        // notice テーブルソート設定
        $("#noticetable_1, #noticetable_2, #noticetable_3").sortable({
            connectWith: ".connectedSortable"
        }).disableSelection();
        if ($('#noticetable_1').sortable('toArray').length > 0) {
            notice_order[0] = $('#noticetable_1').sortable('toArray');
        } else {
            notice_order[0] = 'none';
        }
        if ($('#noticetable_2').sortable('toArray').length > 0) {
            notice_order[1] = $('#noticetable_2').sortable('toArray');
        } else {
            notice_order[1] = 'none';
        }
        if ($('#noticetable_3').sortable('toArray').length > 0) {
            notice_order[2] = $('#noticetable_3').sortable('toArray');
        } else {
            notice_order[2] = 'none';
        }
        // ルール用 テーブルソート設定
        $("#rules_list").sortable({
            connectWith: ".connectedSortable"
        }).disableSelection();
        rule_order = $('#rules_list').sortable('toArray');

        // page 01 基本設定　操作
        $(document).on('input change', '.field_page_01', function() {
            $('#submit_page_01').removeClass('disabled');
        });
        $(document).on('click', '#submit_page_01', function() {
            $('#submit_page_01').addClass('disabled');
            model.savePage01().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            }).fail(function(data) {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                setTimeout("location.reload()", 3000);
            })
        });
        // page 02 ルール設定　操作
        $(document).delegate('.field_page_02', 'input change', function() {
            $('#submit_page_02').removeClass('disabled');
        });
        $(document).delegate('#window_open', 'click', function() { // all window open
            $('.rule-main').show();
        });
        $(document).delegate('#window_close', 'click', function() { // all window close
            $('.rule-main').hide();
        });
        $(document).delegate('.rule-window-btn', 'click', function() {
            var rule_id = $(this).attr('data-ruleid');
            $('.rule_main_id_' + rule_id).slideToggle(); // window開閉
        });
        $(document).delegate('.rule_type', 'click', function() { // 適応欄 詳細表示
            var rule_id = $(this).attr('data-ruleid');
            var type = $(this).val();
            $('.rule_type_option_' + rule_id).addClass('rule-hide');
            $('#rule_type_' + type + '_' + rule_id).removeClass('rule-hide');
            view.renderSummary(rule_id, '');
        });
        $(document).delegate('.rule_type_group', 'click', function() { // 適応欄　グループ選択時
            var rule_id = $(this).attr('data-ruleid');
            var group_id = $(this).val();
            $('[name="rule_group_id_' + rule_id + '"]').attr('disabled', true);
            $('.rule_group_id_' + rule_id + '_' + group_id).attr('disabled', false);
            view.renderSummary(rule_id, group_id);
        });
        $(document).delegate('.in_marume_flag', 'click', function() { // 出勤ルール 選択時
            var rule_id = $(this).attr('data-ruleid');
            var in_marume_flag = $(this).val();
            $('.rule_in_marume_option_' + rule_id).addClass('rule-hide');
            $('#in_marume_' + in_marume_flag + '_' + rule_id).removeClass('rule-hide');
        });
        $(document).delegate('.out_marume_flag', 'click', function() { // 退勤ルール 選択時
            var rule_id = $(this).attr('data-ruleid');
            var out_marume_flag = $(this).val();
            $('.rule_out_marume_option_' + rule_id).addClass('rule-hide');
            $('#out_marume_' + out_marume_flag + '_' + rule_id).removeClass('rule-hide');
        });
        $(document).delegate('.rest-flag', 'click', function() { // 休憩flag on off
            var rule_id = $(this).attr('data-ruleid');
            var rest_flag = $(this).val();
            $('.rest_content_' + rule_id).hide();
            if (rest_flag == 1) {
            $('.rest_content_' + rule_id).show();
            }
        });
        $(document).delegate('.rest-type', 'click', function() { // 休憩定義　選択時
            var rule_id = $(this).attr('data-ruleid');
            var rest_flag = $(this).val();
            $('.rest-rule-type_' + rule_id).hide();
            $('#rest_rule_type'+rest_flag+'_'+rule_id).show();
        });
        $(document).delegate('.group-select', 'input change', function() { // グループセレクト
            var rule_id = $(this).attr('data-ruleid');
            var group_id = $(this).attr('data-group-id');
            view.renderSummary(rule_id, group_id);
        });
        $(document).delegate('.user-select', 'input change', function() { // userセレクト
            var rule_id = $(this).attr('data-ruleid');
            view.renderSummary(rule_id, '');
        });
        $(document).delegate('.basic-in-flag', 'input change', function() {
            var rule_id = $(this).attr('data-ruleid');
            var flag = $(this).val();
            if (flag == 0) {
                $('[name="in_basic_h_' + rule_id + '"]').attr('disabled', true);
                $('[name="in_basic_m_' + rule_id + '"]').attr('disabled', true);
            }
            if (flag == 1) {
                $('[name="in_basic_h_' + rule_id + '"]').attr('disabled', false);
                $('[name="in_basic_m_' + rule_id + '"]').attr('disabled', false);
            }
        });
        $(document).delegate('.basic-out-flag', 'input change', function() {
            var rule_id = $(this).attr('data-ruleid');
            var flag = $(this).val();
            if (flag == 0) {
                $('[name="out_basic_h_' + rule_id + '"]').attr('disabled', true);
                $('[name="out_basic_m_' + rule_id + '"]').attr('disabled', true);
            }
            if (flag == 1) {
                $('[name="out_basic_h_' + rule_id + '"]').attr('disabled', false);
                $('[name="out_basic_m_' + rule_id + '"]').attr('disabled', false);
            }
        });
        $('.connectedSortable').bind('sortstop', function() { // table sort
            $('#submit_page_02').removeClass('disabled');
        });

        $(document).delegate('#submit_page_02', 'click', function() { // 保存
            $('#submit_page_02').addClass('disabled');
            model.checkPage02();
        });
        $(document).delegate('#new_rule_page_02', 'click', function() { // 新規ルール作成
            $('#new_rule_page_02').addClass('disabled');
            $('#submit_page_02').removeClass('disabled');
            rule_num[Object.keys(rule_num).length] = 'new';
            rule_type_data['new'] = 'all';
            view.renderNewRule();
        });
        $(document).delegate('.rule-del-btn', 'click', function() { // 削除
            var rule_id = $(this).attr('data-ruleid');
            var rest_id = $('[name="rest_id_' + rule_id + '"]').val();
            var title = $('[name="rule_title_' + rule_id + '"]').val();
            var summary = $('#rule_type_summary_' + rule_id).text();
            result = window.confirm(title + ' ' + summary + 'を削除します。');
            if (result) {
                model.delPage02(rule_id, rest_id).done(function(data) {
                if (data.message === 'ok') {
                    location.reload();
                }
                }).fail(function(data) {
                    siiimpleToast.alert('削除エラー', {
                        position: 'top|right'
                    });
                    setTimeout("location.reload()", 3000);
                })
            }
        });
        $(document).delegate('.rule-new-del-btn', 'click', function() { // 削除
            var rule_id = $(this).attr('data-ruleid');
            var rest_id = $('[name="rest_id_' + rule_id + '"]').val();
            var title = $('[name="rule_title_' + rule_id + '"]').val();
            var summary = $('#rule_type_summary_' + rule_id).text();
            result = window.confirm(title + ' ' + summary + 'を削除します。');
            if (result) {
                location.reload();
            }
        });

        // page 03 出退勤設定　操作
        view.renderMessage();
        $(document).on('input change', '.field_page_03', function() {
            $('#submit_page_03').removeClass('disabled');
            view.renderMessage();
        });
        $(document).on('click', '#submit_page_03', function() {
            $('#submit_page_03').addClass('disabled');
            model.savePage03().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            }).fail(function(data) {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                setTimeout("location.reload()", 3000);
            })
        });

        // page 14 メッセージ機能設定
        $(document).on('input change', '.field_page_14', function() {
            $('#submit_page_14').removeClass('disabled');
        });
        $(document).on('change', '[name="page14_message_in_flag"]', function() {
            if ($(this).val() == 0) {
                $('[name="page14_message_in_title"]').addClass('disabled');
                $('[name="page14_massage_in_detail"]').addClass('disabled');
            } else {
                $('[name="page14_message_in_title"]').removeClass('disabled');
                $('[name="page14_massage_in_detail"]').removeClass('disabled');
            }
        });
        $(document).on('change', '[name="page14_message_out_flag"]', function() {
            if ($(this).val() == 0) {
                $('[name="page14_message_out_title"]').addClass('disabled');
                $('[name="page14_massage_out_detail"]').addClass('disabled');
            } else {
                $('[name="page14_message_out_title"]').removeClass('disabled');
                $('[name="page14_massage_out_detail"]').removeClass('disabled');
            }
        });
        $(document).on('click', '#submit_page_14', function() {
            $(this).addClass('disabled');
            model.savePage14().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            }).fail(function(data) {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                setTimeout("location.reload()", 3000);
            })
        });

        // page 04 グループ設定　操作
        $('.group-item-area').bind('sortstop', function() {
            var id = $(this).attr('id').slice(-1);
            group_order[Number(id) - 1] = $(this).sortable('toArray');
            $('#submit_page_04').removeClass('disabled');
        });
        $(document).delegate('#item_add_group1', 'click', function() {
            view.addGroupItem(1);
        });
        $(document).delegate('#item_add_group2', 'click', function() {
            view.addGroupItem(2);
        });
        $(document).delegate('#item_add_group3', 'click', function() {
            view.addGroupItem(3);
        });
        $(document).delegate('.field_page_04', 'input change', function() {
            $('#submit_page_04').removeClass('disabled');
        });
        $(document).delegate('#submit_page_04', 'click', function() {
            $('#submit_page_04').addClass('disabled');
            model.savePage04().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            }).fail(function(data) {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                setTimeout("location.reload()", 3000);
            })
        });
        $(document).delegate('.group-item-del', 'click', function() {
            var item_name = $(this).prev('input').val();
            if (window.confirm(item_name + "を削除してよろしいでしょうか？")) {
                var group_id = $(this).attr('data-groupid');
                var item_id = $(this).attr('data-itemid');
                model.delGroupItem(group_id, item_id).done(function(data) {
                    location.reload();
                }).fail(function(data) {
                    siiimpleToast.alert('エラー', {
                        position: 'top|right'
                    });
                    setTimeout("location.reload()", 3000);
                })
            }
        });

        // page 05 ログインユーザー設定
        $("#page05_modal").iziModal({
            headerColor: '#1591a2',
            focusInput: false
        });
        $(document).delegate('.login-edit-btn', 'click', function() {
            var login_id = $(this).attr('data-loginid');
            model.getLoginUserData(login_id).done(function(data) {
                login_flag = 'edit';
                view.renderLoginModal(data, login_flag);
            })
        });
        $(document).delegate('#login_add_user_btn', 'click', function() {
            var data;
            login_flag = 'add';
            view.renderLoginModal(data, login_flag);
        });
        $(document).delegate('#login_modal_cancel', 'click', function() {
            $("#page05_modal").iziModal('close');
        });
        $(document).delegate('.field_page_05', 'input change', function() {
            $('#modal-error-message').hide();
            if (login_flag === 'add') {
                if ($('input[name="login_user_name"]').val() !== '' && $('input[name="login_user_id"]').val() !== '' && $('input[name="login_user_password"]').val() !== '') {
                    $('#login_modal_submit').removeClass('disabled');
                } else {
                    $('#login_modal_submit').addClass('disabled');
                }
            }
            if (login_flag === 'edit') {
                if ($('input[name="login_user_name"]').val() !== '' && $('input[name="login_user_id"]').val() !== '') {
                    $('#login_modal_submit').removeClass('disabled');
                } else {
                    $('#login_modal_submit').addClass('disabled');
                }
            }
        });
        $(document).delegate('#login_modal_submit', 'click', function() {
            model.savePage05().done(function(data) {
                if (data.message == 'err_id') {
                    $('#modal-error-message').text('このログインIDは既に使用されているため登録出来ません。');
                    $('#modal-error-message').show();
                }
                if (data.message == 'ok') {
                    $('#page05_modal').iziModal('close');
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                    setTimeout("location.reload()", 3000);
                }
            }).fail(function() {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                // setTimeout("location.reload()", 3000);
            })
        });
        $(document).delegate('.login-del-btn', 'click', function() {
            var login_id = $(this).attr('data-loginid');
            model.getLoginUserData(login_id).done(function(data) {
                login_flag = 'del';
                if (confirm('ログインユーザー「' + data.user_name + '」の削除をします')) {
                    model.delPage05(data.id).done(function(data) {
                        if (data.message == 'ok') {
                            location.reload();
                        } else {
                            siiimpleToast.alert('エラー', {
                                position: 'top|right'
                            });
                        }
                    }).fail(function() {
                        siiimpleToast.alert('エラー', {
                            position: 'top|right'
                        });
                    })
                }
            })
        });
        $(document).delegate('[name="login_authority"]', 'change', function() {
            if ($('[name="login_authority"]').val() == 1) {
                $('#area_select').show();
            } else {
                $('#area_select').hide();
            }
        });

        // page 06 エリア設定
        $("#page06_modal").iziModal({
            headerColor: '#1591a2',
            focusInput: false
        });
        $(document).delegate('.area-edit-btn', 'click', function() {
            var area_id = $(this).attr('data-areaid');
            model.getArearData(area_id).done(function(data) {
                area_flag = 'edit';
                view.renderAreaModal(data, area_flag);
            })
        });
        $(document).delegate('#area_add_btn', 'click', function() {
            var data;
            area_flag = 'add';
            view.renderAreaModal(data, area_flag);
        });
        $(document).delegate('#area_modal_cancel', 'click', function() {
            $("#page06_modal").iziModal('close');
        });
        $(document).delegate('.field_page_06', 'input change', function() {
            if ($('input[name="area_name"]').val() !== '') {
                $('#area_modal_submit').removeClass('disabled');
            } else {
                $('#area_modal_submit').addClass('disabled');
            }
        });
        $(document).delegate('#area_modal_submit', 'click', function() {
            model.savePage06().done(function(data) {
                if (data.message == 'err_id') {
                    $('#modal-error-message2').text('このエリア名は既に使用されているため登録出来ません。');
                    $('#modal-error-message2').show();
                }
                if (data.message == 'ok') {
                    $('#page06_modal').iziModal('close');
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                    setTimeout("location.reload()", 3000);
                }
            }).fail(function() {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                // setTimeout("location.reload()", 3000);
            })
        });
        $(document).delegate('.area-del-btn', 'click', function() {
            var area_id = $(this).attr('data-areaid');
            model.getArearData(area_id).done(function(data) {
                area_flag = 'del';
                if (confirm('エリア「' + data.area_name + '」の削除をします')) {
                    model.delPage06(data.id).done(function(data) {
                        if (data.message == 'ok') {
                            location.reload();
                        } else {
                            siiimpleToast.alert('エラー', {
                                position: 'top|right'
                            });
                        }
                    }).fail(function() {
                        siiimpleToast.alert('エラー', {
                            position: 'top|right'
                        });
                    })
                }
            })
        });

        // page 07 連携設定
        $(document).delegate('#submit_page_07', 'click', function() {
            $(this).addClass('disabled');
            model.savePage07().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            });
        });
        
        // page 08 連携設定
        view.renderMypageActive();
        $(document).delegate('input[name="mypage_flag"]', 'click', function() {
            view.renderMypageActive();
        });
        $(document).delegate('#submit_page_08', 'click', function() {
            $(this).addClass('disabled');
            model.savePage08().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            });
        });
        
        // page 09 通知設定
        $(document).delegate('#submit_page_09', 'click', function() {
            $(this).addClass('disabled');
            model.savePage09().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            });
        });
        
        // page 10 申請設定
        $('.connectedSortable').bind('sortstop', function() {
            if ($('#noticetable_1').sortable('toArray').length > 0) {
                notice_order[0] = $('#noticetable_1').sortable('toArray');
            } else {
                notice_order[0] = 'none';
            }
            if ($('#noticetable_2').sortable('toArray').length > 0) {
                notice_order[1] = $('#noticetable_2').sortable('toArray');
            } else {
                notice_order[1] = 'none';
            }
            if ($('#noticetable_3').sortable('toArray').length > 0) {
                notice_order[2] = $('#noticetable_3').sortable('toArray');
            } else {
                notice_order[2] = 'none';
            }
            $('#submit_page_10').removeClass('disabled');
        });
        $(document).delegate('.notice-status', 'click', function() {
            var notice_id = $(this).attr('data-noticeid');
            var notice_status = $(this).attr('data-noticestatus');
            if (notice_status == 1) {
                $(this).attr('data-noticestatus', 0);
                $(this).text('未使用');
                notice_status_data[notice_id] = 0;
            } else {
                $(this).attr('data-noticestatus', 1);
                $(this).text('使用');
                notice_status_data[notice_id] = 1;
            }
            $(this).toggleClass('active');
            $('#submit_page_10').removeClass('disabled');
        });
        $(document).delegate('#submit_page_10', 'click', function() {
            $('#submit_page_10').addClass('disabled');
            model.savePage10().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            }).fail(function(data) {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                setTimeout("location.reload()", 3000);
            })
        });
        
        // page 11 各種データ出力
        $(document).delegate('#output_csv_001', 'click', function() {
            var csv_date = $('[name="output_csv_001"]').val();
            var type = '001';
            model.csv_download_page_11(type, csv_date);
        });
        
        // page 12 シフト設定
        $(document).delegate('.field_page_12', 'input change', function() {
            $('#submit_page_12').removeClass('disabled');
            view.renderMessage();
        });
        $(document).delegate('#submit_page_12', 'click', function() {
            model.savePage12().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            }).fail(function(data) {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                setTimeout("location.reload()", 3000);
            })
        });

        // page 15 データ登録 ベータ
        $('#work_time_register').on('change', function() {
            var file = $(this).prop('files');
            var formData = new FormData();
            formData.append('files', file[0]);
            $.ajax({
                url: '../data/admin_conf_register/work_time',
                dataType: 'text',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false
            })
            .done(function(data) {
                console.log(data);
            })
        });

        // page 16 給与管理設定　ベータ
        $(document).on('input change', '.field_page_16', function() {
            $('#submit_page_16').removeClass('disabled');
        });
        $('#submit_page_16').on('click', function() {
            model.savePage16().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            }).fail(function(data) {
                siiimpleToast.alert('登録エラー', {
                    position: 'top|right'
                });
                setTimeout("location.reload()", 3000);
            })
        })
    });
}());
