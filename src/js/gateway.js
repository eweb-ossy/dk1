(function() {

    var max = $('#user_id').attr('data-num'); // 最大入力ID数
    var inputKey = ''; // 入力ID
    var messageFlag = Number($('#input_area').attr('data-message')); // メッセージ表示フラグ
    var timer = []; // 表示タイマー
    var userId; // 従業員ID
    var userName; // 従業員名
    var modal_open = 0;
    var calendar1;
    var calendar_term;
    var end_date = '';
    var notice_flag = 1;
    var info = '';
    var latitude = '';
    var longitude = '';
    var noticeDate = '';
    var timepicker_in; // モーダル用picker
    var timepicker_out; // モーダル用picker
    var noticeHopeData = {};

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

    // MODELS
    var model = {
        // now datetime
        getNowdateTime: function() {
            return axios.get(
                '../../data/nowDateTime/'
            );
        },
        // // model 勤務状況一覧取得
        // get_now_user: function() {
        //     socket.emit('system_id', sysId);
        //     socket.on('nowusers_server_to_client', function(data) {
        //         view.render_now_users(data);
        //     });
        // },
        // model パーソナル情報取得
        get_user: function(inputKey) {

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
                    user_id: inputKey
                }
            })
        },
        // model 出退勤登録
        insert_data: function(flag) {
            if (gps_flag === 0 || (gps_flag === 1 && info === '取得') || (gps_flag === 2 && agent !== 'pc' && info === '取得') || (gps_flag === 2 && agent === 'pc')) {
                var d = new $.Deferred();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '../../data/gateway/insert',
                    data: {
                        flag: flag,
                        user_id: userId,
                        user_name: userName,
                        area_id: area_id,
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
                        if (message_in_flag == 1) {
                            $('#modal4').iziModal('open');
                            view.clearTimer();
                            $('#message_in').trigger('focus');
                        } 
                    }
                    if (flag === 'out') {
                        var toast_title = 'おつかれさまです';
                        if (message_out_flag == 1) {
                            $('#modal5').iziModal('open');
                            view.clearTimer();
                            $('#message_out').trigger('focus');
                        } 
                    }
                    view.show_toast(toast_title + '<br>' + data.message); // トースト表示
                    view.render_id_clear(); // 表示数値キー削除
                }).fail(function(jqXHR) {
                    view.show_err_toast('通信エラー');
                }).always(function() {
                    d.resolve();
                });
                return d;
            } else {
                var flag_text = flag === 'in' ? '出勤' : '退勤';
                view.show_err_toast('位置情報取得エラー：'+info+'<br><br>'+flag_text+'できませんでした。<br><br>もう一度'+flag_text+'をするか、ログアウトしてやり直して下さい。');
            }       
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
                view.render_id_clear(); // 表示数値キー削除
            }).fail(function() {
                view.show_err_toast('通信エラー');
            }).always(function() {
                d.resolve();
            });
            return d;
        },
        // model メッセージ登録
        insert_message_data: function(type, message) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/gateway/insert_message',
                data: {
                    user_id: userId,
                    type: type,
                    message: message
                }
            }).done(function(data) {
                if (data.message === 'err') {
                    view.show_err_toast('通信エラー');
                }
                if (data.message === 'ok') {
                    view.show_toast('メッセージを送信しました');
                }
            })
        }
    }

    // VIEWS
    var view = {
        // view 時計表示
        render_time: function() {
            var dateView = document.getElementById('date_view');
            var timeView = document.getElementById('time_view');
            var secView = document.getElementById('second');
            var countDown = function() {
                var nowDatetime = new Date();
                var year = nowDatetime.getFullYear();
                var month = nowDatetime.getMonth() + 1;
                var day = nowDatetime.getDate();
                var week = nowDatetime.getDay();
                var weekStr = ['日', '月', '火', '水', '木', '金', '土'][week];
                var h = nowDatetime.getHours();
                var m = nowDatetime.getMinutes();
                var s = nowDatetime.getSeconds();
                var msec = nowDatetime.getMilliseconds();
                h = ('0' + h).slice(-2);
                m = ('0' + m).slice(-2);
                var colon;
                dateView.textContent = year + '年' + ('0' + month).slice(-2) + '月' + ('0' + day).slice(-2) + '日' + '(' + weekStr + ')';
                if (msec > 499) {
                    colon = ' ';
                } else {
                    colon = ':';
                }
                timeView.textContent = h + colon + m;
                secView.textContent = ('0' + s).slice(-2);
                setTimeout(countDown, 500 - nowDatetime.getMilliseconds() % 500);
            }
            countDown();
        },
        // view 時計表示2
        rendarDateTime: function() {
            var dateView = document.getElementById('date_view');
            var timeView = document.getElementById('time_view');
            var secView = document.getElementById('second');
            var countDown = function() {
                model.getNowdateTime().then(function(data) {
                    dateView.textContent = data.data.date_ja + '(' + data.data.week + ')';
                    var colon = (data.data.msec <= 500) ? ':' : ' ';
                    timeView.textContent = data.data.hour_w + colon + data.data.minute_w;
                    secView.textContent = data.data.second_w;
                    setTimeout(countDown, 500 - data.data.second % 500);
                });
            }
            countDown();
        },
        // view 入力数値キー表示
        render_id: function(key) {
            if (inputKey.length < max) {
                inputKey += key;
            }
            $('#user_id').text(inputKey);
        },
        // view 表示数値キー削除
        render_id_clear: function() {
            inputKey = '';
            $('#user_id').text('　');
            $('.user_data').text('　');
            $('.header-btn').addClass('disable');
            $('#input_btn').addClass('disable');
            $('#output_btn').addClass('disable');
            $('.rest-bloc').addClass('disable');
            if (messageFlag === 1) {
                $('#input_area').addClass('display-no');
                $('#message_area').removeClass('display-no');
            }
        },
        // view 表示数値キー１文字削除　バックスペース
        render_id_del: function() {
            if (inputKey.length > 0) {
                inputKey = inputKey.slice(0, -1);
                if (inputKey.length === 0) {
                    $('#user_id').text('　');
                } else {
                    $('#user_id').text(inputKey);
                }
            }
        },
        // view 表示タイマー
        time_user_clear: function(time) {
            var timer_id = setTimeout(function() {
                view.render_id_clear();
            }, time);
            timer.push(timer_id);
        },
        // view 従業員情報　表示
        render_user: function(data) {
            userId = Number(inputKey);
            inputKey = '';
            $('#user_id').text('　');
            view.clearTimer();
            // var time_user_clear = function(time) { // 表示タイマー
            //   var timer_id = setTimeout(function() {
            //     view.render_id_clear();
            //   }, time);
            //   timer.push(timer_id);
            // }
            if (Object.keys(data).length !== 0 && data.management_flag != 1) { // 従業員データがある場合は表示
                userName = data.user_name;
                $('#user_name').css('color', '#357788').text(userName);
                $('#group1').text(data.group1_name);
                $('#group2').text(data.group2_name);
                $('#group3').text(data.group3_name);
                $('#count').text(data.count);
                $('#time').text(data.time);
                $('.header-btn').removeClass('disable');

                // 出退勤ボタン表示
                if (!data.in_flag || data.in_flag === 0) { // 未出勤
                    $('#input_btn').removeClass('disable');
                    $('#output_btn').addClass('disable');
                    $('.rest-bloc').addClass('disable');
                }
                if (data.in_flag === 1 && data.out_flag === 0) { // 出勤済　未退勤
                    $('#input_btn').addClass('disable');
                    $('#output_btn').removeClass('disable');

                    // 休憩ボタン処理
                    if (data.rest_flag !== 3) { // flag = 3は、休憩設定なし
                        $('.rest-bloc').addClass('disable');
                        if (!data.auto_rest) { // 2021.09.01 追加　自動休憩なしの場合は　入力可能にする
                            if (data.rest_flag === 1) { // 未休憩
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
                }

                if (messageFlag === 1) { // メッセージ表示の場合はインプット表示へ切り替え
                    $('#input_area').removeClass('display-no');
                    $('#message_area').addClass('display-no');
                }
                $('.iziModal-header-title').text(userName); // モーダルヘッダーに名前を表示
                view.time_user_clear(10000); // 10秒後表示クリア
            } else {
            // 従業員情報がない場合
                $('.user_data').text('　');
                $('#user_name').css('color', '#e65d5d');
                $('#user_name').text('未登録');
                if (messageFlag === 1) { // メッセージ表示の場合はメッセージ表示へ切り替え
                    $('#input_area').addClass('display-no');
                    $('#message_area').removeClass('display-no');
                }
                view.time_user_clear(5000); // 5秒後表示クリア
            }
        },
        // // view 勤務状況一覧　表示
        // render_now_users: function(data) {
        //     $('#inUserData').html(''); // 表示消去
        //     $('#outUserData').html('');
        //     if (!data) {
        //         return;
        //     }
        //     if (area_id > 0) { // エリアソート
        //         var area_data = data.filter(function(o) {
        //             return Number(o.area_id) == area_id;
        //         });
        //         var in_data = area_data.filter(function(o) { // データを出勤中と退勤でわける　in_data, out_data
        //             return Number(o.in_flag) === 1 && Number(o.out_flag) === 0;
        //         });
        //         var out_data = area_data.filter(function(o) {
        //             return Number(o.in_flag) === 1 && Number(o.out_flag) === 1;
        //         });
        //     }
        //     if (area_id === 0) { // エリアソートなしの場合
        //         var in_data = data.filter(function(o) { // データを出勤中と退勤でわける　in_data, out_data
        //             return Number(o.in_flag) === 1 && Number(o.out_flag) === 0;
        //         });
        //         var out_data = data.filter(function(o) {
        //             return Number(o.in_flag) === 1 && Number(o.out_flag) === 1;
        //         });
        //     }

        //     // 実 - 出退勤時刻で表示
        //     if (gateway_status_view_flag === 0) {
        //         in_data.sort(function(a, b) { // 各データをソートする
        //             if (a.in_time < b.in_time) return -1;
        //             if (a.in_time > b.in_time) return 1;
        //             return 0;
        //         });
        //         out_data.sort(function(a, b) {
        //             if (a.out_time < b.out_time) return -1;
        //             if (a.out_time > b.out_time) return 1;
        //             return 0;
        //         });
        //         in_data.forEach(function(val) { // 出勤中一覧　表示
        //             if (val.in_time) { // 実出勤時刻があれば表示
        //                 $('#inUserData').prepend('<li>' + val.in_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
        //             }
        //             if (!val.in_time && val.in_work_time) { // 実出勤時刻がなくても修正後出勤時刻があれば表示
        //                 $('#inUserData').prepend('<li>' + val.in_work_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
        //             }
        //         });
        //         out_data.forEach(function(val) { // 退勤一覧　表示
        //         if (val.out_time) {
        //             $('#outUserData').prepend('<li>' + val.out_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
        //         }
        //         if (!val.out_time && val.out_work_time) {
        //             $('#outUserData').prepend('<li>' + val.out_work_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
        //         }
        //         });
        //     }
        //     // 修正後 - 出退勤時刻で表示
        //     if (gateway_status_view_flag === 1) {
        //         in_data.sort(function(a, b) { // 各データをソートする
        //             if (a.in_work_time < b.in_work_time) return -1;
        //             if (a.in_work_time > b.in_work_time) return 1;
        //             return 0;
        //         });
        //         out_data.sort(function(a, b) {
        //             if (a.out_work_time < b.out_work_time) return -1;
        //             if (a.out_work_time > b.out_work_time) return 1;
        //             return 0;
        //         });
        //         in_data.forEach(function(val) { // 出勤中一覧　表示
        //             $('#inUserData').prepend('<li>' + val.in_work_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
        //         });
        //         out_data.forEach(function(val) { // 退勤一覧　表示
        //             $('#outUserData').prepend('<li>' + val.out_work_time.substr(0, 5) + ' ' + val.name_sei + ' ' + val.name_mei + '</li>');
        //         });
        //     }

        // },
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
                    if (gateway_status_view_flag === 0) { // 実 - 出退勤時刻で表示
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
                    if (gateway_status_view_flag === 1) { // 修正後 - 出退勤時刻で表示
                        var in_time = elem.in_work_time;
                        var out_time = elem.out_work_time;
                    }
                    if (gateway_mail_flag === 1) { // 修正依頼フラグ
                        user_status.push('<tr' + bg_color + '><td>' + elem.day + '</td><td style="color:' + color + ';">' + elem.week + '</td><td>' + in_time + '</td><td>' + out_time + '</td></td>' + elem.memo + '</td><td><td class="mail-sys"><div class="to-mail-btn" data-date="' + elem.date + '">作成</div></td></tr>');
                        date = ' ' + elem.year + '年' + elem.month + '月';
                    } else {
                        user_status.push('<tr' + bg_color + '><td>' + elem.day + '</td><td style="color:' + color + ';">' + elem.week + '</td><td>' + in_time + '</td><td>' + out_time + '</td></td>' + elem.memo + '</td><td></tr>');
                        date = ' ' + elem.year + '年' + elem.month + '月';
                    }
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
        // view トースト表示　通常
        show_toast: function(message) {
            alertify.set('notifier','delay', 5);
            alertify.set('notifier','position', 'top-right');
            alertify.notify(message);
        },
        // view トースト表示　エラー
        show_err_toast: function(message) {
            alertify.set('notifier','delay', 10);
            alertify.set('notifier','position', 'top-right');
            alertify.error(message);
        },
        hover_num_key: function(input_key) {
            if (input_key === 'clear') {
                var hover_key = '#num_clear div';
            } else if (input_key === 'enter') {
                var hover_key = '#submit_userid div';
            } else {
                var hover_key = '#num_' + input_key;
            }
            $(hover_key).toggleClass('hovered');
            setTimeout(function() {
                $(hover_key).toggleClass('hovered');
            }, 500);
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
        // view clear timer
        clearTimer: function() {
            if (timer.length > 0) {
                for (var i = 0; i < timer.length; i++) {
                clearTimeout(timer[i]);
            }
                timer = [];
            }
        }
    }

    let gateway_mail_flag, area_id, gps_flag, agent, defaultInHour, defaultInMinute, defaultOutHour, defaultOutMinute, defaultMinuteIncrement;
    $(function() {
        $.ajax({
            dataType: 'json',
            url: '../../data/gateway/init'
        }).done(function(data) {
            gateway_mail_flag = Number(data.gateway_mail_flag);
            area_id = data.area_id;
            gps_flag = Number(data.gps_flag);
            agent = data.agent;
            let edit_in_time = data.edit_in_time;
            let edit_out_time = data.edit_out_time;
            defaultInHour = edit_in_time ? edit_in_time.slice(0, 2) : null;
            defaultInMinute = edit_in_time ? edit_in_time.slice(3, 2) : null;
            defaultOutHour = edit_out_time ? edit_out_time.slice(0, 2) : null;
            defaultOutMinute = edit_out_time ? edit_out_time.slice(3, 2) : null;
            defaultMinuteIncrement = data.edit_min ? data.edit_min  : 1;
            // 時計表示
            // view.rendarDateTime(); // 時計表示サーバ
            view.render_time(); // 時計表示クライアント
            // model.get_now_user();
            $('#modal1, #modal3').iziModal({ // モーダル設定
                headerColor: '#1591a2',
                focusInput: false,
                onOpened: function() {
                    modal_open = 1;
                },
                onClosed: function() {
                    modal_open = 0;
                    view.time_user_clear(10000); // 10秒後表示クリア
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
                if (timer.length > 0) {
                    view.clearTimer();
                }
                },
                onClosed: function() {
                    modal_open = 0;
                    view.time_user_clear(10000); // 10秒後表示クリア
                    $('#memo').val("");
                }
            });
            $('#modal4, #modal5').iziModal({
                headerColor: '#1591a2',
                focusInput: false,
                onOpened: function() {
                    modal_open = 1;
                },
                onClosed: function() {
                    modal_open = 0;
                    $('#message_in').val("");
                    $('#message_out').val("");
                    view.time_user_clear(1000); // 1秒後表示クリア
                }
            });
        });

        // キーボード入力
        $(document).on('keypress', function(e) { // 数値キー
            if (modal_open === 1) { // モーダル表示時
                return;
            }
            e.preventDefault();
            var input_key = 99;
            switch (e.which) {
                case 49:
                input_key = 1;
                break;
                case 97:
                input_key = 1;
                break;
                case 50:
                input_key = 2;
                break;
                case 98:
                input_key = 2;
                break;
                case 51:
                input_key = 3;
                break;
                case 99:
                input_key = 3;
                break;
                case 52:
                input_key = 4;
                break;
                case 100:
                input_key = 4;
                break;
                case 53:
                input_key = 5;
                break;
                case 101:
                input_key = 5;
                break;
                case 54:
                input_key = 6;
                break;
                case 102:
                input_key = 6;
                break;
                case 55:
                input_key = 7;
                break;
                case 103:
                input_key = 7;
                break;
                case 56:
                input_key = 8;
                break;
                case 104:
                input_key = 8;
                break;
                case 57:
                input_key = 9;
                break;
                case 105:
                input_key = 9;
                break;
                case 48:
                input_key = 0;
                break;
                case 96:
                input_key = 0;
                break;
            }
            if (input_key !== 99) {
                view.render_id(input_key);
            }
            view.hover_num_key(input_key); // key hover 表示
        });
        hotkeys('clear', function() { // clearキー
            view.render_id_clear();
            view.hover_num_key('clear'); // key hover 表示
        });
        hotkeys('backspace', function() { // backspaceキー
            view.render_id_del();
        });
        hotkeys('enter', function() { // enterキー
            view.hover_num_key('enter'); // key hover 表示
            if (inputKey) {
                model.get_user(inputKey).done(function(data) {
                    view.render_user(data);
                }).fail(function(data) {
                    view.show_err_toast('通信エラー');
                });
            }
        });

        // タップ操作入力
        $('.num-btn').on('click', function(e) { // 数値キー
            e.preventDefault();
            var key = $(this).children('div').text();
            view.render_id(key);
        });
        $('#num_clear').on('click', function(e) { // 訂正ボタン
            e.preventDefault();
            view.render_id_clear();
        });
        $('#submit_userid').on('click', function(e) { // 確定ボタン
            e.preventDefault();
            if (inputKey) {
                model.get_user(inputKey).done(function(data) {
                    view.render_user(data);
                }).fail(function() {
                    view.show_err_toast('通信エラー');
                });
            }
        });

        // 出退勤入力
        $('#input_btn').on('click', function(e) { // 出勤ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_data('in');
            // if (message_in_flag == 1) {
            //   $('#modal4').iziModal('open');
            //   view.clearTimer();
            //   $('#message_in').trigger('focus');
            // } 
        });
        $('#output_btn').on('click', function(e) { // 退勤ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_data('out');
            // if (message_out_flag == 1) {
            //   $('#modal5').iziModal('open');
            //   view.clearTimer();
            //   $('#message_out').trigger('focus');
            // } 
        });
        // 休憩入力
        $('#rest_in_btn').on('click', function(e) { // 休憩開始ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_rest_data('in');
        });
        $('#rest_out_btn').on('click', function(e) { // 休憩終了ボタン
            e.preventDefault();
            $(this).addClass('disable');
            model.insert_rest_data('out');
        });

        // 上部ボタン入力 モーダル open
        $('#status_btn').on('click', function(e) { // 個人勤務状況ボタン modal_1　クリック
            e.preventDefault();
            model.get_user_status().done(function(data) {
                view.render_user_status(data);
                $('#modal1').iziModal('open'); // モーダル表示
                // window.clearTimeout(timer); // 表示クリア　ストップ
                view.clearTimer();
            }).fail(function(data) {
                view.show_err_toast('通信エラー');
            });
        });
        $(document).on('click', '#mail_btn', function() { // 修正依頼ボタン modal_2　クリック
            view.render_modal2();
            // window.clearTimeout(timer); // 表示クリア　ストップ
            view.clearTimer();
        });
        $('#shift_btn').on('click', function(e) { // シフト表示ボタン modal_3　クリック
            e.preventDefault();
            model.get_user_shift().done(function(data) {
                view.render_user_shift(data);
                $('#modal3').iziModal('open');
                // window.clearTimeout(timer);
                view.clearTimer();
            }).fail(function(data) {
                view.show_err_toast('通信エラー');
            });
        });

        // モーダル 操作
        $(document).on('click', '.tabs li', function() {// モーダル 1 3　タブ操作
            var index = $(this).parent().parent().find('.tabs li').index(this);
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            $(this).parent().parent().find('.tab-content').removeClass('show').eq(index).addClass('show');
        });
        $(document).on('click', '.to-mail-btn', function() { // モーダル1　依頼ボタン
            // $('#modal1').iziModal('close');
            view.render_modal2();
            noticeDate = $(this).attr('data-date');
            setTimeout(function() {
                calendar1.setDate(noticeDate);
                view.rendar_modal2_date();
                view.render_modal2_status_change();
            }, 50);
        });
        $(document).on('click', '.notice-btn', function() { // モーダル 2 依頼ボタンクリック
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
        $(document).on('click', '#today_mark', function() { // modal2 本日ボタン
            var now = new Date();
            calendar1.setDate(now);
            noticeDate = formatDate(now, 'YYYY-MM-DD');
            view.rendar_modal2_date();
            view.render_modal2_status_change();
        });
        $(document).on('click', '#picker_del_in_time', function() {
            timepicker_in.clear();
        });
        $(document).on('click', '#picker_del_out_time', function() {
            timepicker_out.clear();
        });
        $(document).on('keyup', '#memo', function() { // modal2 コメント変更時
            view.render_modal2_check();
        });
        $(document).on('click', '#time_edit_submit', function() { // modal2 依頼送信ボタン　クリック
            $(this).addClass('disable');
            model.mail_submit().done(function(data) {
                if (data == 'ok') {
                    $('#modal2').iziModal('close', {
                        transition: 'bounceOutUp'
                    });
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
        $(document).on('click', '#message_in_submit', function() {
            var message_in = $('#message_in').val();
            if (!message_in) {
                if (!$('#modal4').hasClass('wobble')) {
                    $('#modal4').addClass('wobble');
                    setTimeout(function(){
                        $('#modal4').removeClass('wobble');
                        $('#message_in').trigger('focus');
                    }, 1500);
                }
            } else {
                $('#modal4').iziModal('close', {
                    transition: 'bounceOutUp'
                });
                model.insert_message_data('in', message_in);
                $('#message_in').val("");
            }
        });
        $(document).on('click', '#message_out_submit', function() {
            var message_out = $('#message_out').val();
            if (!message_out) {
                if (!$('#modal5').hasClass('wobble')) {
                    $('#modal5').addClass('wobble');
                    setTimeout(function(){
                        $('#modal5').removeClass('wobble');
                        $('#message_out').trigger('focus');
                    }, 1500);
                }
            } else {
                $('#modal5').iziModal('close', {
                    transition: 'bounceOutUp'
                });
                model.insert_message_data('out', message_out);
                $('#message_out').val("");
            }
        });

        // モーダルクローズ後　処理
        // $(document).on('closed', '#modal1, #modal2, #modal3', function(e) {

        // var timer_id = setTimeout(function() { // 10秒後表示クリア
        //   view.render_id_clear();
        // }, 10000);
        // timer.push(timer_id);
        // console.log('close '+ timer);

        // });
    });

}());
