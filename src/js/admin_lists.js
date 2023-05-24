(function() {
    var table_list_user_detail;
    var datepicker;
    var download_column;
    var download_data;
    var all_data = {};
    var timepicker_in; // モーダル用picker
    var timepicker_out; // モーダル用picker
    var timepicker_shift_in; // モーダル用picker
    var timepicker_shift_out; // モーダル用picker
    var row_data = {};
    var shift_status;
    var shift_status_id = 'none';
    var select_shift_time_diff;

    if (Cookies.get('toListsuserID')) {
        var user_id = Cookies.get('toListsuserID');
        var date = new Date(Cookies.get('toListsMonth'));
    } else {
        window.location.href = '/';
    }

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
    const model = {
        // model テーブルコラムデータ取得
        getTableColumns: function() {
            return $.ajax({
                url: '../../data/columns/lists',
                dataType: 'json',
                type: 'POST'
            })
        },
        // model 従業員データ取得
        getUserData: function() {
            return $.ajax({
                url: '../../data/admin_lists/user_data',
                dataType: 'json',
                type: 'POST',
                data: {
                    user_id: user_id,
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM')
                }
            })
        },
        // model モーダル 保存
        save_data: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_list_day/save',
                data: {
                    today: row_data.date,
                    user_id: user_id,
                    in_time: $('#picker_in_time').val(),
                    out_time: $('#picker_out_time').val(),
                    rest: $('#rest_range').val(),
                    memo: $('#memo').val(),
                    area_id: $('select[name="place"] option:selected').attr('data-area-id'),
                    shift_in_time: $('#picker_shift_in_time').val(),
                    shift_out_time: $('#picker_shift_out_time').val(),
                    shift_rest: $('#shift_rest_range').val(),
                    shift_status: shift_status_id,
                    shift_hour: select_shift_time_diff
                }
            })
        },
        // model ファイルダウンロード
        downloadData: function(type) {
            var action = '../../data/admin_download/' + type;
            var form = document.createElement('form');
            form.setAttribute('action', action);
            form.setAttribute('method', 'post');
            form.style.display = "none";
            document.body.appendChild(form);
            var input = document.createElement('input');
            input.setAttribute('type', 'hidden');
            input.setAttribute('name', 'column');
            input.setAttribute('value', JSON.stringify(download_column));
            form.appendChild(input);
            var input2 = document.createElement('input');
            input2.setAttribute('type', 'hidden');
            input2.setAttribute('name', 'dl_data');
            input2.setAttribute('value', JSON.stringify(download_data));
            form.appendChild(input2);
            var input3 = document.createElement('input');
            input3.setAttribute('type', 'hidden');
            input3.setAttribute('name', 'type');
            input3.setAttribute('value', 'user_detail');
            form.appendChild(input3);
            var input4 = document.createElement('input');
            input4.setAttribute('type', 'hidden');
            input4.setAttribute('name', 'data_date');
            input4.setAttribute('value', formatDate(new Date(date), 'YYYY年MM月') + ' ' + $('#user_name').text() + ' ' + $('#user_id').text());
            form.appendChild(input4);
            var input5 = document.createElement('input');
            input5.setAttribute('type', 'hidden');
            input5.setAttribute('name', 'all_data');
            input5.setAttribute('value', JSON.stringify(all_data));
            form.appendChild(input5);
            form.submit();
        },
        // model 集計
        listData: function(list_data) {
            // 総労働時間
            var all_work_data = list_data.filter(function(element) {
            return element.work_hour2 > 0;
            });
            all_data['work_minute'] = all_work_data.reduce(function(result, current) {
            return result + Number(current.work_hour2)
            }, 0);
            if (all_data['work_minute'] > 0) {
            all_data['work_hour'] = Math.floor(all_data['work_minute'] / 60) + ':' + ('0' + all_data['work_minute'] % 60).slice(-2);
            } else {
            all_data['work_hour'] = '';
            }
            // 総通常時間
            var all_normal_data = list_data.filter(function(element) {
            return element.normal_hour2 > 0;
            });
            all_data['normal_minute'] = all_normal_data.reduce(function(result, current) {
            return result + Number(current.normal_hour2)
            }, 0);
            if (all_data['normal_minute'] > 0) {
            all_data['normal_hour'] = Math.floor(all_data['normal_minute'] / 60) + ':' + ('0' + all_data['normal_minute'] % 60).slice(-2);
            } else {
            all_data['normal_hour'] = '';
            }
            // 総シフト時間
            var all_shift_work_data = list_data.filter(function(element) {
            return element.shift_hour2 > 0;
            });
            all_data['shift_hour2'] = all_shift_work_data.reduce(function(result, current) {
            return result + Number(current.shift_hour2)
            }, 0);
            if (all_data['shift_hour2'] > 0) {
            all_data['shift_hour'] = Math.floor(all_data['shift_hour2'] / 60) + ':' + ('0' + all_data['shift_hour2'] % 60).slice(-2);
            } else {
            all_data['shift_hour'] = '';
            }
            // 総休憩時間
            var all_rest_data = list_data.filter(function(element) {
            return element.rest_hour2 > 0;
            });
            all_data['rest_minute'] = all_rest_data.reduce(function(result, current) {
            return result + Number(current.rest_hour2)
            }, 0);
            if (all_data['rest_minute'] > 0) {
            all_data['rest_hour'] = Math.floor(all_data['rest_minute'] / 60) + ':' + ('0' + all_data['rest_minute'] % 60).slice(-2);
            } else {
            all_data['rest_hour'] = '';
            }
            // 総残業時間
            var all_over_data = list_data.filter(function(element) {
            return element.over_hour2 > 0;
            });
            all_data['over_minute'] = all_over_data.reduce(function(result, current) {
            return result + Number(current.over_hour2)
            }, 0);
            if (all_data['over_minute'] > 0) {
            all_data['over_hour'] = Math.floor(all_data['over_minute'] / 60) + ':' + ('0' + all_data['over_minute'] % 60).slice(-2);
            } else {
            all_data['over_hour'] = '';
            }
            // 総深夜時間
            var all_night_data = list_data.filter(function(element) {
            return element.night_hour2 > 0;
            });
            all_data['night_minute'] = all_night_data.reduce(function(result, current) {
            return result + Number(current.night_hour2)
            }, 0);
            if (all_data['night_minute'] > 0) {
            all_data['night_hour'] = Math.floor(all_data['night_minute'] / 60) + ':' + ('0' + all_data['night_minute'] % 60).slice(-2);
            } else {
            all_data['night_hour'] = '';
            }
            // 総遅刻時間
            var all_late_data = list_data.filter(function(element) {
            return element.late_hour2 > 0;
            });
            all_data['late_minute'] = all_late_data.reduce(function(result, current) {
            return result + Number(current.late_hour2)
            }, 0);
            if (all_data['late_minute'] > 0) {
            all_data['late_hour'] = Math.floor(all_data['late_minute'] / 60) + ':' + ('0' + all_data['late_minute'] % 60).slice(-2);
            } else {
            all_data['late_hour'] = '';
            }
            // 総早退時間
            var all_left_data = list_data.filter(function(element) {
            return element.left_hour2 > 0;
            });
            all_data['left_minute'] = all_left_data.reduce(function(result, current) {
            return result + Number(current.left_hour2)
            }, 0);
            if (all_data['left_minute'] > 0) {
            all_data['left_hour'] = Math.floor(all_data['left_minute'] / 60) + ':' + ('0' + all_data['left_minute'] % 60).slice(-2);
            } else {
            all_data['left_hour'] = '';
            }
            // 出勤回数
            var all_in_work_time_data = list_data.filter(function(element) {
            return element.in_work_time;
            });
            all_data['in_work_time'] = all_in_work_time_data.length;
            // 退勤回数
            var all_out_work_time_data = list_data.filter(function(element) {
            return element.out_work_time;
            });
            all_data['out_work_time'] = all_out_work_time_data.length;
        },
        // model 従業員セレクトデータ取得
        getSelectUsers: function() {
            var end_date = formatDate(new Date(date.getFullYear(), date.getMonth() + 1, 0), 'YYYY-MM-DD');
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_lists/select_users',
                data: {
                    now_date: end_date
                }
            })
        }
    }

    function tableDataPush(rows) { // テーブルデータ格納用 function 
        let list_data = [];
        if (rows.length > 0) {
            for (var i = 0; i < rows.length; i++) {
                list_data.push(rows[i]._row.data);
            }
            model.listData(list_data);
            download_data = list_data;
        }
    }
    const view = {
        // view date picker 年月表示
        renderDate: new Promise(function(resolve, reject) {
            datepicker = flatpickr('#month', {
                plugins: [
                    new monthSelectPlugin({
                        dateFormat: "Y年m月", //defaults to "F Y"
                        theme: "light" // defaults to "light"
                    })
                ],
                "onChange": function() {
                    date = datepicker.selectedDates[0];
                    view.renderTableData();
                    view.renderDateText(date);
                }
            });
            resolve();
        }),
        // view 日付、和暦、日付差異等　を表示する
        renderDateText: function(date) {
            datepicker.setDate(date);
            let first_date, end_date, days_num;
            if (end_day > 0) {
                let pre_date = new Date(date);
                pre_date.setMonth(pre_date.getMonth() - 1);
                let pre_day = end_day + 1;
                first_date = formatDate(pre_date, 'YYYY年MM月') + pre_day + '日';
                end_date = formatDate(date, 'YYYY年MM月') + end_day + '日';
                let pre_month_days = new Date(pre_date.getFullYear(), pre_date.getMonth() + 1, 0);
                pre_month_days = pre_month_days.getDate();
                days_num = (pre_month_days - end_day) + end_day;
            } else {
                first_date = formatDate(new Date(date), 'YYYY年MM月') + '01日';
                end_date = formatDate(new Date(date.getFullYear(), date.getMonth() + 1, 0), 'YYYY年MM月DD日');
                let pre_month_days = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                days_num = pre_month_days.getDate();
            }
            $('#to_from_date').text(first_date + 'から' + end_date + 'までの' + days_num + '日間');

            let wareki = new Intl.DateTimeFormat('ja-JP-u-ca-japanese', {era: 'long', year: 'numeric'}).format(new Date(date));
            $('#date-area-wareki').text(wareki);

            // month diff
            let nowDate = new Date();
            let nowYear = nowDate.getFullYear();
            let nowMonth = nowDate.getMonth();
            let selectDate = new Date(date);
            let selectYear = selectDate.getFullYear();
            let selectMonth = selectDate.getMonth();
            let d1 = new Date(nowYear, nowMonth);
            let d2 = new Date(selectYear, selectMonth);
            let months =  d1.getMonth() - d2.getMonth() + (12 * (d1.getFullYear() - d2.getFullYear()));
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
        },
        // view テーブルコラム設定表示
        renderTableColumns: new Promise(function(resolve, reject) {
            model.getTableColumns().done(function(data) {
                // ダウンロード用コラムの生成
                download_column = data.filter( o => o.output); // columns data の output TRUEを抽出
                download_column.forEach( (element, index) => {
                    let value = index === 0 ? '合計' : ''; // 最初の行には'合計'を入れる
                    if (!element.field) { // fieldが無い = グループコラムの時
                        element.columns.forEach( val => {
                            all_data[val.field] = value;
                        })
                    } else {
                        all_data[element.field] = value;
                    }
                });
                // テーブルの初期設定
                table_list_user_detail = new Tabulator('#data_table', {
                    layout: 'fitColumns',
                    height: 'calc(100vh - 270px)',
                    columnMinWidth: 20,
                    columns: data,
                    tooltips: function(cell) { // テーブルのカラーリング
                        let cells = cell.getColumn();
                        if (cells._column.field == 'in_work_time' || cells._column.field == 'out_work_time' || cells._column.field == 'work_hour' || cells._column.field == 'work_minute' || cells._column.field == 'rest_hour' || cells._column.field == 'rest_minute' || cells._column.field == 'status') {
                            cell.getElement().style.color = "#1591a2";
                        }
                        if (cells._column.field == 'over_hour' || cells._column.field == 'over_minute' || cells._column.field == 'night_hour' || cells._column.field == 'night_minute' || cells._column.field == 'late_hour' || cells._column.field == 'late_minute' || cells._column.field == 'left_hour' || cells._column.field == 'left_minute') {
                            cell.getElement().style.color = "#ff4560";
                        }
                        if (cells._column.field == 'normal_hour' || cells._column.field == 'normal_minute') {
                            cell.getElement().style.color = "#673AB7";
                        }
                        if (cells._column.field == 'shift_in_time' || cells._column.field == 'shift_out_time' || cells._column.field == 'shift_hour') {
                            cell.getElement().style.color = "#9abcea";
                        }
                        let val = cell.getValue();
                        if (val == '日' || val == '祝') {
                            cell.getElement().style.color = "#F44336";
                        }
                        if (val == '土') {
                            cell.getElement().style.color = "#0D47A1";
                        }
                        if (val == '片打刻') {
                            cell.getElement().style.color = "#ff4560";
                        }
                        if (val == '未出勤') {
                            cell.getElement().style.color = "#673ab7";
                        }
                        if (val == '出勤') {
                            cell.getElement().style.color = "#9abcea";
                        }
                        if (val == '公休') {
                            cell.getElement().style.color = "#F44336";
                        }
                        if (val == '有給' || val == '有給取得') {
                            cell.getElement().style.color = "#fff";
                            cell.getElement().style.background = "#40a598";
                        }
                        if (val == '欠勤') {
                            cell.getElement().style.color = "#fff";
                            cell.getElement().style.background = "#fd6b80";
                        }
                    },
                    dataFiltered: function(filters, rows) { // テーブルデータのフィルター時
                        tableDataPush(rows);
                    },
                    dataSorted: function(sorters, rows) { // テーブルデータのソート時
                        tableDataPush(rows);
                    },
                    rowClick: function(e, row) { // テーブルのクリック時
                        if (authority === 2 || authority === 5) {
                            return;
                        }
                        row_data = row.getData();
                        if (!row_data.day) {
                            return;
                        }
                        view.render_modal(row_data);
                    },
                    invalidOptionWarnings: false, // 無効なオプションの警告を無効にする
                });
                resolve();
                table_list_user_detail.hideColumn('work_hour2');
                table_list_user_detail.hideColumn('rest_hour2');
                table_list_user_detail.hideColumn('over_hour2');
                table_list_user_detail.hideColumn('night_hour2');
                table_list_user_detail.hideColumn('late_hour2');
                table_list_user_detail.hideColumn('left_hour2');
                table_list_user_detail.hideColumn('today_flag');
                table_list_user_detail.hideColumn('in_latitude');
                table_list_user_detail.hideColumn('in_longitude');
                table_list_user_detail.hideColumn('out_latitude');
                table_list_user_detail.hideColumn('out_longitude');
                table_list_user_detail.hideColumn('normal_hour2');
                table_list_user_detail.hideColumn('shift_hour2');
                table_list_user_detail.hideColumn('shift_rest');
                table_list_user_detail.hideColumn('area_id');
                table_list_user_detail.hideColumn('date');
            });
        }),
        // view テーブルデータ表示
        renderTableData: function() {
            table_list_user_detail.clearData();
            table_list_user_detail.replaceData('../../data/admin_lists/table_data', {
            year: formatDate(new Date(date), 'YYYY'),
            month: formatDate(new Date(date), 'MM'),
            user_id: user_id,
            end_day: end_day
            }, 'POST')
            .then(function() {
                view.renderListData();
            });
        },
        // view テーブル上部　従業員データ表示
        renderUserData: function() {
            model.getUserData().done(function(data) {
                $('#user_kana').text(data.user_kana);
                $('#user_name').text(data.user_name);
                $('#user_id').text(user_id);
                $('#group1_name').text(data.group1_name);
                $('#group2_name').text(data.group2_name);
                $('#group3_name').text(data.group3_name);
            });
        },
        // view 時刻修正　モーダル　表示
        render_modal: function(row_data) {
            // モーダル 日付表示
            $('#modal_date').text(row_data.date.substr(0, 4)+"年"+row_data.date.substr(5, 2)+"月"+row_data.day+"日"+"("+row_data.week+")");
            var defaultDate = row_data.in_work_time ? row_data.in_work_time : '';
            // モータル用 time picker
            timepicker_in = flatpickr('#picker_in_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: defaultInHour,
                defaultMinute: defaultInMinute,
                minuteIncrement: defaultMinuteIncrement,
                defaultDate: defaultDate,
                onChange: function() {
                    view.render_modal_check();
                },
                onClose: function() {
                    view.render_modal_check();
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
                defaultDate: row_data.out_work_time,
                onChange: function() {
                    view.render_modal_check();
                },
                onClose: function() {
                    view.render_modal_check();
                }
            });
            var defaultShiftDate = row_data.shift_in_time ? row_data.shift_in_time : '';
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
                defaultDate: row_data.shift_out_time,
                onChange: function() {
                    view.render_modal_check();
                },
                onClose: function() {
                    view.render_modal_check();
                }
            });
            // モーダル 出退勤時刻
            var in_time = row_data.in_work_time ? row_data.in_work_time : '--';
            $('#in_time').text(in_time);
            var out_time = row_data.out_work_time ? row_data.out_work_time : '--';
            $('#out_time').text(out_time);
            var work_val = row_data.work_hour2 ? row_data.work_hour2+'分' : '--';
            $('#work_value').text(work_val);
            var work_val2 = row_data.work_hour ? row_data.work_hour : '';
            $('#work_value2').text(work_val2);
            // モーダル 休憩
            $('#rest_range').prop('disabled', false).val(Number(row_data.rest_hour2));
            // $('.rest-btn-area').show();
            var rest_val = row_data.rest_hour2 ? row_data.rest_hour2 : 0;
            view.render_rest_btn(rest_val);
            var rest_val2 = row_data.rest_hour ? row_data.rest_hour : '0:00';
            $('#rest_value2').text(rest_val2);
            // モーダル　エリア
            if (row_data.area) {
                $('[name="place"] > option[value="' + row_data.area + '"]').prop('selected', true);
            } else {
                $('[name="place"] > option[value=""]').prop('selected', true);
            }
            // モーダル メモ
            $('#memo').val(row_data.memo);
            // モーダル ヘッダー部
            $('.iziModal-header-title').text($('#user_name').text());
            var group1_title = $('#group1_name').text();
            var group2_title = $('#group2_name').text();
            var group3_title = $('#group3_name').text();
            $('.iziModal-header-subtitle').text('ID：' + user_id + '　' + group1_title + '　' + group2_title + '　' + group3_title);
            // モーダル GPSデータ
            if (row_data.in_latitude && row_data.in_longitude) {
                $('#map_in').html('<iframe src="https://maps.google.co.jp/maps?output=embed&t=m&hl=ja&z=15&ll=' + row_data.in_latitude + ',' + row_data.in_longitude + '&q=' + row_data.in_latitude + ',' + row_data.in_longitude + '" frameborder="0" scrolling="no" width="323px" height="78px"></iframe>');
            } else {
                $('#map_in').html('');
            }
            if (row_data.out_latitude && row_data.out_longitude) {
                $('#map_out').html('<iframe src="https://maps.google.co.jp/maps?output=embed&t=m&hl=ja&z=15&ll=' + row_data.out_latitude + ',' + row_data.out_longitude + '&q=' + row_data.out_latitude + ',' + row_data.out_longitude + '" frameborder="0" scrolling="no" width="323px" height="78px"></iframe>');
            } else {
                $('#map_out').html('');
            }
            // モーダル シフト
            var shift_in_time = row_data.shift_in_time ? row_data.shift_in_time : '--';
            $('#shift_in_time').text(shift_in_time);
            var shift_out_time = row_data.shift_out_time ? row_data.shift_out_time : '--';
            $('#shift_out_time').text(shift_out_time);
            var shift_val = row_data.shift_hour2 ? row_data.shift_hour2+'分' : '--';
            $('#shift_value').text(shift_val);
            var shift_val2 = row_data.shift_hour ? row_data.shift_hour : '';
            $('#shift_value2').text(shift_val2);
            shift_status = row_data.shift_status;
            view.render_shift_btn(shift_status);
            // モーダル シフト休憩
            $('#shift_rest_range').prop('disabled', false).val(Number(row_data.shift_rest));
            // $('.rest-btn-area').show();
            var shift_rest_val = row_data.shift_rest ? row_data.shift_rest : 0;
            view.render_shift_rest_btn(shift_rest_val);
            var rest_val2 = row_data.rest_hour ? row_data.rest_hour : '0:00';
            $('#rest_value2').text(rest_val2);
            //
            $('#time_edit_submit').attr('data-user-id', row_data.user_id);
            $('#time_edit_submit').addClass('disable');
            $('#modal2').iziModal('open');
        },
        // view モーダル　休憩　定時時刻　アクティブ表示
        render_rest_btn: function(rest_val) {
            $('.r_btn').removeClass('active');
            $('#rest_' + rest_val).addClass('active');
            $('#memori_' + rest_val).addClass('active');
            $('#rest_value').text(rest_val + '分');
            var hours = (rest_val / 60);
            var rhours = Math.floor(hours);
            var minutes = (hours - rhours) * 60;
            var rminutes = Math.round(minutes);
            $('#rest_value2').text(rhours+':'+('0' + rminutes).slice(-2));
            $('#rest_range').val(rest_val);
            view.render_modal_check();
        },
        // view モーダル　シフト休憩　定時時刻　アクティブ表示
        render_shift_rest_btn: function(shift_rest_val) {
            $('.rs_btn').removeClass('active-s');
            $('#shift_rest_' + shift_rest_val).addClass('active-s');
            $('#shift_memori_' + shift_rest_val).addClass('active-s');
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
            shift_status_id = 'none';
            if (data === '未登録' || data === '') {
                $('#state_none').addClass('disabled');
                shift_status = '';
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
                shift_status = '公休';
                shift_status_id = 1;
                var color = '#ff7d73';
            }
            if (data === '有給') {
                $('#state_raid').addClass('disabled');
                shift_status = '有給';
                shift_status_id = 2;
                var color = '#40a598';
            }
            $('#status_name').text('シフト '+shift_status).css('color', color);
            // view.render_modal_check();
        },
        // view モーダル バリデーション
        render_modal_check: function() {
            $('#time_edit_submit').addClass('disable');
            $('.time-input, .rest-value, .shift-rest-value').removeClass('error');
            $('#work_value, #shift_value').text('--');
            $('#work_value2, #shift_value2').text('');
            let datetime_in = timepicker_in.selectedDates; // 出勤時刻
            let datetime_out = timepicker_out.selectedDates; // 退勤時刻
            let select_in_time_hour = new Date(datetime_in).getHours(); // 出勤時刻 - 時
            let select_in_time_minute = new Date(datetime_in).getMinutes(); // 出勤時刻 - 分
            let select_in_time = select_in_time_hour * 60 + select_in_time_minute; // 出勤時間（分）
            let select_out_time_hour = new Date(datetime_out).getHours(); // 退勤時刻 - 時
            let select_out_time_minute = new Date(datetime_out).getMinutes(); // 退勤時刻 - 分
            let select_out_time = select_out_time_hour * 60 + select_out_time_minute; // 退勤時間（分）
            let in_time_val = $('#picker_in_time').val();
            let out_time_val = $('#picker_out_time').val();
            if (over_day > 0) { // 日またぎ処理
                if (select_in_time_hour <= over_day) {
                    select_in_time += 1440; // 24H
                    let hour = in_time_val.substr(0, 2);
                    let minute = in_time_val.substr(-2);
                    hour = Number(hour)+24;
                    in_time_val = hour + ':' + minute;
                }
                if (select_out_time_hour <= over_day) {
                    select_out_time += 1440; // 24H
                    let hour = out_time_val.substr(0, 2);
                    let minute = out_time_val.substr(-2);
                    hour = Number(hour)+24;
                    out_time_val = hour + ':' + minute;
                }
            }
            let rest = $('#rest_range').val();

            // 出勤時刻 なし + 退勤時刻 なし + 休憩時間 > 0
            if (!select_in_time && !select_out_time && rest > 0) {
                $('.rest-value').addClass('error');
                return;
            }
            // 出勤時刻 あり + 退勤時刻 なし + 休憩時間 > 0
            if (select_in_time && !select_out_time && rest > 0) {
                $('.rest-value').addClass('error');
                return;
            }
            // 出勤時刻 なし + 退勤時刻 あり
            if (!select_in_time && select_out_time) {
            // if (!select_in_time && select_out_time || !select_in_time && select_out_time === 0) {
                $('#picker_in_time').addClass('error'); // 退勤時刻のみ -> error
                // 出勤時刻 なし + 退勤時刻 あり + 休憩時間 > 0
                if (rest > 0) {
                    $('.rest-value').addClass('error');
                }
                return;
            }
            // 出勤時刻 あり + 退勤時刻 あり
            if (select_in_time && select_out_time) {
                let select_time_diff = select_out_time - select_in_time; // 勤務時間（休憩時間引かない）
                // 勤務時間 - 休憩時間 が 0 もしくは マイナス
                if ((select_time_diff - rest) <= 0) {
                    $('#picker_out_time').addClass('error');
                    return;
                }
                // 勤務時間 が 休憩時間 より 少ない
                if (select_time_diff <= rest) {
                    $('.rest-value').addClass('error');
                    return;
                }
                select_time_diff -= rest; // 勤務時間 = 勤務時間 -休憩時間
                $('#work_value').text(select_time_diff+'分'); // 勤務時間(分）を表示
                let hours = (select_time_diff / 60);
                let rhours = Math.floor(hours);
                let minutes = (hours - rhours) * 60;
                let rminutes = Math.round(minutes);
                $('#work_value2').text(rhours+':'+('0' + rminutes).slice(-2)); // 勤務時間 0:00 を表示
            }
            // シフト出勤時
            let in_shift_time_val, out_shift_time_val, shift_rest;
            if (shift_status === '出勤') {
                let select_shift_in_time_hour = new Date(timepicker_shift_in.selectedDates).getHours();
                let select_shift_in_time_minute = new Date(timepicker_shift_in.selectedDates).getMinutes();
                let select_shift_in_time = select_shift_in_time_hour * 60 + select_shift_in_time_minute;
                let select_shift_out_time_hour = new Date(timepicker_shift_out.selectedDates).getHours();
                let select_shift_out_time_minute = new Date(timepicker_shift_out.selectedDates).getMinutes();
                let select_shift_out_time = select_shift_out_time_hour * 60 + select_shift_out_time_minute;
                in_shift_time_val = $('#picker_shift_in_time').val();
                out_shift_time_val = $('#picker_shift_out_time').val();
                if (over_day > 0) { // 日またぎ処理
                    if (select_shift_in_time_hour <= over_day) {
                        select_shift_in_time += 1440; // 24H
                        let hour = in_shift_time_val.substr(0, 2);
                        let minute = in_shift_time_val.substr(-2);
                        hour = Number(hour)+24;
                        in_shift_time_val = hour + ':' + minute;
                    }
                    if (select_shift_out_time_hour <= over_day) {
                        select_shift_out_time += 1440; // 24H
                        let hour = out_shift_time_val.substr(0, 2);
                        let minute = out_shift_time_val.substr(-2);
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
                    shift_rest = $('#shift_rest_range').val();
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
                    let hours = (select_shift_time_diff / 60);
                    let rhours = Math.floor(hours);
                    let minutes = (hours - rhours) * 60;
                    let rminutes = Math.round(minutes);
                    $('#shift_value2').text(rhours+':'+('0' + rminutes).slice(-2));
                }
            }

            // 元データ と 差異があった場合、 submit ボタンを active にする
            if (row_data.in_work_time != in_time_val) { // 出勤時刻が違う
                $('#time_edit_submit').removeClass('disable');
            }
            if (row_data.out_work_time != out_time_val) { // 退勤時刻が違う
                $('#time_edit_submit').removeClass('disable');
            }
            let rest_hour2 = row_data.rest_hour2 ? row_data.rest_hour2 : 0;
            if (rest_hour2 != rest) { // 休憩時間が違う
                $('#time_edit_submit').removeClass('disable');
            }
            let area_id = $('select[name="place"] option:selected').attr('data-area-id');
            if (row_data.area_id != area_id) { // エリア が 違う
                $('#time_edit_submit').removeClass('disable');
            }
            let memo = $('#memo').val();
            if (row_data.memo != memo) { // メモが違う
                $('#time_edit_submit').removeClass('disable');
            }
            if (row_data.shift_status != shift_status) { // シフトステータスが違う
                $('#time_edit_submit').removeClass('disable');
            }
            if (shift_status === '出勤') {
                if (row_data.shift_in_time != in_shift_time_val) {
                    $('#time_edit_submit').removeClass('disable');
                }
                if (row_data.shift_out_time != out_shift_time_val) {
                    $('#time_edit_submit').removeClass('disable');
                }
                let shift_rest_data = row_data.shift_rest ? row_data.shift_rest : 0;
                if (shift_rest_data != shift_rest) {
                    $('#time_edit_submit').removeClass('disable');
                }
            }
        },
        // view 表上部　合計値表示
        renderListData: function() {
            $('.tabulator-calcs-top > [tabulator-field="shift_hour"]').text(all_data['shift_hour']);
            $('.tabulator-calcs-top > [tabulator-field="work_hour"]').text(all_data['work_hour']);
            $('.tabulator-calcs-top > [tabulator-field="work_minute"]').text(all_data['work_minute']);
            $('.tabulator-calcs-top > [tabulator-field="rest_hour"]').text(all_data['rest_hour']);
            $('.tabulator-calcs-top > [tabulator-field="rest_minute"]').text(all_data['rest_minute']);
            $('.tabulator-calcs-top > [tabulator-field="over_hour"]').text(all_data['over_hour']);
            $('.tabulator-calcs-top > [tabulator-field="over_minute"]').text(all_data['over_minute']);
            $('.tabulator-calcs-top > [tabulator-field="night_hour"]').text(all_data['night_hour']);
            $('.tabulator-calcs-top > [tabulator-field="night_minute"]').text(all_data['night_minute']);
            $('.tabulator-calcs-top > [tabulator-field="late_hour"]').text(all_data['late_hour']);
            $('.tabulator-calcs-top > [tabulator-field="late_minute"]').text(all_data['late_minute']);
            $('.tabulator-calcs-top > [tabulator-field="left_hour"]').text(all_data['left_hour']);
            $('.tabulator-calcs-top > [tabulator-field="left_minute"]').text(all_data['left_minute']);
            $('.tabulator-calcs-top > [tabulator-field="normal_hour"]').text(all_data['normal_hour']);
            $('.tabulator-calcs-top > [tabulator-field="normal_minute"]').text(all_data['normal_minute']);
        },
        // view 従業員セレクト設定
        renderUserSelect: function() {
            model.getSelectUsers().done(function(data) {
                $('#user_select').html('');
                // $('#user_select').append('<option value="">-- 従業員切り替え --</option>');
                $.each(data, function(key, elem) {
                    var select = user_id == elem.user_id ? ' selected' : ''
                    $('#user_select').append('<option value="' + elem.user_id + '"' + select + '>' + elem.user_name + '&nbsp;(' + elem.user_id + ')</option>');
                });
            });
        },
        // view 曜日セレクト
        renderWeekSelect: function(week_filter) {
            if (week_filter === 'none') {
                table_list_user_detail.clearFilter();
                return;
            }
            if (week_filter !== '土日祝' || week_filter !== '平日') {
                table_list_user_detail.setFilter('week', '=', week_filter);
            }
            if (week_filter === '土日祝') {
                table_list_user_detail.setFilter('week', 'in', ['土', '日', '祝']);
            }
            if (week_filter === '平日') {
                table_list_user_detail.setFilter('week', 'in', ['月', '火', '水', '木', '金']);
            }
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
        // 初期データ読み込み・表示
        Promise.all([view.renderTableColumns, view.renderDate]).then(function() {
            view.renderDateText(date); //
            view.renderUserData(); // 従業員名表示
            view.renderUserSelect(); // 従業員セレクト表示
            view.renderTableData(); // テーブルデータ表示
        });
        $('#modal2').iziModal({ // モーダル設定
            headerColor: '#1591a2',
            focusInput: false,
            width: 700,
        });
        
        // 日付操作
        function changeDate(diff) {
            date = diff ? new Date(date.setMonth(date.getMonth() + diff)) : new Date();
            view.renderDateText(date);
            view.renderTableData();
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

        // 従業員セレクト
        $('#user_select').on('change', function() {
            user_id = $('select[name="user_select"] option:selected').val();
            view.renderTableData();
            view.renderUserData();
        });
        // 曜日抽出
        $('#week_select').on('change', function() {
            week_filter = $('select[name="week_select"] option:selected').val();
            view.renderWeekSelect(week_filter);
        });
        // // 締め日変更
        // $(document).delegate('#end_day_select', 'change', function() {
        // end_day = Number($('select[name="end_day"] option:selected').val());
        // Promise.all([view.renderTableColumns, view.renderDate]).then(function() {
        // date = datepicker.selectedDates[0]; // 表示されてる年月日取得
        // view.renderDateText(date); //
        // view.renderTableData(); // テーブルデータ表示
        // });
        // });

        // モーダル 操作
        $(document).on('input', '#rest_range', function() { // 休憩スライダー操作　時間表示
            var rest_val = $(this).val();
            view.render_rest_btn(rest_val);
        });
        $(document).on('click', '.r_btn', function() { // 休憩定時ボタン　クリック
            var rest_val = $(this).attr('data-time');
            view.render_rest_btn(rest_val);
        });
        $(document).on('input change', '.time-val', function() { // モーダル入力変更があった場合
            view.render_modal_check();
        });
        $(document).on('click', '#picker_del_in_time', function() {
            timepicker_in.clear();
            view.render_modal_check();
        });
        $(document).on('click', '#picker_del_out_time', function() {
            timepicker_out.clear();
            view.render_modal_check();
        });
        $(document).on('click', '#shift_view_btn', function() { // shift view
            $(this).toggleClass('active-shift');
            $('#shift_btn_area').slideToggle();
            view.render_shift_btn(row_data.shift_status);
        });
        $(document).on('click', '.shift-btn', function() { // シフトステータスボタン
            view.render_shift_btn($(this).text());
            view.render_modal_check();
        });
        $(document).on('input', '#shift_rest_range', function() { // シフト休憩スライダー操作　時間表示
            var rest_val = $(this).val();
            view.render_shift_rest_btn(rest_val);
        });
        $(document).on('click', '.rs_btn', function() { // シフト休憩定時ボタン　クリック
            var rest_val = $(this).attr('data-time');
            view.render_shift_rest_btn(rest_val);
        });
        $(document).on('click', '#picker_shift_del_in_time', function() {
            timepicker_shift_in.clear();
            view.render_modal_check();
        });
        $(document).on('click', '#picker_shift_del_out_time', function() {
            timepicker_shift_out.clear();
            view.render_modal_check();
        });
        $(document).on('click', '#time_edit_submit', function() { // 修正登録ボタン　クリック
            model.save_data().done(function(data) {
            if (data.message === 'ok') {
                $('#modal2').iziModal('close');
                view.show_toast(data.today + ' 修正登録 ' + row_data.user_name + ' ' + data.user_id); // トースト表示
                view.renderTableData();
            } else {
                view.show_err_toast('通信エラー');
            }
            }).fail(function() {
                view.show_err_toast('通信エラー');
            });
        });

        // ファイルダウンロードボタン
        $('#download_btn_excel').on('click', function() {
            model.downloadData('xlsx');
        });
        $('#download_btn_pdf').on('click', function() {
            model.downloadData('pdf');
        });
    });
}());
