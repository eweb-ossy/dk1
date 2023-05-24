(function() {
    var mobile_menu_visible = 0; // モバイルサイドバー用
    var date = new Date();
    date.setDate(1);
    var month_diff = 0;
    var calendar;
    var in_picker;
    var out_picker;
    var edit = 0;
    var shiftData;

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
    var addDate = function(date, num, interval) {
        switch (interval) {
            case 'YYYY':
            date.setYear(date.getYear() + num);
            break;
            case 'MM':
            date.setMonth(date.getMonth() + num);
            break;
            case 'hh':
            date.setHours(date.getHours() + num);
            break;
            case 'mm':
            date.setMinutes(date.getMinutes() + num);
            break;
            case 'ss':
            date.setSeconds(date.getSeconds() + num);
            break;
            default:
            date.setDate(date.getDate() + num);
        }
        return date;
    };

    var model = {
        // model 通知情報取得
        get_notice_data: function() {
            var data = {
                system_id: sysId,
                user_id: userId
            };
            socket.emit('notice_client_to_server', data);
            socket.on('notice_server_to_client', function(notice_data) {
                var non_read_mark = 0;
                var ng_mark = 0;
                $.each(notice_data, function(key, val) {
                    if (val.user_id != userId && val.high_user_id.indexOf(String(userId)) < 0) {
                        return true;
                    }

                    if (val.notice_status == 0) {
                        non_read_mark++;
                    }
                    if (val.notice_status == 1) {
                        if (val.user_id == userId) { // 自分の場合
                            var icon = '<i class="fas fa-thumbs-up"></i>';
                        } else { // 申請依頼
                            var icon = '<i class="fas fa-thumbs-up"></i>';
                        }
                    }
                    if (val.notice_status == 2) {
                        if (val.user_id == userId) { // 自分の場合
                            var icon = '<i class="fas fa-exclamation-circle"></i>';
                        } else { // 申請依頼
                            var icon = '<i class="fas fa-exclamation-circle"></i>';
                        }
                        ng_mark++;
                    }
                    if (val.notice_flag == 1) {
                        var notice_title1 = '修正依頼';
                    }
                    if (val.notice_flag == 2) {
                        var notice_title1 = '削除依頼';
                    }
                    if (val.notice_flag == 3) {
                        var notice_title1 = '遅刻依頼';
                    }
                    if (val.notice_flag == 4) {
                        var notice_title1 = '早退依頼';
                    }
                    if (val.notice_flag == 5) {
                        var notice_title1 = '残業依頼';
                    }
                    if (val.notice_flag == 6) {
                        var notice_title1 = '有給依頼';
                    }
                    if (val.notice_flag == 7) {
                        var notice_title1 = '欠勤依頼';
                    }
                    if (val.notice_flag == 8) {
                        var notice_title1 = 'その他依頼';
                    }
                    if (val.notice_flag == 11) {
                        var notice_title1 = '休暇依頼';
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
                    // var non_read_flag = 0;
                    // for (var i = 0; i < val.notice_text_id.length; i++) {
                    //   var read = val.read_users[Number(val.notice_text_id[i])].indexOf(String(userId));
                    //   if (read < 0) {
                    //     non_read_flag++;
                    //     $('.no-data').hide();
                    //     var header_menu_html = '<div class="dropdown-item" id="'+ val.notice_id +'">' + icon + "　" + user_data_w + toDate_w + endDate_w + '  <i class="fas fa-angle-double-right"></i> ' + notice_title1 +'</div>';
                    //     $('#header_notice').append(header_menu_html);
                    //   }
                    // }
                    // if (non_read_flag > 0) {
                    //   non_read_mark++;
                    // }
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
                });
        },
        // model
        get_shift_data: function() {
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
        // model shift register save
        shift_register: function(select_date) {
            var status = $('[name="status"]:checked').val();
            var in_time = '';
            var out_time = '';
            if (status == 0) {
                var in_hour = in_picker.getHour();
                var in_minute = in_picker.getMinute();
                var out_hour = out_picker.getHour();
                var out_minute = out_picker.getMinute();
                in_time = in_hour+':'+in_minute+':00';
                out_time = out_hour+':'+out_minute+':00';
            }
            return $.ajax({
                type: 'POST',
                url: '../data/admin_shift/saveRegister',
                data: {
                    user_id: userId,
                    dk_date: select_date,
                    shift_status: status,
                    in_time: in_time,
                    out_time: out_time
                }
            })
        },
        // model register get
        get_register_data: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../data/admin_shift/registerStatus',
                data: {
                    user_id: userId,
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM'),
                    flag: 'mypage'
                }
            })
        },
        // model undata change save
        nudata_change_save: function() {
            return $.ajax({
                type: 'POST',
                url: '../data/admin_shift/saveUndataChange',
                data: {
                    user_id: userId,
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM')
                }
            })
        },
        // model undata del save
        nudata_del_save: function() {
            return $.ajax({
                type: 'POST',
                url: '../data/admin_shift/saveUndataDel',
                data: {
                    user_id: userId,
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM')
                }
            })
        },
    }

    var view = {
        // view date表示
        renderDate: function() {
            var now_date = new Date(date);
            if (mypage_end_day > 0) {
                var pre_date = new Date(date);
                pre_date.setMonth(pre_date.getMonth() - 1);
                var pre_day = mypage_end_day + 1;
                var first_date = formatDate(pre_date, 'YYYY年MM月') + pre_day + '日';
                var end_date = formatDate(now_date, 'YYYY年MM月') + mypage_end_day + '日';
                var pre_month_days = new Date(pre_date.getFullYear(), pre_date.getMonth() + 1, 0);
                pre_month_days = pre_month_days.getDate();
                var days_num = (pre_month_days - mypage_end_day) + mypage_end_day;
            } else {
                var first_date = formatDate(now_date, 'YYYY年MM月') + '01日';
                var end_date = formatDate(new Date(now_date.getFullYear(), now_date.getMonth() + 1, 0), 'YYYY年MM月DD日');
                var pre_month_days = new Date(now_date.getFullYear(), now_date.getMonth() + 1, 0);
                var days_num = pre_month_days.getDate();
            }
            $('#to_from_date').text(first_date + 'から' + end_date + 'までの' + days_num + '日間');
            $('#month').val(formatDate(new Date(date), 'YYYY年MM月'));
            var date_format = formatDate(new Date(date), 'YYYY-MM');
            var now = formatDate(new Date(), 'YYYY-MM');
            if (date_format === now) {
                $('#this_month').addClass('disable');
                $('#this_month_mark').addClass('this-month');
                $('#this_month_mark').text('今月');
                month_diff = 0;
            } else {
                $('#this_month').removeClass('disable');
                $('#this_month_mark').removeClass('this-month');
                if (month_diff > 0) {
                    $('#this_month_mark').text(month_diff + 'ヶ月後');
                } else {
                    $('#this_month_mark').text(Math.abs(month_diff) + 'ヶ月前');
                }
            }
        },
        // view
        renderSelect: function(info) {
            in_picker.setHour(shift_first_hour);
            in_picker.setMinute(0);
            out_picker.setHour(shift_end_hour);
            out_picker.setMinute(0);
            var date = new Date(info.event.start);
            var list_date = formatDate(date, 'YYYY年MM月DD日');
            var select_date = formatDate(date, 'YYYY-MM-DD');
            var dayOfWeek = date.getDay();
            var dayOfWeekStr = ["（日）", "（月）", "（火）", "（水）", "（木）", "（金）", "（土）"][dayOfWeek];
            $('#list_date').html('<i class="fas fa-calendar-check"></i> ' + list_date + dayOfWeekStr);
            $('#shift_register_btn').attr('data-date', select_date);
            var now = new Date();
            if (date < now) {
                $('#statue_area').hide();
                $('#time_area').hide();
                $('#shift_register_btn').addClass('disabled');
                return;
            }
            $('#statue_area').show();
            $('#shift_register_btn').removeClass('disabled');
            var status_id = info.event.id;
            $('.radio-text').removeClass('on');
            if (status_id.length > 1) {
                $('#status_0').prop('checked', true);
                $('#time_area').show();
                $('#status_0').next('label').addClass('on');
                var time = status_id.split(":");
                in_picker.setTime(Number(time[0]), Number(time[1]));
                out_picker.setTime(Number(time[2]), Number(time[3]));
            }
            if (status_id == 1) {
                $('#status_1').prop('checked', true);
                $('#time_area').hide();
                $('#status_1').next('label').addClass('on');
            }
            if (status_id == 2) {
                $('#status_2').prop('checked', true);
                $('#time_area').hide();
                $('#status_2').next('label').addClass('on');
            }
            if (status_id == 0) {
                $('#status_none').prop('checked', true);
                $('#time_area').hide();
                $('#status_none').next('label').addClass('on');
            }
        },
        // view calendar holiday
        rendar_cal_holiday: function(data) {
            $('.holiday').html('');
            $.each(data, function(key, elem) {
                $('.fc-day-top' + '[data-date="' + elem.date + '"]').prepend('<span class="holiday">' + elem.holiday + '</span>');
                if (elem.holiday) {
                    $('.fc-day-top' + '[data-date="' + elem.date + '"] > .fc-day-number').css('color', '#ff7d73');
                }
            });
        },
        // view 未登録 → 公休
        render_undata_change_btn: function(data) {
            var register_date = [];
            $.each(data, function(key, elem) {
                register_date.push(elem.start);
            });
            var now_date = formatDate(new Date(), 'YYYY-MM-DD');
            $('#undata_change_btn').addClass('disabled');
            var on = 0;
            $.each(shiftData, function(key, elem) {
                if (register_date.indexOf(elem.start) == -1) {
                    if (elem.start > now_date && elem.title === '・未登録') {
                        on++;
                    }
                }
            });
            if (on > 0) {
                $('#undata_change_btn').removeClass('disabled');
            }
        },
        // view カレンダー
        render_cal_data: function() {
            model.get_work_data().done(function(data) { // 勤務状況取得
                model.clear_shift_data();
                calendar.addEventSource(data); // カレンダーへ反映
                model.get_shift_data().done(function(data) { // シフトデータ取得
                    calendar.addEventSource(data); // カレンダーへ反映
                    shiftData = data; // shiftData変数
                    model.get_register_data().done(function(data) { // 申請データ取得
                        view.render_undata_change_btn(data);
                        calendar.addEventSource(data);
                    });
                });
            });
        }
    }

    $(function() {
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
        model.get_notice_data(); // 通知データ取得
        view.renderDate();
        model.cat_calendar_holiday_data().done(function(data) {
            view.rendar_cal_holiday(data);
        });
        view.render_cal_data(); // カレンダーデータ表示

        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: ['dayGrid'],
            timeZone: 'local',
            defaultView: 'dayGridMonth',
            locale: 'ja',
            firstDay: shift_cal_first_day,
            header: {
                left: '',
                center: '',
                right: ''
            },
            eventClick: function(info) {
                $('.fc-day-grid-event').css('opacity', 1);
                $('.shift_today, .shift_past, .in-register').css('opacity', 0.5);
                info.el.style.opacity = 0.5;
                view.renderSelect(info);
            }
        });
        calendar.render();

        $('#less_month').on('click', function(e) {
            date = addDate(new Date(date), -1, 'MM');
            month_diff--;
            view.renderDate();
            calendar.prev();
            model.cat_calendar_holiday_data().done(function(data) {
                view.rendar_cal_holiday(data);
            });
            view.render_cal_data(); // カレンダーデータ表示
        });
        $('#add_month').on('click', function(e) {
            date = addDate(new Date(date), 1, 'MM');
            month_diff++;
            view.renderDate();
            calendar.next();
            model.cat_calendar_holiday_data().done(function(data) {
                view.rendar_cal_holiday(data);
            });
            view.render_cal_data(); // カレンダーデータ表示
        });
        $('#this_month_mark, #this_month').on('click', function(e) {
            date = new Date();
            date.setDate(1);
            view.renderDate();
            calendar.today();
            model.cat_calendar_holiday_data().done(function(data) {
                view.rendar_cal_holiday(data);
            });
            view.render_cal_data(); // カレンダーデータ表示
        });
        // 予定ボタン クリック
        $(document).on('change', '[name="status"]', function() {
            $('.radio-text').removeClass('on');
            $(this).next('label').addClass('on');
            var status_id = $(this).val();
            $('#time_area').hide();
            if (status_id == 0) {
                $('#time_area').show();
            }
        });
        // 申請ボタン クリック
        $(document).on('click', '#shift_register_btn', function() {
            var select_date = $(this).attr('data-date');
            model.shift_register(select_date).done(function(data) {
                if (data === 'ok') {
                    view.render_cal_data(); // カレンダーデータ表示
                }
                $('#list_date').html('<i class="fas fa-calendar"></i> カレンダーを選択');
                $('#statue_area, #time_area').hide();
                $('#shift_register_btn').addClass('disabled');
                in_picker.setHour(shift_first_hour);
                in_picker.setMinute(0);
                out_picker.setHour(shift_end_hour);
                out_picker.setMinute(0);
            })
        });
        // header menu クリック
        $(document).on('click', '.header-menu-btn', function() {
            $(this).next('.dropdown-menu').toggleClass('show');
        });
        //
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.dropdown-menu').length && !$(event.target).closest('.header-menu-btn').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });
        // 通知クリック
        $(document).on('click', '.dropdown-item', function() {
            Cookies.set('notice_id', $(this).attr('id'));
            location.href = '/mypage_notice';
        });
        // 未登録 → 公休　クリック
        $(document).on('click', '#undata_change_btn', function() {
            model.nudata_change_save().done(function(data) {
                if (data === 'ok') {
                    view.render_cal_data(); // カレンダーデータ表示
                }
            });
        });
        // 全て未登録にする　クリック
        $(document).on('click', '#del_btn', function() {
            model.nudata_del_save().done(function(data) {
                if (data === 'ok') {
                    view.render_cal_data(); // カレンダーデータ表示
                }
            });
        });

        var TimePicker = tui.TimePicker;
        in_picker = new tui.TimePicker('#in_time_picker', {
            initialHour: 15,
            initialMinute: 13,
            inputType: 'spinbox',
            showMeridiem: false,
            initialHour: shift_first_hour,
            minuteStep: shift_input_hour
        });
        out_picker = new tui.TimePicker('#out_time_picker', {
            initialHour: 15,
            initialMinute: 13,
            inputType: 'spinbox',
            showMeridiem: false,
            initialHour: shift_end_hour,
            minuteStep: shift_input_hour
        });
    });

}());
