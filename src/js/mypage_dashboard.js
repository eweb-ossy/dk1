(function() {
    Cookies.remove('notice_id');

    var mobile_menu_visible = 0; // モバイルサイドバー用
    var userName; // 従業員名
    var notice_count = 0; // 通知数
    var info = '';
    var latitude = '';
    var longitude = '';
    var noticeDate = '';
    var timepicker_in; // モーダル用picker
    var timepicker_out; // モーダル用picker
    var noticeHopeData = {};
    var calendar1;
    var calendar_term;
    var end_date = '';
    var notice_flag = 1;

    var formatDate = function(date, format) { // 指定の日付フォーマットを返す
        format = format.replace(/YYYY/g, date.getFullYear());
        format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
        format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));
        format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));
        format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
        format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
        if (format.match(/S/g)) {
            var milliSeconds = ('00' + date.getMilliseconds()).slice(-3);
            var length = format.match(/S/g).length;
            for (var i = 0; i < length; i++) format = format.replace(/S/, milliSeconds.substring(i, i + 1));
        }
        return format;
    };

    var model = {
        // model パーソナル情報取得
        get_user: function() {

            // ここで GPS データ 取得は どうか？
            function success(position) {
                info = '取得';
                latitude = position.coords.latitude;
                longitude = position.coords.longitude;
                return;
            }
            function error(e) {
                info = '不明エラー';
                switch (e.code) {
                    case 1:
                        info = '拒否';
                        break;
                    case 2:
                        info = '失敗';
                        break;
                    case 3:
                        info = 'タイムアウト';
                        break;
                }
                return;
            }
            navigator.geolocation.getCurrentPosition(success, error, {
                enableHighAccuracy: true
            });

            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/gateway/user',
                data: {
                    user_id: userId
                }
            })
        },
        // model 出退勤登録
        insert_data: function(flag) {
            if (gps_flag === 0 || gps_flag === 1 && info === '取得' || gps_flag === 2) {
                var d = new $.Deferred();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '../../data/gateway/insert',
                    data: {
                        flag: flag,
                        user_id: userId,
                        user_name: userName,
                        latitude: latitude,
                        longitude: longitude,
                        info: info
                    }
                }).done(function(data) {
                if (data.message === 'ng') {
                    view.show_err_toast('データ登録エラー：出退勤情報を保存できませんでした。もう一度やり直してください。');
                    return;
                }
                if (flag === 'in') {
                    var toast_title = 'おはようございます';
                }
                if (flag === 'out') {
                    var toast_title = 'おつかれさまです';
                }
                if (flag === 'nonstop_in') {
                    var toast_title = '直行';
                }
                if (flag === 'nonstop_out') {
                    var toast_title = '直帰';
                }
                view.show_toast(toast_title + '<br>' + data.message); // トースト表示
                model.get_user().done(function(data) { // 従業員データ取得
                    view.render_user(data); // 従業員データ表示
                    model.get_now_user(); // 勤務状況一覧表示
                    });
                }).fail(function() {
                    view.show_err_toast('通信エラー');
                }).always(function() {
                    d.resolve();
                });
                return d;
            }
            var flag_text = flag === 'in' ? '出勤' : '退勤';
            view.show_err_toast('位置情報取得エラー：'+info+'<br><br>'+flag_text+'できませんでした。<br><br>もう一度'+flag_text+'をするか、ログアウトしてやり直して下さい。');
        },
        // model 通知情報取得
        get_notice_data: function() {
            var data = {
                system_id: sysId,
                user_id: userId
            };
            socket.emit('notice_client_to_server', data);
            socket.on('notice_server_to_client', function(notice_data) {
                view.render_notice(notice_data);
            });
        },
        // model 勤務状況一覧取得
        get_now_user: function() {
            socket3.emit('system_id', sysId);
            socket3.on('nowusers_server_to_client', function(data) {
                view.render_now_users(data);
            });
        },
        // model モーダル1 用　個人勤務状況を取得
        get_user_status: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/gateway/status',
                data: {
                    user_id: userId
                }
            })
        },
        // model モーダル3 用　シフト情報を取得
        get_user_shift: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/gateway/shift',
                data: {
                    user_id: userId
                }
            })
        },
        // model モーダル2　日付セレクト時　従業員出勤状況の取得
        getUserWorkStatus: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/notice/user_work_status',
                data: {
                    user_id: userId,
                    date: noticeDate
                }
            })
        },
        // model モーダル2 申請送信
        mail_submit: function() {
            return $.ajax({
                type: 'POST',
                url: '../../data/notice/new',
                data: {
                    to_user_id: userId,
                    to_date: noticeDate,
                    notice_flag: notice_flag,
                    notice_in_time: $('#picker_in_time').val(),
                    notice_out_time: $('#picker_out_time').val(),
                    notice_text: $('#memo').val(),
                    user_name: userName,
                    end_date: end_date,
                    noticeHopeData: noticeHopeData
                }
            })
        },
        // model work data
        get_work_data: function(date) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../data/admin_shift/workStatus',
                data: {
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM'),
                    user_id: userId
                }
            })
        },
        get_shift_data: function(date) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../data/admin_shift/table_shift_data',
                data: {
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM'),
                    user_id: userId,
                    flag: 'cal'
                }
            })
        },
        // model 休憩登録
        insert_rest_data: function(flag) {
            var d = new $.Deferred();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/gateway/insert_rest',
                data: {
                    flag: flag,
                    user_id: userId,
                    user_name: userName
                }
            }).done(function(data) {
                if (data.message === 'err') {
                    view.show_err_toast('通信エラー');
                    return d;
                }
                if (flag === 'in') {
                    var toast_title = '休憩を開始します';
                }
                if (flag === 'out') {
                    var toast_title = '休憩を終了します';
                }
                view.show_toast(toast_title + '<br>' + data.message); // トースト表示
                model.get_user().done(function(data) { // 従業員データ取得
                    view.render_user(data); // 従業員データ表示
                    model.get_now_user(); // 勤務状況一覧表示
                });
            }).fail(function() {
                view.show_err_toast('通信エラー');
            }).always(function() {
                d.resolve();
            });
            return d;
        },
    }

    const view = {
        // view 時計表示
        render_time: function() {
            let countDown = function() {
                let nowDatetime = new Date();
                let year = nowDatetime.getFullYear();
                let month = nowDatetime.getMonth() + 1;
                let day = nowDatetime.getDate();
                let week = nowDatetime.getDay();
                let weekStr = ['日', '月', '火', '水', '木', '金', '土'][week];
                let h = nowDatetime.getHours();
                let m = nowDatetime.getMinutes();
                let s = nowDatetime.getSeconds();
                let msec = nowDatetime.getMilliseconds();
                h = ('0' + h).slice(-2);
                m = ('0' + m).slice(-2);
                $('#date_view').text(year + '年' + ('0' + month).slice(-2) + '月' + ('0' + day).slice(-2) + '日' + '(' + weekStr + ')');
                let colon = msec > 499 ? ' ' : ':';
                $('#time_view').text(h + colon + m);
                $('#second').text(('0' + s).slice(-2));
                setTimeout(countDown, 500 - nowDatetime.getMilliseconds() % 500);
            }
            countDown();
        },
        // view パーソナル情報　表示
        render_user: function(data) {
            userName = data.user_name;
            $('#user_id').text(userId);
            $('#user_name').css('color', '#1dbdd2');
            $('#user_name').text(userName);
            $('#user_group').text(data.group1_name + ' ' + data.group2_name + ' ' + data.group3_name);
            if (data.management_flag == 1) {
                $('#input_area').hide();
                $('#user_notice_area').hide();
                $('#mypage_menu_mystate').hide();
                $('#mypage_menu_shift').hide();
                return;
            }
            $('#user_count').text(data.count);
            $('#user_time').text(data.time);
            $('.iziModal-header-title').text(userName); // モーダルヘッダーに名前を表示
            // 出退勤ボタン制御
            if (!data.in_flag || data.in_flag == 0) { // 未出勤
                $('#input_btn').removeClass('disable');
                $('#output_btn').addClass('disable');
                $('.rest-bloc').addClass('disable');
                $('#nonstop_in').removeClass('disable');
                $('#nonstop_out').addClass('disable');
            }
            if (data.in_flag == 1 && data.out_flag == 0) { // 出勤済　未退勤
                $('#input_btn').addClass('disable');
                $('#output_btn').removeClass('disable');
                $('#nonstop_in').addClass('disable');
                $('#nonstop_out').removeClass('disable');

                // 休憩ボタン処理
                if (data.rest_flag != 3) { // flag = 3は、休憩設定なし
                    $('.rest-bloc').addClass('disable');
                    if (!data.auto_rest) { // 2021.09.01 追加　自動休憩なしの場合は　入力可能にする
                        if (data.rest_flag === '' || data.rest_flag === 1) { // 未休憩
                            $('#rest_in_btn').removeClass('disable');
                        }
                        if (data.rest_flag === 0) { // 休憩中
                            $('#rest_out_btn').removeClass('disable');
                            $('#output_btn').addClass('disable');
                        }
                    }
                }
            }
            if (data.in_flag == 1 && data.out_flag == 1) { // 退勤済
                $('#input_btn').addClass('disable');
                $('#output_btn').addClass('disable');
                $('.rest-bloc').addClass('disable');
                $('#nonstop_in').addClass('disable');
                $('#nonstop_out').addClass('disable');
            }
        },
        // view 通知情報　表示
        render_notice: function(data) {
            var non_read_mark = 0;
            var ng_mark = 0;
            $('.notice_area01').html('');
            $('.notice-dashboard').hide();
            $.each(data, function(key, val) {
                if (val.user_id != userId && val.high_user_id.indexOf(String(userId)) < 0) {
                    return true;
                }
                if (val.notice_status == 0) {
                    if (val.user_id == userId) { // 自分の場合
                        var status_text = '承認依頼中';
                        var icon = '<i class="far fa-paper-plane"></i>';
                        var color = 'alert-warning';
                    } else { // 申請依頼
                        var status_text = '申請';
                        var icon = '<i class="fas fa-bell"></i>';
                        var color = 'alert-info';
                    }
                    non_read_mark++;
                }
                if (val.notice_status == 1) {
                    if (val.user_id == userId) { // 自分の場合
                        var status_text = val.from_user_name + 'から<br>承認されました';
                        var icon = '<i class="fas fa-thumbs-up"></i>';
                        var color = 'alert-success';
                    } else { // 申請依頼
                        var status_text = val.from_user_name + 'が<br>承認しました';
                        var icon = '<i class="fas fa-thumbs-up"></i>';
                        var color = 'alert-success';
                    }
                }
                if (val.notice_status == 2) {
                    if (val.user_id == userId) { // 自分の場合
                        var status_text = '申請NGです';
                        var icon = '<i class="fas fa-exclamation-circle"></i>';
                        var color = 'alert-danger';
                    } else { // 申請依頼
                        var status_text = 'NG送信中';
                        var icon = '<i class="fas fa-exclamation-circle"></i>';
                        var color = 'alert-danger';
                    }
                    ng_mark++;
                }
                if (val.notice_flag == 1) {
                    var notice_title1 = '修正依頼';
                    var notice_time = val.before_in_time + '〜' + val.before_out_time + ' を ' + val.notice_in_time + '〜' + val.notice_out_time + ' に時刻修正を申請';
                    var type_color = ' type-color01';
                }
                if (val.notice_flag == 2) {
                    var notice_title1 = '削除依頼';
                    var notice_time = val.before_in_time + '〜' + val.before_out_time + ' を 削除申請';
                    var type_color = ' type-color02';
                }
                if (val.notice_flag == 3) {
                    var notice_title1 = '遅刻依頼';
                    var notice_time = val.notice_in_time + ' 出勤に 遅刻申請';
                    var type_color = ' type-color03';
                }
                if (val.notice_flag == 4) {
                    var notice_title1 = '早退依頼';
                    var notice_time = val.notice_out_time + ' 退勤に 早退申請';
                    var type_color = ' type-color04';
                }
                if (val.notice_flag == 5) {
                    var notice_title1 = '残業依頼';
                    var notice_time = val.notice_out_time + ' まで 残業申請';
                    var type_color = ' type-color05';
                }
                if (val.notice_flag == 6) {
                    var notice_title1 = '有給依頼';
                    var notice_time = '';
                    var type_color = ' type-color06';
                }
                if (val.notice_flag == 7) {
                    var notice_title1 = '欠勤依頼';
                    var notice_time = '';
                    var type_color = ' type-color07';
                }
                if (val.notice_flag == 8) {
                    var notice_title1 = 'その他依頼';
                    var notice_time = '';
                    var type_color = ' type-color08';
                }
                if (val.notice_flag == 11) {
                    var notice_title1 = '休暇依頼';
                    var notice_time = '';
                    var type_color = ' type-color11';
                }
                if (val.user_id == userId) {
                    var user_data_w = '';
                } else {
                    var user_data_w = 'ID:' + val.user_id + ' ' + val.user_name + 'より<br>';
                }
                var nowDatetime = new Date(val.to_date);
                var year = nowDatetime.getFullYear();
                var month = nowDatetime.getMonth() + 1;
                var day = nowDatetime.getDate();
                var week = nowDatetime.getDay();
                var weekStr = ['日', '月', '火', '水', '木', '金', '土'][week];
                var toDate_w = year + '年' + ('0' + month).slice(-2) + '月' + ('0' + day).slice(-2) + '日' + '(' + weekStr + ')';
                if (val.end_date) {
                    var nowDatetime = new Date(val.end_date);
                    var year = nowDatetime.getFullYear();
                    var month = nowDatetime.getMonth() + 1;
                    var day = nowDatetime.getDate();
                    var week = nowDatetime.getDay();
                    var weekStr = ['日', '月', '火', '水', '木', '金', '土'][week];
                    var endDate_w = ' から ' + year + '年' + ('0' + month).slice(-2) + '月' + ('0' + day).slice(-2) + '日' + '(' + weekStr + ')';
                } else {
                    var endDate_w = '';
                }

                // 未読-既読は一旦やめる
                var non_read_w = '';
                // var non_read_flag = 0;
                // for (var i = 0; i < val.notice_text_id.length; i++) {
                //   var read = val.read_users[Number(val.notice_text_id[i])].indexOf(String(userId));
                //   if (read < 0) {
                //     non_read_flag++;
                //   }
                // }
                // if (non_read_flag > 0) {
                //   var non_read_w = '<div class="non-read">未読 ' + non_read_flag + '</div>';
                //   non_read_mark++;
                // } else {
                //   var non_read_w = '';
                // }

                var notice_html = '<div id="' + val.notice_id + '" class="alert ' + color + '"><div class="alert-content"><div class="alert-icon">' + icon + '</div><div class="alert-title"><div class="title">' + status_text + '</div><div class="date">' + val.notice_datetime.slice(0, -3) + '</div></div><div class="notice-type ' + color + type_color + '">' + notice_title1 + '</div><div class="alert-text"><div class="box-main">' + user_data_w + toDate_w + endDate_w + '  <i class="fas fa-angle-double-right"></i> ' + notice_title1 + '<br>' + notice_time + '</div>' + non_read_w + '</div>';

                $('.notice_area01').append(notice_html);

                // if (non_read_w) {
                //   $('.no-data').hide();
                //   var header_menu_html = '<div class="dropdown-item" id="'+ val.notice_id +'">' + icon + "　" + user_data_w + toDate_w + endDate_w + '  <i class="fas fa-angle-double-right"></i> ' + notice_title1 +'</div>';
                //   $('#header_notice').append(header_menu_html);
                // }
                if (notice_html) {
                    $('.notice-dashboard').show();
                }
            });

            $('#notice_count_new, .badge-text1').hide();
            $('#notice_count_ng, .badge-text2').hide();
            if (non_read_mark > 0) {
                $('#notice_count_new, .badge-text1').show();
                $('#notice_count_new').text(non_read_mark);
            }
            if (ng_mark > 0) {
                $('#notice_count_ng, .badge-text2').show();
                $('#notice_count_ng').text(ng_mark);
            }

            // if (non_read_mark > 0) {
            //   $('.notification').show().text(non_read_mark);
            // }
        },
        // view 勤務状況一覧　表示
        render_now_users: function(data) {
            $('#inUserData').html(''); // 表示消去
            $('#outUserData').html('');
            if (!data) {
                return;
            }
            data = data.filter(function(o) { // データを自分と部下のみにする
                return low_users_list.indexOf(o.user_id) >= 0 || userId == o.user_id;
            });
            var in_data = data.filter(function(o) { // データを出勤中と退勤でわける　in_data, out_data
                return Number(o.in_flag) === 1 && Number(o.out_flag) === 0;
            });
            var out_data = data.filter(function(o) {
                return Number(o.in_flag) === 1 && Number(o.out_flag) === 1;
            });

            // 実 - 出退勤時刻で表示
            if (mypage_status_view_flag === 0) {
                in_data.sort(function(a, b) { // 各データをソートする
                    if (a.in_time < b.in_time) return -1;
                    if (a.in_time > b.in_time) return 1;
                    return 0;
                });
                out_data.sort(function(a, b) {
                    if (a.out_time < b.out_time) return -1;
                    if (a.out_time > b.out_time) return 1;
                    return 0;
                });
                in_data.forEach(function(val) { // 出勤中一覧　表示
                    if (val.in_time) { // 実出勤時刻があれば表示
                        $('#inUserData').prepend('<li>' + val.in_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
                    }
                    if (!val.in_time && val.in_work_time) { // 実出勤時刻がなくても修正後出勤時刻があれば表示
                        $('#inUserData').prepend('<li>' + val.in_work_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
                    }
                });
                out_data.forEach(function(val) { // 退勤一覧　表示
                    if (val.out_time) {
                        $('#outUserData').prepend('<li>' + val.out_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
                    }
                    if (!val.out_time && val.out_work_time) {
                        $('#outUserData').prepend('<li>' + val.out_work_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
                    }
                });
            }
            // 修正後 - 出退勤時刻で表示
            if (mypage_status_view_flag === 1) {
                in_data.sort(function(a, b) { // 各データをソートする
                    if (a.in_work_time < b.in_work_time) return -1;
                    if (a.in_work_time > b.in_work_time) return 1;
                    return 0;
                });
                out_data.sort(function(a, b) {
                    if (a.out_work_time < b.out_work_time) return -1;
                    if (a.out_work_time > b.out_work_time) return 1;
                    return 0;
                });
                in_data.forEach(function(val) { // 出勤中一覧　表示
                    $('#inUserData').prepend('<li>' + val.in_work_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
                });
                out_data.forEach(function(val) { // 退勤一覧　表示
                    $('#outUserData').prepend('<li>' + val.out_work_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
                });
            }
        },
        // view モーダルタブ　表示リセット
        reset_tabs: function(area) {
            $('.month_data').html('');
            $(area + ' .tabs .tab').removeClass('active');
            $(area + ' .tabs .tab:first').addClass('active');
            $(area + ' .tab-content').removeClass('show');
            $(area + ' .tab-content:first').addClass('show');
        },
        // view モーダル1　個人勤務状況表示
        render_user_status: function(data) {
            view.reset_tabs('#tabs1');
            for (var i = 0; i < 3; i++) {
                var user_status = Array();
                var date;
                $.each(data[i], function(key, elem) {
                    var color = elem.w == 0 || elem.w == 7 ? 'red' : elem.w == 6 ? 'blue' : '#3e3a39';
                    var bg_color = elem.today_flag == 1 ? ' style="background:rgba(120, 224, 255, .5);"' : '';
                    var in_time = out_time = '';
                    if (mypage_status_view_flag === 0) { // 実 - 出退勤時刻で表示
                        if (elem.in_time) {
                            in_time = elem.in_time;
                        }
                        if (!elem.in_time && elem.in_work_time) { // 実出勤時刻がなくても修正後出勤時刻があれば表示
                            in_time = elem.in_work_time;
                        }
                        if (elem.out_time) {
                            out_time = elem.out_time;
                        }
                        if (!elem.out_time && elem.out_work_time) {
                            out_time = elem.out_work_time;
                        }
                        if (elem.out_time === '未退勤' && elem.out_work_time) {
                            out_time = elem.out_work_time;
                        }
                    }
                    if (mypage_status_view_flag === 1) { // 修正後 - 出退勤時刻で表示
                        var in_time = elem.in_work_time;
                        var out_time = elem.out_work_time;
                    }
                    user_status.push('<tr' + bg_color + '><td>' + elem.day + '</td><td style="color:' + color + ';">' + elem.week + '</td><td>' + in_time + '</td><td>' + out_time + '</td></td>' + elem.memo + '</td><td><td class="mail-sys"><div class="to-mail-btn" data-date="' + elem.date + '">作成</div></td></tr>');
                    date = ' ' + elem.year + '年' + elem.month + '月';
                });
                $("#month_" + i).text(date);
                $('#month_data_' + i).append(user_status.join());
            }
        },
        // view モーダル3　シフト表示
        render_user_shift: function(data) {
            view.reset_tabs('#tabs2');
            for (var i = 0; i < 3; i++) {
                var user_shift = Array();
                var date;
                $.each(data[i], function(key, elem) {
                    var color = elem.w == 0 || elem.w == 7 ? 'red' : elem.w == 6 ? 'blue' : '#3e3a39';
                    var bg_color = elem.today_flag == 1 ? ' style="background:rgba(120, 224, 255, .5);"' : '';
                    user_shift.push('<tr' + bg_color + '><td>' + elem.day + '</td><td style="color:' + color + ';">' + elem.week + '</td><td>' + elem.status + '</td><td>' + elem.in_time + '</td><td>' + elem.out_time + '</td></td></tr>');
                    date = ' ' + elem.year + '年' + elem.month + '月';
                });
                $("#shift_month_" + i).text(date);
                $('#shift_month_data_' + i).append(user_shift.join());
            }
        },
        // view モーダル2（修正依頼）　初期設定
        render_modal2: function() {
            if (!calendar1) {
                calendar1 = flatpickr("#modal_date", { // カレンダー表示
                    inline: true,
                    defaultDate: 'today',
                    onReady: function(selectedDates, dateStr, instance) {
                        noticeDate = dateStr;
                        view.rendar_modal2_date();
                        view.rendar_modal2_status(); // 出勤状況　表示
                    },
                    onChange: function(selectedDates, dateStr, instance) {
                        noticeDate = dateStr;
                        view.rendar_modal2_date();
                        if (end_date === '') { // 有給のend_dateがある場合は、出勤状況は取得しない
                            view.rendar_modal2_status(); // 出勤状況　表示
                        }
                    }
                });
            } else {
                var now = new Date();
                calendar1.setDate(now);
                noticeDate = formatDate(now, 'YYYY-MM-DD');
                view.rendar_modal2_date();
            }
            timepicker_in = flatpickr('#picker_in_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: defaultInHour,
                defaultMinute: defaultInMinute,
                minuteIncrement: defaultMinuteIncrement,
                onChange: function() {
                    view.render_modal2_check();
                },
                onClose: function() {
                    view.render_modal2_check();
                }
            });
            timepicker_out = flatpickr('#picker_out_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: defaultOutHour,
                defaultMinute: defaultOutMinute,
                minuteIncrement: defaultMinuteIncrement,
                onChange: function() {
                    view.render_modal2_check();
                },
                onClose: function() {
                    view.render_modal2_check();
                }
            });
            $('#modal2').iziModal('open'); // モーダル表示
        },
        // view モーダル2 日付表示
        rendar_modal2_date: function() {
            if (noticeDate.length > 10) { // 有給複数選択時
                end_date = noticeDate.slice(-10);
                noticeDate = noticeDate.slice(0, 10);
            } else {
                end_date = '';
            }
            var date = new Date (noticeDate);
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();
            var dayOfWeek = date.getDay();
            var dayOfWeekStr = [ "日", "月", "火", "水", "木", "金", "土" ][dayOfWeek] ;
            var dateW = year+'年'+month+'月'+day+'日'+'('+dayOfWeekStr+')';
            $('#calendar_date').css('font-size', '25px');
            if (end_date !== '') { // 有給 end_dateがある場合
                var endDate = new Date (end_date);
                var endYear = endDate.getFullYear();
                var endMonth = endDate.getMonth() + 1;
                var endDay = endDate.getDate();
                var endDayOfWeek = endDate.getDay();
                var endDayOfWeekStr = [ "日", "月", "火", "水", "木", "金", "土" ][dayOfWeek] ;
                dateW += 'から<br>'+endYear+'年'+endMonth+'月'+endDay+'日'+'('+endDayOfWeekStr+')';
                $('#calendar_date').css('font-size', '15px');
            }
            $('#calendar_date').html(dateW);
            if (end_date === '') { // 有給 end_dateがある場合は処理しない
                notice_flag = '';
                $('.select-notice').hide();
                $('.time-edit-area').hide();
                $('.notice-comment-area').hide();
                $('#paid_hour').hide();
                $('.notice-btn').removeClass('disabled');
                var option = {mode: 'single', maxDate: null, minDate: null};
                if (calendar1) {
                    calendar1.set(option);
                }
                var now_date = formatDate(new Date(), 'YYYY-MM-DD');
                $('.past, future').removeClass('disabled');
                if (noticeDate === now_date) {
                    $('#today_mark').addClass('today');
                    $('#today_mark').text('本日');
                    $('.past, .future').removeClass('disabled');
                } else {
                    $('#today_mark').removeClass('today');
                    var diff_day = Math.ceil((date - new Date()) / 86400000);
                    if (diff_day > 0) {
                        $('#today_mark').text(diff_day + '日後');
                        $('.past').addClass('disabled');
                    } else {
                        $('#today_mark').text(Math.abs(diff_day) + '日前');
                        $('.future').addClass('disabled');
                    }
                }
                var options = {era: 'long', year: 'numeric'};
                var wareki = new Intl.DateTimeFormat('ja-JP-u-ca-japanese', options).format(date);
                $('#date-area-wareki').text(wareki);
            }
        },
        // view モーダル２　出勤状況　表示
        rendar_modal2_status: function() {
            model.getUserWorkStatus().done(function(data) {
                noticeHopeData = data;
                if (data.shift['status'] !== '') {
                    var shiftStatus = ['出勤', '公休', '有給'];
                    var shiftStatusW = shiftStatus[data.shift['status']];
                } else {
                    var shiftStatusW = '未定';
                }
                $('#shiftStatus').text(shiftStatusW);
                if (data.shift['status'] == 0 && data.shift['in_time'] && data.shift['out_time']) {
                    $('.shift-time').show();
                    $('#shift_in_time').text(data.shift['in_time'].substr(0, 5));
                    $('#shift_out_time').text(data.shift['out_time'].substr(0, 5));
                } else {
                    $('.shift-time').hide();
                }
                $('#timeStatus').text('未出勤');
                $('#in_time').text('--');
                $('#out_time').text('--');
                timepicker_in.clear();
                timepicker_out.clear();
                var in_time = '';
                var out_time = '';
                if (data.time) {
                    $('#timeStatus').text(data.time.status);
                    if (data.time['in_time']) {
                        in_time = data.time['in_time'].substr(0, 5);
                        $('#in_time').text(in_time);
                    }
                    if (data.time['out_time']) {
                        out_time = data.time['out_time'].substr(0, 5);
                        $('#out_time').text(out_time);
                    }
                    timepicker_in.setDate(in_time);
                    timepicker_out.setDate(out_time);
                }
                // 出勤情報が無い場合は削除依頼を停止
                if (in_time === '') {
                    $("button[data-id='2']").addClass('disabled');
                }
                // // 出勤情報がある場合は遅刻依頼を停止
                // if (in_time !== '') {
                //   $("button[data-id='3']").addClass('disabled');
                // }
                // // 退勤情報がある場合は早退依頼を停止
                // if (out_time !== '') {
                //   $("button[data-id='4']").addClass('disabled');
                // }
            })
        },
        // view モーダル2 ステータス条件で表示切替
        render_modal2_status_change: function() {
            view.rendar_modal2_status();
            calendar1.setDate(noticeDate);
            var option = {mode: 'single', maxDate: null, minDate: null}
            // 修正依頼
            if (notice_flag === 1) {
                $('#in_time_input_area').show();
                $('#out_time_input_area').show();
                $('.time-edit-area').show();
            }
            // 削除依頼
            if (notice_flag === 2) {
                $('.notice-comment-area').show();
                $('#noticeComment').text('時刻の削除を依頼する');
            }
            // 遅刻依頼
            if (notice_flag === 3) {
                $('#in_time_input_area').show();
                $('#out_time_input_area').hide();
                $('.time-edit-area').show();
            }
            // 早退依頼
            if (notice_flag === 4) {
                $('#in_time_input_area').hide();
                $('#out_time_input_area').show();
                $('.time-edit-area').show();
            }
            // 残業依頼
            if (notice_flag === 5) {
                $('#in_time_input_area').hide();
                $('#out_time_input_area').show();
                $('.time-edit-area').show();
            }
            // 欠勤依頼
            if (notice_flag === 7) {
                $('.notice-comment-area').show();
                $('#noticeComment').text('欠勤を依頼する');
            }
            // 休暇依頼
            if (notice_flag === 11) {
                $('.notice-comment-area').show();
                $('#noticeComment').text('休暇を依頼する');
            }
            // その他依頼
            if (notice_flag === 8) {
                $('.notice-comment-area').show();
                $('#noticeComment').text('その他の依頼をする');
            }
            // 有給依頼
            if (notice_flag === 6) {
                if (calendar_term === 9) {
                    option = {mode: 'range', maxDate: null, minDate: null}
                }
                if (calendar_term === 1) { //
                    option = {mode: 'range', maxDate: null, minDate: new Date()}
                }
                $('.notice-comment-area').show();
                $('#paid_hour').show();
                $('#noticeComment').text('有給を依頼する');
            }
            // カレンダーモードセット
            calendar1.set(option);
            view.render_modal2_check();
        },
        // view モーダル2 バリデーション
        render_modal2_check: function() {
            $('#time_edit_submit').addClass('disable');
            if (notice_flag === '') {
                return;
            }
            if ($('#memo').val() === '') {
                return;
            }
            if (notice_flag === 1) {
                var in_time = $('#picker_in_time').val();
                var out_time = $('#picker_out_time').val();
                if (!in_time && !out_time) {
                    return;
                }
            }
            $('#time_edit_submit').removeClass('disable');
        },
        // view トースト表示　通常
        show_toast: function(message) {
            siiimpleToast.message(message, {
                position: 'top|right'
            });
        },
        // view トースト表示　エラー
        show_err_toast: function(message) {
            siiimpleToast.alert(message, {
                position: 'top|right'
            });
        },
    }

    $(function() {
        view.render_time(); // 時計表示
        model.get_user().done(function(data) { // 従業員データ取得
            view.render_user(data); // 従業員データ表示

            // シフトアラート
            if (mypage_shift_alert == 1 && data.shift_alert_flag == 0) {
                var now = new Date();
                var year = now.getFullYear();
                var month = now.getMonth()+1;
                var day = now.getDate();
                var d = new Date(year, month, 0);
                var last_day = d.getDate();
                model.get_shift_data(now).done(function(data) {
                    var shift_in = data.filter(function(element) {
                        return element.title != '・未登録'
                    });
                    if (shift_in.length < last_day) {
                        view.show_err_toast('今月のシフトが登録されてません！');
                    }
                });
                if (shift_closing_day == 0) {
                    shift_closing_day = last_day;
                }
                if (shift_closing_day <= day) {
                    var next_date = now.setMonth(now.getMonth() + 1);
                    var next_year = formatDate(new Date(next_date), 'YYYY');
                    var next_month = formatDate(new Date(next_date), 'MM');
                    var next_d = new Date(next_year, next_month, 0);
                    var nexg_last_day = next_d.getDate();
                    model.get_shift_data(next_date).done(function(data) {
                        var next_shift_in = data.filter(function(element) {
                            return element.title != '・未登録'
                        });
                        if (next_shift_in.length < nexg_last_day) {
                            view.show_err_toast('来月のシフト登録期限日もしくは過ぎてます！');
                        }
                    });
                }
            }
        });
        model.get_notice_data(); // 通知データ取得
        if (low_user === 1) {
            model.get_now_user(); // 上長の場合、部下の勤務状況を取得
        }
        // setInterval(model.get_notice_data(), 3000);
        // サイドバー用
        $(document).on('click', '.navbar-toggler', function() {
            if (mobile_menu_visible === 1) {
                $('html').removeClass('nav-open');
                $('.close-layer').remove();
                setTimeout(function() {
                    $(this).removeClass('toggled');
                }, 400);
                mobile_menu_visible = 0;
            } else {
                setTimeout(function() {
                    $(this).addClass('toggled');
                }, 430);
                var $layer = $('<div class="close-layer"></div>');
                if ($('body').find('.main-panel').length != 0) {
                    $layer.appendTo(".main-panel");
                } else if (($('body').hasClass('off-canvas-sidebar'))) {
                    $layer.appendTo(".wrapper-full-page");
                }
                setTimeout(function() {
                    $layer.addClass('visible');
                }, 100);
                $layer.click(function() {
                    $('html').removeClass('nav-open');
                    mobile_menu_visible = 0;
                    $layer.removeClass('visible');
                    setTimeout(function() {
                        $layer.remove();
                        $(this).removeClass('toggled');
                    }, 400);
                });
                $('html').addClass('nav-open');
                mobile_menu_visible = 1;
            }
        });
        // // タブ操作
        // $(document).on('click', '.nav-link', function() {
        //   $('.tab-pane, .nav-link').removeClass('active');
        //   $(this).addClass('active');
        //   $('#' + $(this).attr('data-tab')).addClass('active');
        // });
        $('#modal1, #modal3').iziModal({ // モーダル設定
            headerColor: '#1591a2',
            focusInput: false,
            onOpened: function() {
                modal_open = 1;
            },
            onClosed: function() {
                modal_open = 0;
            }
        });
        $('#modal2').iziModal({
            headerColor: '#1591a2',
            focusInput: false,
            width: 750,
            onOpened: function() {
                modal_open = 1;
                if (!noticeDate) {
                    var now = new Date();
                    calendar1.setDate(now);
                    noticeDate = formatDate(now, 'YYYY-MM-DD');
                }
                view.rendar_modal2_date();
                view.render_modal2_status_change();
            },
            onClosed: function() {
                modal_open = 0;
                $('#memo').val("");
            }
        });

        // 申請依頼ボタン
        $('#dashboard_status_btn').on('click touchstart', function(e) { // 個人勤務状況ボタン modal_1　クリック
            e.preventDefault();
            model.get_user_status().done(function(data) {
                view.render_user_status(data);
                $('#modal1').iziModal('open'); // モーダル表示
            }).fail(function(data) {
                view.show_err_toast('通信エラー');
            });
        });
        $('#dashboard_notice_btn').on('click', function() { // 申請依頼ボタン modal_2　クリック
            view.render_modal2();
        });
        $('#dashboard_shift_btn').on('click touchstart', function(e) { // シフト表示ボタン modal_3　クリック
            e.preventDefault();
            model.get_user_shift().done(function(data) {
                view.render_user_shift(data);
                $('#modal3').iziModal('open');
            }).fail(function(data) {
                view.show_err_toast('通信エラー');
            });
        });

        // 出退勤入力
        $('#input_btn').on('click touchstart', function(e) { // 出勤ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_data('in');
        });
        $('#output_btn').on('click touchstart', function(e) { // 退勤ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_data('out');
        });
        // 休憩入力
        $('#rest_in_btn').on('click touchstart', function(e) { // 休憩開始ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_rest_data('in');
        });
        $('#rest_out_btn').on('click touchstart', function(e) { // 休憩終了ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_rest_data('out');
        });
        $('#nonstop_in').on('click touchstart', function(e) { // 直行出勤ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_data('nonstop_in');
        });
        $('#nonstop_out').on('click touchstart', function(e) { // 直帰退勤ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_data('nonstop_out');
        });

        // モーダル 操作
        $(document).delegate('.tabs li', 'click', function() {// モーダル 1 3　タブ操作
            var index = $(this).parent().parent().find('.tabs li').index(this);
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            $(this).parent().parent().find('.tab-content').removeClass('show').eq(index).addClass('show');
        });
        $(document).delegate('.to-mail-btn', 'click', function() { // モーダル1　依頼ボタン
            // $('#modal1').iziModal('close');
            view.render_modal2();
            noticeDate = $(this).attr('data-date');
            setTimeout(function() {
                calendar1.setDate(noticeDate);
                view.rendar_modal2_date();
                view.render_modal2_status_change();
            }, 50);
        });
        $(document).delegate('.notice-btn', 'click', function() { // モーダル 2 依頼ボタンクリック
            view.rendar_modal2_date();
            $('.select-notice').show();
            $(this).addClass('disabled');
            var name = $(this).text();
            $('#modal2 .iziModal-header-subtitle').text(name);
            notice_flag = Number($(this).attr('data-id'));
            calendar_term = Number($(this).attr('data-term'));
            $('#selectNotice').text(name);
            view.render_modal2_status_change();
        });
        $(document).delegate('#today_mark', 'click', function() { // modal2 本日ボタン
            var now = new Date();
            calendar1.setDate(now);
            noticeDate = formatDate(now, 'YYYY-MM-DD');
            view.rendar_modal2_date();
            view.render_modal2_status_change();
        });
        $(document).delegate('#picker_del_in_time', 'click', function() {
            timepicker_in.clear();
        });
        $(document).delegate('#picker_del_out_time', 'click', function() {
            timepicker_out.clear();
        });
        $('#memo').keyup(function() { // modal2 コメント変更時
            view.render_modal2_check();
        });
        $(document).delegate('#time_edit_submit', 'click', function() { // modal2 依頼送信ボタン　クリック
            $(this).addClass('disable');
            model.mail_submit().done(function(data) {
            if (data == 'ok') {
                $('#modal2').iziModal('close', {
                transition: 'bounceOutUp'
            });
            model.get_notice_data(); // 通知データ取得
                return;
            } else {
                view.show_mail_error('通信エラー');
                return;
            }
            }).fail(function(data) {
                view.show_mail_error('通信エラー');
                return;
            });
        });

        // 通知クリック
        $(document).on('click', '.alert', function() {
            Cookies.set('notice_id', $(this).attr('id'));
            location.href = '/mypage_notice';
        });
        // // header menu クリック
        // $(document).on('click', '.header-menu-btn', function() {
        //   $(this).next('.dropdown-menu').toggleClass('show');
        // });
        //
        // $(document).click(function(event) {
        //   if(!$(event.target).closest('.dropdown-menu').length && !$(event.target).closest('.header-menu-btn').length) {
        //     $('.dropdown-menu').removeClass('show');
        //   }
        // });
    });
}());
