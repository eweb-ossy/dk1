(function() {

    var table_users; // table
    var table_shift; // table
    var shift_status;
    var days_num;
    var calendar; // calendar
    var register_save_flag = 0;
    var filterUserData;
    var select_group_1;
    var select_group_2;
    var select_group_3;
    var datepicker;
    var date;
    var row_data = {};
    var shift_status_id;
    var select_shift_time_diff;

    var userId = Cookies.get('shiftUserId');

    var formatDate = function(date, format) {
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
        // model テーブル　ユーザーコラム取得
        getUsersTableColumns: function() {
            return $.ajax({
                url: '../data/columns/shift_users',
                dataType: 'json',
                type: 'POST'
            })
        },
        // model テーブル　シフトコラム取得
        getShiftTableColumns: function() {
            return $.ajax({
                url: '../data/columns/shift_main',
                dataType: 'json',
                type: 'POST'
            })
        },
        // model シフト登録用ファイルダウンロード
        downloadCsv: function() {
            var action = '../data/admin_shift/downloadCsv';
            var form = document.createElement('form');
            form.setAttribute('action', action);
            form.setAttribute('method', 'post');
            form.style.display = "none";
            document.body.appendChild(form);
            var input = document.createElement('input');
            input.setAttribute('type', 'hidden');
            input.setAttribute('name', 'name');
            input.setAttribute('value', $('#user_name').text());
            form.appendChild(input);
            var input2 = document.createElement('input');
            input2.setAttribute('type', 'hidden');
            input2.setAttribute('name', 'year');
            input2.setAttribute('value', formatDate(new Date(date), 'YYYY'));
            form.appendChild(input2);
            var input3 = document.createElement('input');
            input3.setAttribute('type', 'hidden');
            input3.setAttribute('name', 'month');
            input3.setAttribute('value', formatDate(new Date(date), 'MM'));
            form.appendChild(input3);
            var input4 = document.createElement('input');
            input4.setAttribute('type', 'hidden');
            input4.setAttribute('name', 'user_id');
            input4.setAttribute('value', userId);
            form.appendChild(input4);
            form.submit();
        },
        // model シフト登録用ファイル　アップロード
        uploadCsv: function(file) {
            var formData = new FormData();
            formData.append('files', file);
            return $.ajax({
                url: '../data/admin_shift/uploadCsv',
                dataType: 'text',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false
            })
        },
        // model 選択　従業員情報　取得
        get_user_data: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../data/admin_shift/userData',
                data: {
                    user_id: userId
                }
            })
        },
        // model モーダル シフトデータ保存
        save_data: function() {
        return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../data/admin_shift/saveData',
                data: {
                    user_id: userId,
                    dk_date: row_data.date,
                    in_time: $('#picker_shift_in_time').val(),
                    out_time: $('#picker_shift_out_time').val(),
                    rest: $('#shift_rest_range').val(),
                    status: shift_status_id,
                    hour: select_shift_time_diff
                }
            })
        },
        // model モーダル register single 反映
        ref_register: function() {
            return $.ajax({
                type: 'POST',
                url: '../data/admin_shift/refRegister',
                data: {
                    user_id: userId,
                    status: shift_status_id,
                    dk_date: row_data.date,
                    in_time: $('#picker_shift_in_time').val(),
                    out_time: $('#picker_shift_out_time').val(),
                    rest: $('#shift_rest_range').val(),
                    status: shift_status_id,
                    hour: select_shift_time_diff,
                    id: row_data.id
                }
            })
        },
        // model get calendar shift data
        get_calendar_shift_data: function() {
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
        // model calendar data clear
        clear_shift_data: function() {
            var eventSources = calendar.getEventSources();
            var len = eventSources.length;
            for (var i = 0; i < len; i++) {
                eventSources[i].remove();
            }
        },
        // model calendar holiday data
        cat_calendar_holiday_data: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../data/admin_shift/calHoriday',
                data: {
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM'),
                }
            })
        },
        // model work data
        get_work_data: function() {
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
        // model 申請データ取得
        get_register_data: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../data/admin_shift/registerStatus',
                data: {
                    user_id: userId,
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM'),
                    flag: 'admin'
                }
            })
        },
        // model 申請データ all month 反映
        ref_register_All: function() {
            return $.ajax({
                type: 'POST',
                url: '../data/admin_shift/refRegisterAll',
                data: {
                    user_id: userId,
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM')
                }
            })
        },
        // model all data
        get_all_data: function() {
            return $.ajax({
                type: 'POST',
                url: '../data/admin_shift/allData',
                data: {
                    filterUserData: filterUserData,
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM')
                }
            })
        }
    }

    // VIEWS
    const view = {
        // view 日付　表示
        renderDate: new Promise(function(resolve, reject) {
            datepicker = flatpickr('#month', {
                plugins: [
                    new monthSelectPlugin({
                        dateFormat: "Y年m月", //defaults to "F Y"
                        theme: "light" // defaults to "light"
                    })
                ],
                defaultDate: 'today',
                "onChange": function() {
                    date = datepicker.selectedDates[0];
                    view.renderDateText(date);
                    view.renderUsersTableData();
                    if (shift_view_flag === 0) {
                        view.renderShiftTableData();
                    }
                    if (shift_view_flag === 1) {
                        calendar.gotoDate(date);
                        view.render_cal_data();
                        view.rendar_cal_holiday();
                    }
                }
            });
            date = datepicker.selectedDates[0];
            date.setDate(1);
            // // cookie
            // if (Cookies.get('shiftMonth')) {
            //   date = new Date(Cookies.get('shiftMonth'));
            //   view.renderDateText(date);
            // }
            resolve();
        }),
        renderDateText: function(date) {
        //
            datepicker.setDate(date);
            //
            var shift_end_day = 0;
            if (shift_end_day > 0) {
                var pre_date = new Date(date);
                pre_date.setMonth(pre_date.getMonth() - 1);
                var pre_day = shift_end_day + 1;
                var first_date = formatDate(pre_date, 'YYYY年MM月') + pre_day + '日';
                var end_date = formatDate(date, 'YYYY年MM月') + shift_end_day + '日';
                var pre_month_days = new Date(pre_date.getFullYear(), pre_date.getMonth() + 1, 0);
                pre_month_days = pre_month_days.getDate();
                days_num = (pre_month_days - shift_end_day) + shift_end_day;
            } else {
                var first_date = formatDate(new Date(date), 'YYYY年MM月') + '01日';
                var end_date = formatDate(new Date(date.getFullYear(), date.getMonth() + 1, 0), 'YYYY年MM月DD日');
                var pre_month_days = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                days_num = pre_month_days.getDate();
            }
            $('#to_from_date').text(first_date + 'から' + end_date + 'までの' + days_num + '日間');

            var options = {era: 'long', year: 'numeric'};
            var wareki = new Intl.DateTimeFormat('ja-JP-u-ca-japanese', options).format(new Date(date));
            $('#date-area-wareki').text(wareki);

            // month diff
            var nowDate = new Date();
            var nowYear = nowDate.getFullYear();
            var nowMonth = nowDate.getMonth();
            var selectDate = new Date(date);
            var selectYear = selectDate.getFullYear();
            var selectMonth = selectDate.getMonth();
            var d1 = new Date(nowYear, nowMonth);
            var d2 = new Date(selectYear, selectMonth);
            var months =  d1.getMonth() - d2.getMonth() + (12 * (d1.getFullYear() - d2.getFullYear()));
            if (months === 0) {
                $('#this_month').addClass('disable');
                $('#this_month_mark').addClass('this-month');
                $('#this_month_mark').text('今月');
            } else {
                $('#this_month').removeClass('disable');
                $('#this_month_mark').removeClass('this-month');
                if (months > 0) {
                    $('#this_month_mark').text(months + 'ヶ月前');
                } else {
                    $('#this_month_mark').text(Math.abs(months) + 'ヶ月後');
                }
            }
            //
            Cookies.set('shiftMonth', date);
        },
        // view テーブル 従業員コラム　設定 表示
        renderUsersTableColumns: new Promise(function(resolve, reject) {
            model.getUsersTableColumns().done(function(data) {
                table_users = new Tabulator('#users_table', {
                    height: 500,
                    columns: data,
                    selectable: true,
                    rowFormatter: function(row) {
                        if (row.getData().shift > 0) {
                            row.getElement().style.color = "#f9ae00";
                            row.getElement().style.fontWeight = "bold";
                        }
                        if (row.getData().shift == days_num) {
                            row.getElement().style.color = "#1c1cff";
                            row.getElement().style.fontWeight = "bold";
                        }
                        if (row.getData().register > 0) {
                            row.getElement().style.backgroundColor = "rgba(252, 168, 168, 0.5)";
                        }
                    },
                    rowClick: function(e, row) {
                        var data = row.getData();
                        if (userId === data.user_id) {
                            view.render_del_select(); // 従業員選択解除 処理
                        } else {
                            userId = data.user_id;
                            Cookies.set('shiftUserId', userId);
                            $('#user_select_disable').removeClass('disabled').addClass('on');
                        }
                        if (shift_view_flag === 0) {
                            view.renderShiftTableData(); // シフトデータ表示 list
                        }
                        if (shift_view_flag === 1) {
                            view.render_cal_data();
                        }
                        view.render_all();
                    },
                    dataFiltered: function(filters, rows) {
                        var list_data = [];
                        filterUserData = [];
                        if (rows.length > 0) {
                            for (var i = 0; i < rows.length; i++) {
                                list_data.push(rows[i]._row.data); 
                            }
                            $.each(list_data, function(key, val) {
                                filterUserData.push(val['user_id']);
                            });
                        }
                        view.render_all();
                    },
                    invalidOptionWarnings: false,
                });
                resolve();

                table_users.toggleColumn('group1_name');
                table_users.toggleColumn('group2_name');
                table_users.toggleColumn('group3_name');

                table_users.hideColumn('shift');
                table_users.hideColumn('register');
            });
        }),
        // view テーブル シフトコラム　表示
        renderShiftTableColumns: function() {
            model.getShiftTableColumns().done(function(data) {
                table_shift = new Tabulator('#shift_table', {
                    height: '100%',
                    columns: data,
                    placeholder: '従業員を選択',
                    resizableColumns: false,
                    rowFormatter: function(row) {
                        if (row.getData().dateView === 'shift_today') {
                            row.getElement().style.background = "#fcf8e3";
                        }
                        if (row.getData().dateView === 'shift_past') {
                            row.getElement().style.background = "#f4f4f4";
                        }
                    },
                    tooltips: function(cell) {
                        var val = cell.getValue();
                        if (val == '日' || val == '祝') {
                            cell.getElement().style.color = "#F44336";
                        }
                        if (val == '土') {
                            cell.getElement().style.color = "#0D47A1";
                        }
                        if (val == '出勤') {
                            cell.getElement().style.color = "#9abcea";
                        }
                        if (val == '公休') {
                            cell.getElement().style.color = "#F44336";
                        }
                        if (val == '未登録') {
                            cell.getElement().style.color = "#ccc";
                        }
                        if (val == '有給') {
                            cell.getElement().style.color = "#fff";
                            cell.getElement().style.background = "#40a598";
                        }
                    },
                    rowClick: function(e, row) {
                        if (authority === 2) {
                            return;
                        }
                        row_data = row.getData();
                        view.render_modal(row_data);
                    },
                    invalidOptionWarnings: false,
                });
                view.renderShiftTableData();
            });
        },
        // view テーブル 従業員データ　表示
        renderUsersTableData: function() {
            // table_users.clearData();
            table_users.replaceData('../data/admin_shift/table_users_data', {
                year: formatDate(new Date(date), 'YYYY'),
                month: formatDate(new Date(date), 'MM')
            }, 'POST');
        },
        // view テーブル　シフトデータ表示
        renderShiftTableData: function() {
            table_shift.clearData();
            if (!userId) {
                return;
            }
            table_shift.replaceData('../data/admin_shift/table_shift_data', {
                year: formatDate(new Date(date), 'YYYY'),
                month: formatDate(new Date(date), 'MM'),
                user_id: userId,
                flag: 'list'
            }, 'POST');
            view.renderUserData();
            table_shift.hideColumn('date');
        },
        // view トップ従業員データ表示
        renderUserData: function() {
            model.get_user_data(userId).done(function(data) {
                $('#user_name').text(data.user_name);
                $('#user_id').text(userId);
                $('#group1_name').text(data.group1_name);
                $('#group2_name').text(data.group2_name);
                $('#group3_name').text(data.group3_name);
                table_users.deselectRow();
                table_users.selectRow(userId);
            });
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
        // view 時刻修正　モーダル　表示
        render_modal: function(row_data) {
            register_save_flag = 0; // 申請保存フラグ
            $('#shift_btn_area').show(); // shift view
            $('#time_edit_submit').text('登録').removeClass('regidter');
            // モーダル 日付表示
            $('#shift_date').text(row_data.dateW);
            var defaultShiftDate = row_data.in_time ? row_data.in_time : '';
            timepicker_shift_in = flatpickr('#picker_shift_in_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: defaultInHour,
                defaultMinute: defaultInMinute,
                minuteIncrement: defaultMinuteIncrement,
                defaultDate: defaultShiftDate,
                onChange: function() {
                    view.render_modal_check();
                },
                onClose: function() {
                    view.render_modal_check();
                }
            });
            timepicker_shift_out = flatpickr('#picker_shift_out_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: defaultOutHour,
                defaultMinute: defaultOutMinute,
                minuteIncrement: defaultMinuteIncrement,
                defaultDate: row_data.out_time,
                onChange: function() {
                    view.render_modal_check();
                },
                onClose: function() {
                    view.render_modal_check();
                }
            });
            // モーダル ヘッダー部
            $('.iziModal-header-title').text($('#user_name').text());
            var group1_title = $('#group1_name').text();
            var group2_title = $('#group2_name').text();
            var group3_title = $('#group3_name').text();
            $('.iziModal-header-subtitle').text('ID：' + userId + '　' + group1_title + '　' + group2_title + '　' + group3_title);
            // モーダル シフト
            var shift_in_time = row_data.in_time ? row_data.in_time : '--';
            $('#shift_in_time').text(shift_in_time);
            var shift_out_time = row_data.out_time ? row_data.out_time : '--';
            $('#shift_out_time').text(shift_out_time);
            var shift_val = row_data.hour2 ? row_data.hour2+'分' : '--';
            $('#shift_value').text(shift_val);
            var shift_val2 = row_data.hour ? row_data.hour : '';
            $('#shift_value2').text(shift_val2);
            view.render_shift_btn(row_data.status);
            // モーダル シフト休憩
            $('#shift_rest_range').prop('disabled', false).val(Number(row_data.rest));
            var shift_rest_val = row_data.rest2 ? row_data.rest2 : 0;
            view.render_shift_rest_btn(shift_rest_val);
            var rest_val2 = row_data.rest ? row_data.rest : '0:00';
            $('#rest_value2').text(rest_val2);

            // 申請保存時
            if (row_data.flag === 'register') {
                $('.iziModal-header-title').text($('#user_name').text()+' の申請');
                $('.iziModal-header-subtitle').text('申請の反映登録をおこないます');
                $('#time_edit_submit').text('反映').addClass('regidter');
                $('#modal2').iziModal('setIcon', 'icon-notice');
                $('#modal2').iziModal('setHeaderColor', '#464646');
                register_save_flag = 1;
                view.render_modal_check();
            }

            $('#time_edit_submit').attr('data-user-id', userId);
            $('#modal2').iziModal('open');
        },
        // view モーダル　シフト休憩　定時時刻　アクティブ表示
        render_shift_rest_btn: function(shift_rest_val) {
            $('.rs_btn').removeClass('active');
            $('#shift_rest_' + shift_rest_val).addClass('active');
            $('#shift_memori_' + shift_rest_val).addClass('active');
            $('#shift_rest_value').text(shift_rest_val + '分');
            var hours = (shift_rest_val / 60);
            var rhours = Math.floor(hours);
            var minutes = (hours - rhours) * 60;
            var rminutes = Math.round(minutes);
            $('#shift_rest_value2').text(rhours+':'+('0' + rminutes).slice(-2));
            $('#shift_rest_range').val(shift_rest_val);
            view.render_modal_check();
        },
        // view モーダル　シフトステータス　切替
        render_shift_btn: function(data) {
            $('.shift-btn').removeClass('disabled');
            $('#shift_time_edit_area').hide();
            if (data === '未登録' || data === '') {
                $('#state_none').addClass('disabled');
                shift_status = '未登録<div class="shift-date">'+row_data.dateW+'</div>';
                var color = '#ccc';
                shift_status_id = 'del';
            }
            if (data === '出勤') {
                $('#state_work').addClass('disabled');
                shift_status = '出勤';
                var color = '#3788d8';
                shift_status_id = 0;
                $('#shift_time_edit_area').show();
            }
            if (data === '公休') {
                $('#state_rest').addClass('disabled');
                shift_status = '公休<div class="shift-date">'+row_data.dateW+'</div>';
                shift_status_id = 1;
                var color = '#ff7d73';
            }
            if (data === '有給') {
                $('#state_raid').addClass('disabled');
                shift_status = '有給<div class="shift-date">'+row_data.dateW+'</div>';
                shift_status_id = 2;
                var color = '#40a598';
            }
            $('#status_name').html('シフト '+shift_status).css('color', color);
            $('#modal2').iziModal('setHeaderColor', color);
            view.render_modal_check();
        },
        // view モーダル バリデーション
        render_modal_check: function() {
            $('#time_edit_submit').addClass('disable');
            $('.time-input, .shift-rest-value').removeClass('error');
            $('#shift_value').text('--');
            $('#shift_value2').text('');
            // シフト出勤時
            if (shift_status === '出勤') {
                var select_shift_in_time_hour = new Date(timepicker_shift_in.selectedDates).getHours();
                var select_shift_in_time_minute = new Date(timepicker_shift_in.selectedDates).getMinutes();
                var select_shift_in_time = select_shift_in_time_hour * 60 + select_shift_in_time_minute;
                var select_shift_out_time_hour = new Date(timepicker_shift_out.selectedDates).getHours();
                var select_shift_out_time_minute = new Date(timepicker_shift_out.selectedDates).getMinutes();
                var select_shift_out_time = select_shift_out_time_hour * 60 + select_shift_out_time_minute;
                var in_shift_time_val = $('#picker_shift_in_time').val();
                var out_shift_time_val = $('#picker_shift_out_time').val();
                if (over_day > 0) { // 日またぎ処理
                    if (select_shift_in_time_hour <= over_day) {
                        select_shift_in_time += 1440; // 24H
                        var hour = in_shift_time_val.substr(0, 2);
                        var minute = in_shift_time_val.substr(-2);
                        hour = Number(hour)+24;
                        in_shift_time_val = hour + ':' + minute;
                    }
                    if (select_shift_out_time_hour <= over_day) {
                        select_shift_out_time += 1440; // 24H
                        var hour = out_shift_time_val.substr(0, 2);
                        var minute = out_shift_time_val.substr(-2);
                        hour = Number(hour)+24;
                        out_shift_time_val = hour + ':' + minute;
                    }
                }
                if (!select_shift_in_time) {
                    $('#picker_shift_in_time').addClass('error'); // シフト出勤予定 -> error
                    return;
                }
                if (!select_shift_out_time) {
                    $('#picker_shift_out_time').addClass('error'); // シフト退勤予定なし -> error
                    return;
                }
                if (select_shift_in_time && select_shift_out_time) { // シフト出勤予定あり+シフト退勤予定なしあり
                    var shift_rest = $('#shift_rest_range').val();
                    select_shift_time_diff = select_shift_out_time - select_shift_in_time; // シフト時間（休憩時間引かない）
                    if ((select_shift_time_diff - shift_rest) <= 0) {
                        $('#picker_shift_out_time').addClass('error'); // 休憩予定がシフト時間より多い -> error
                        return;
                    }
                    if (select_shift_time_diff <= shift_rest) {
                        $('.shift-rest-value').addClass('error'); // シフト勤務予定が休憩予定より少ない -> error
                        return;
                    }
                    select_shift_time_diff -= shift_rest; // シフト時間（休憩引く）
                    $('#shift_value').text(select_shift_time_diff+'分');
                    var hours = (select_shift_time_diff / 60);
                    var rhours = Math.floor(hours);
                    var minutes = (hours - rhours) * 60;
                    var rminutes = Math.round(minutes);
                    $('#shift_value2').text(rhours+':'+('0' + rminutes).slice(-2));
                }
            }
            // 初期データと差異があった場合、修正ボタンをactiveにする
            if (row_data.status != shift_status) {
                $('#time_edit_submit').removeClass('disable');
            }
            if (shift_status === '出勤') {
                if (row_data.in_time != in_shift_time_val) {
                    $('#time_edit_submit').removeClass('disable');
                }
                if (row_data.out_time != out_shift_time_val) {
                    $('#time_edit_submit').removeClass('disable');
                }
                var shift_rest_data = row_data.rest2 ? row_data.rest2 : 0;
                if (shift_rest_data != shift_rest) {
                    $('#time_edit_submit').removeClass('disable');
                }
                if (register_save_flag === 1) {
                    $('#time_edit_submit').removeClass('disable');
                }
            }
        },
        // view calendar
        render_calendar: function() {
            var calendarEl = document.getElementById('shift_table');
            calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['dayGrid'],
                timeZone: 'local',
                defaultView: 'dayGridMonth',
                locale: 'ja',
                defaultDate: new Date(date),
                firstDay: shift_cal_first_day,
                header: {
                    left: '',
                    center: '',
                    right: ''
                },
                eventClick: function(info) {
                    $('.fc-day-grid-event').css('opacity', 1);
                    info.el.style.opacity = 0.5;
                    if (authority === 2) {
                        return;
                    }
                    row_data = info.event.extendedProps;
                    view.render_modal(row_data);
                }
            });
            calendar.render();
            view.render_cal_data();
            view.rendar_cal_holiday();
        },
        // view calendar holiday
        rendar_cal_holiday: function() {
            model.cat_calendar_holiday_data().done(function(data) {
                $('.holiday').html('');
                $.each(data, function(key, elem) {
                    $('.fc-day-top' + '[data-date="' + elem.date + '"]').prepend('<span class="holiday">' + elem.holiday + '</span>');
                    if (elem.holiday) {
                        $('.fc-day-top' + '[data-date="' + elem.date + '"] > .fc-day-number').css('color', '#ff7d73');
                    }
                });
            });
        },
        //
        render_cal_data: function() {
            view.renderUserData();
            model.clear_shift_data();
            if (userId) {
                model.get_work_data().done(function(data) {
                    calendar.addEventSource(data);
                    model.get_calendar_shift_data().done(function(data) {
                        calendar.addEventSource(data);
                        model.get_register_data().done(function(data) {
                            calendar.addEventSource(data);
                            view.rendar_register_submit_btn(data.length);
                        });
                    });
                });
            }
        },
        // view register submit btn
        rendar_register_submit_btn: function(register_num) {
            $('#register_submit_btn').addClass('disabled');
            if (register_num > 0) {
                $('#register_submit_btn').removeClass('disabled');
            }
        },
        // view all
        render_all: function() {
            model.get_all_data().done(function(data) {
                if (data.length === 0) {
                    return;
                }
                var obj = [];
                var i = 0;
                var all_day_hour = 0;
                var all_num = 0;
                $.each(data, function(key, elem) {
                    var day_hour = 0;
                    var num = 0;
                    $.each(elem, function(k, e) {
                        if (userId) {
                            if (Number(userId) != k) {
                                return true;
                            }
                        }
                        if (e.hour > 0) {
                            day_hour += e.hour;
                            num++;
                            all_day_hour += e.hour;
                            all_num++;
                        }
                    });
                    var hour = Math.floor(day_hour/60);
                    var minute = day_hour%60;
                    var time = hour+':'+('0'+minute).slice(-2);
                    if (shift_view_flag === 1 && !userId) {
                        var title = '';
                        if (hour > 0) {
                            title = num+'人 合計時間 '+time;
                        }
                        obj[i] = {
                            title: title,
                            start: key,
                            color: 'rgba(255, 255, 255, 0)',
                            textColor: '#1b97a8',
                            classNames: 'work-status'
                        };
                    }
                    i++;
                });
                if (shift_view_flag === 1 && !userId) {
                    model.clear_shift_data();
                    calendar.addEventSource(obj);
                }
                var all_hour = Math.floor(all_day_hour/60);
                var all_minute = all_day_hour%60;
                var all_time = all_hour+':'+('0'+all_minute).slice(-2);
                if (!userId) {
                    table_users.deselectRow();
                    $('#user_time_data').html("");
                    $('#user_name').html("");
                    $('#user_id').text("");
                    $('#group1_name').text("");
                    $('#group2_name').text("");
                    $('#group3_name').text("");
                    $('#user_name').html('<i class="fas fa-users"></i> 稼働合計人数：'+all_num+'人 <i class="fas fa-clock"></i> 合計時間：'+all_time);
                    $('#group1_name').text(select_group_1);
                    $('#group2_name').text(select_group_2);
                    $('#group3_name').text(select_group_3);
                }
                if (userId) {
                    $('#user_time_data').html('<i class="fas fa-walking"></i> シフト日数：'+all_num+'日 <i class="fas fa-clock"></i> 合計シフト時間：'+all_time);
                }
            });
        },
        // view 従業員選択解除 処理
        render_del_select: function() {
            userId = '';
            filterUserData = [];
            select_group_1 = '';
            select_group_2 = '';
            select_group_3 = '';
            Cookies.remove('shiftUserId');
            $('#user_select_disable').addClass('disabled').removeClass('on');
            $('#register_submit_btn').addClass('disabled');
            view.renderUsersTableData();
            if (shift_view_flag === 0) {
                view.renderShiftTableData();
            }
            if (shift_view_flag === 1) {
                view.render_cal_data();
            }
        }
    }

    $(function() {
        // 初期データ読み込み・表示
        Promise.all([view.renderUsersTableColumns, view.renderDate]).then(function() {
            view.renderUsersTableData();
            view.renderDateText(date); //
            view.render_all(); // 合計値表示
            if (shift_view_flag === 0) { // shift list table view
                $('#register_submit_btn').hide();
                view.renderShiftTableColumns(); // シフトテーブル表示
            }
            if (shift_view_flag === 1) { // shift calender view
                $('#register_submit_btn').show();
                view.render_calendar(); // シフトカレンダー表示
            }
        });
        if (userId) { // userIdがある場合は、従業員選択解除ボタンをアクティブ
            $('#user_select_disable').removeClass('disabled').addClass('on');
        }
        if (authority === 2) { // authority 権限が回覧の場合はファイル登録ボタンをdisabled
            $('#csv_download_btn').addClass('disabled');
            $('.up-file').addClass('disabled');
        }
        $('#modal2').iziModal({ // モーダル設定
            headerColor: '#1591a2',
            focusInput: false,
            width: 700
        });

        // 日付操作
        function changeDate(diff) {
            date = diff ? new Date(date.setMonth(date.getMonth() + diff)) : new Date();
            view.renderDateText(date);
            view.renderUsersTableData();
            if (shift_view_flag === 0) {
                view.renderShiftTableData();
            }
            if (shift_view_flag === 1) {
                if (diff === -1) calendar.prev();
                if (diff === 1) calendar.next();
                if (!diff) calendar.today();
                view.render_cal_data();
                view.rendar_cal_holiday();
            }
        }
        $('#less_month').on('click', function() { // 戻るボタン
            changeDate(-1);
        });
        $('#add_month').on('click', function() { // 次へボタン
            changeDate(1);
        });
        $('#this_month_mark, #this_month').on('click', function() { // 今月ボタン
            changeDate();
        });

        // シフト登録用ファイルダウンロードボタン
        $('#csv_download_btn').on('click', function() {
            model.downloadCsv();
        });
        // シフト登録用ファイル　アップロードボタン
        $('input[type="file"]').on('change', function() {
            var files = $(this).prop('files');
            if (files.length > 0) {
                $('#loader').show();
                for (var i = 0; i < files.length; i++) {
                    model.uploadCsv(files[i]).done(function(data) {
                        $('#loader').hide();
                        view.show_toast('シフト登録 ' + data); // トースト表示
                        view.renderUsersTableData();
                        if (shift_view_flag === 0) {
                            view.renderShiftTableData();
                        }
                        if (shift_view_flag === 1) {
                            view.render_cal_data();
                            view.rendar_cal_holiday();
                        }
                    })
                }
            }
        });
        // 詳細表示ボタン　クリック
        $('#users_table_btn').on('click', function() {
            $('#users_table_area').toggleClass('detaile_view');
            $(this).toggleClass('on');
            table_users.toggleColumn('group1_name');
            table_users.toggleColumn('group2_name');
            table_users.toggleColumn('group3_name');
        });

        // モーダル　操作
        $(document).on('input change', '.time-val', function() { // モーダル入力変更があった場合　登録ボタンアクティブ
            view.render_modal_check();
        });
        $(document).on('click', '#picker_shift_del_in_time', function() {
            timepicker_shift_in.clear();
            view.render_modal_check();
        });
        $(document).on('click', '#picker_shift_del_out_time', function() {
            timepicker_shift_out.clear();
            view.render_modal_check();
        });
        $(document).on('click', '.shift-btn', function() { // シフトステータスボタン
            view.render_shift_btn($(this).text());
        });
        $(document).on('input', '#shift_rest_range', function() { // シフト休憩スライダー操作　時間表示
            var rest_val = $(this).val();
            view.render_shift_rest_btn(rest_val);
        });
        $(document).on('click', '.rs_btn', function() { // シフト休憩定時ボタン　クリック
            var rest_val = $(this).attr('data-time');
            view.render_shift_rest_btn(rest_val);
        });
        $(document).on('click', '#time_edit_submit', function() { // 修正登録ボタン　クリック
            $('#time_edit_submit').addClass('disable'); // 登録ボタン 非アクティブにする
            // シフト変更保存
            if (register_save_flag === 0) {
                model.save_data().done(function(data) {
                    if (data.message === 'ok') {
                        $('#modal2').iziModal('close');
                        view.show_toast(data.today + ' 修正登録 ' + $('#user_name').text() + ' ' + data.user_id); // トースト表示
                        view.renderUsersTableData();
                        if (shift_view_flag === 0) {
                            view.renderShiftTableData();
                        }
                        if (shift_view_flag === 1) {
                            view.render_cal_data();
                        }
                    } else {
                    view.show_err_toast('通信エラー');
                    }
                }).fail(function() {
                    view.show_err_toast('通信エラー');
                });
            }
            // シフト申請保存
            if (register_save_flag === 1) {
                model.ref_register().done(function(data) {
                    if (data === 'ok') {
                        $('#modal2').iziModal('close');
                        view.show_toast('申請シフト反映'); // トースト表示
                        view.renderUsersTableData();
                        if (shift_view_flag === 0) {
                            view.renderShiftTableData();
                        }
                        if (shift_view_flag === 1) {
                            view.render_cal_data();
                        }
                    } else {
                        view.show_err_toast('通信エラー');
                    }
                }).fail(function() {
                    view.show_err_toast('通信エラー');
                });
            }
        });
        // リスト - カレンダー　表示切替　ボタン
        const $register_submit_btn = $('#register_submit_btn');
        const $shift_table = $('#shift_table');
        const $shift_view_title = $('#shift_view_title');
        $('#shift_view_change').on('click', function() {
            $shift_table.empty();
            if (shift_view_flag === 0) { // list -> calender
                $register_submit_btn.show();
                $shift_table.removeClass('tabulator');
                $shift_view_title.html('<i class="far fa-calendar"></i> カレンダー');
                view.render_calendar();
                shift_view_flag = 1;
            } else { // calender -> list
                $register_submit_btn.hide();
                $shift_table.removeClass('fc fc-ltr fc-unthemed');
                model.clear_shift_data();
                view.renderShiftTableColumns();    
                $shift_view_title.html('<i class="fas fa-list"></i> リスト');
                shift_view_flag = 0;
            }
            view.renderUsersTableData();
        });
        // ALL 申請反映ボタン
        $('#register_submit_btn').on('click', function() {
            $(this).addClass('disabled');
            model.ref_register_All().done(function(data) {
                $.each(data, function(key, elem) {
                    if (elem.message == 'ok') {
                        view.show_toast('申請シフト反映 '+elem.dk_date); // トースト表示
                    } else {
                        view.show_err_toast('反映エラー'+elem.dk_date);
                    }
                });
                view.renderUsersTableData();
                view.render_cal_data();
            }).fail(function() {
                view.show_err_toast('通信エラー');
            });
        });

        //
        $('#select_group_1').on('change', function() {
            table_users.removeFilter('group1_name', '=', select_group_1);
            select_group_1 = $(this).val();
            if (select_group_1 !== 'ALL') {
                table_users.addFilter('group1_name', '=', select_group_1);
            }
        });
        $('#select_group_2').on('change', function() {
            table_users.removeFilter('group2_name', '=', select_group_2);
            select_group_2 = $(this).val();
            if (select_group_2 !== 'ALL') {
                table_users.addFilter('group2_name', '=', select_group_2);
            }
        });
        $('#select_group_3').on('change', function() {
            table_users.removeFilter('group3_name', '=', select_group_3);
            select_group_3 = $(this).val();
            if (select_group_3 !== 'ALL') {
                table_users.addFilter('group3_name', '=', select_group_3);
            }
        });

        $('#shift_status').on('change', function() {
            table_users.removeFilter('shift', '=', 0);
            table_users.removeFilter('shift', '>', 0);
            table_users.removeFilter('shift', '=', days_num);
            table_users.removeFilter('shift', '!=', days_num);
            var shift_status = $(this).val();
            if (shift_status == 0) {
                table_users.addFilter('shift', '=', 0);
            }
            if (shift_status == 1) {
                table_users.addFilter('shift', '>', 0);
                table_users.addFilter('shift', '!=', days_num);
            }
            if (shift_status == 2) {
                table_users.addFilter('shift', '=', days_num);
            }
        });

        // 従業員選択解除 ボタン　クリック
        $('#user_select_disable').on('click', function() {
            view.render_del_select(); // 従業員選択解除 処理
        });
    });
}());
