(function() {
    var table_list_user;
    var datepicker;
    var download_column;
    var download_data;
    var all_data = {};

    var date;

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

    // minute(分) を 00:00 形式に変換して返す
    function minuteToStr(minute) {
        return Math.floor(minute / 60) + ':' + ('0' + minute % 60).slice(-2);
    }

    // MODELS
    const model = {
        // model テーブルコラムデータ取得
        getTableColumns: function() {
            return $.ajax({
                url: '../data/columns/list_user',
                dataType: 'json',
                type: 'POST'
            })
        },
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
            input3.setAttribute('value', 'list_user');
            form.appendChild(input3);
            var input4 = document.createElement('input');
            input4.setAttribute('type', 'hidden');
            input4.setAttribute('name', 'data_date');
            input4.setAttribute('value', formatDate(new Date(date), 'YYYY年MM月'));
            form.appendChild(input4);
            var input5 = document.createElement('input');
            input5.setAttribute('type', 'hidden');
            input5.setAttribute('name', 'all_data');
            input5.setAttribute('value', JSON.stringify(all_data));
            form.appendChild(input5);
            form.submit();
        },
        listData: function(list_data) {
            // 勤務日数
            let all_work_count = list_data.filter( element => element.work_count > 0 );
            all_data['work_count'] = all_work_count.reduce( (result, current) => {
                return result + Number(current.work_count)
            }, 0);

            // 総労働時間
            let all_work_data = list_data.filter( element => element.work_hour2 > 0 );
            all_data['work_minute'] = all_work_data.reduce( (result, current) => {
                return result + Number(current.work_hour2)
            }, 0);
            all_data['work_hour'] = all_data['work_minute'] > 0 ? minuteToStr(all_data['work_minute']) : '';

            // 総通常時間
            let all_normal_data = list_data.filter( element => element.normal_hour2 > 0 );
            all_data['normal_minute'] = all_normal_data.reduce( (result, current) => {
                return result + Number(current.normal_hour2)
            }, 0);
            all_data['normal_hour'] = all_data['normal_minute'] > 0 ? minuteToStr(all_data['normal_minute']) : '';

            // 残業日数
            let all_over_count = list_data.filter( element => element.over_count > 0 );
            all_data['over_count'] = all_over_count.reduce( (result, current) => {
                return result + Number(current.over_count)
            }, 0);

            // 総残業時間
            let all_over_data = list_data.filter( element => element.over_hour2 > 0 );
            all_data['over_minute'] = all_over_data.reduce( (result, current) => {
                return result + Number(current.over_hour2)
            }, 0);
            all_data['over_hour'] = all_data['over_minute'] > 0 ? minuteToStr(all_data['over_minute']) : '';

            // 深夜日数
            let all_night_count = list_data.filter( element => element.night_count > 0 );
            all_data['night_count'] = all_night_count.reduce( (result, current) => {
                return result + Number(current.night_count)
            }, 0);

            // 総深夜時間
            let all_night_data = list_data.filter( element => element.night_hour2 > 0 );
            all_data['night_minute'] = all_night_data.reduce( (result, current) => {
                return result + Number(current.night_hour2)
            }, 0);
            all_data['night_hour'] = all_data['night_minute'] > 0 ? minuteToStr(all_data['night_minute']) : '';

            // 遅刻日数
            let all_late_count = list_data.filter( element => element.late_count > 0 );
            all_data['late_count'] = all_late_count.reduce( (result, current) => {
                return result + Number(current.late_count)
            }, 0);

            // 総遅刻時間
            let all_late_data = list_data.filter( element => element.late_hour2 > 0 );
            all_data['late_minute'] = all_late_data.reduce( (result, current) => {
                return result + Number(current.late_hour2)
            }, 0);
            all_data['late_hour'] = all_data['late_minute'] > 0 ? minuteToStr(all_data['late_minute']) : '';

            // 早退日数
            let all_left_count = list_data.filter( element => element.left_count > 0 );
            all_data['left_count'] = all_left_count.reduce( (result, current) => {
                return result + Number(current.left_count)
            }, 0);

            // 総早退時間
            let all_left_data = list_data.filter( element => element.left_hour2 > 0 );
            all_data['left_minute'] = all_left_data.reduce( (result, current) => {
                return result + Number(current.left_hour2)
            }, 0);
            all_data['left_hour'] = all_data['left_minute'] > 0 ? minuteToStr(all_data['left_minute']) : '';

            // 有給日数
            let all_paid_count = list_data.filter( element => element.paid_num > 0 );
            all_data['paid_num'] = all_paid_count.reduce( (result, current) => {
                return result + Number(current.paid_num)
            }, 0);

            // 欠勤日数
            let all_absence_count = list_data.filter( element => {
                return element.absence_num > 0;
            });
            all_data['absence_num'] = all_absence_count.reduce( (result, current) => {
                return result + Number(current.absence_num)
            }, 0);

            // 片打刻
            let all_oneside_count = list_data.filter( element => {
                return element.oneside_num > 0;
            });
            all_data['oneside_num'] = all_oneside_count.reduce( (result, current) => {
                return result + Number(current.oneside_num)
            }, 0);

            // 0H出勤
            let all_zero_count = list_data.filter( element => {
                return element.zero_num > 0;
            });
            all_data['zero_num'] = all_zero_count.reduce( (result, current) => {
                return result + Number(current.zero_num)
            }, 0);
        }
    }

    // VIEWS
    const view = {
        renderDate: function() {
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
                    table_list_user.clearData();
                    view.renderTableData();
                    view.renderDateText(date);
                }
            });
            date = datepicker.selectedDates[0];
            date.setDate(1);
            // cookie
            if (Cookies.get('listUser')) {
                date = new Date(Cookies.get('listUser'));
                view.renderDateText(date);
            }
        },
        renderDateText: function(date) {
            //
            datepicker.setDate(date);
            //
            if (end_day > 0) {
                var pre_date = new Date(date);
                pre_date.setMonth(pre_date.getMonth() - 1);
                var pre_day = end_day + 1;
                var first_date = formatDate(pre_date, 'YYYY年MM月') + pre_day + '日';
                var end_date = formatDate(date, 'YYYY年MM月') + end_day + '日';
                var pre_month_days = new Date(pre_date.getFullYear(), pre_date.getMonth() + 1, 0);
                pre_month_days = pre_month_days.getDate();
                var days_num = (pre_month_days - end_day) + end_day;
            } else {
                var first_date = formatDate(new Date(date), 'YYYY年MM月') + '01日';
                var end_date = formatDate(new Date(date.getFullYear(), date.getMonth() + 1, 0), 'YYYY年MM月DD日');
                var pre_month_days = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                var days_num = pre_month_days.getDate();
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
            Cookies.set('listUser', date);
        },
        renderTableColumns: function() {
            model.getTableColumns().done(function(data) {
                // ダウンロード用コラムの生成
                download_column = data.filter(function(o) {
                    return o.output;
                });
                download_column.forEach(function(element, index) {
                    var value = '';
                    if (index === 0) {
                        value = '合計';
                    }
                    if (!element.field) {
                        element.columns.forEach(function(val) {
                            all_data[val.field] = value;
                        })
                    } else {
                        all_data[element.field] = value;
                    }
                });
                table_list_user = new Tabulator('#data_table', {
                    height: 'calc(100vh - 262px)',
                    layout: 'fitDataStretch',
                    columns: data,
                    tooltips: function(cell) {
                        var cells = cell.getColumn();
                        if (cells._column.field == 'work_count' || cells._column.field == 'work_hour' || cells._column.field == 'work_minute') {
                            cell.getElement().style.color = "#1591a2";
                        }
                        if (cells._column.field == 'over_count' || cells._column.field == 'over_hour' || cells._column.field == 'over_minute' || cells._column.field == 'night_count' || cells._column.field == 'night_hour' || cells._column.field == 'night_minute' || cells._column.field == 'late_count' || cells._column.field == 'late_hour' || cells._column.field == 'late_minute' || cells._column.field == 'left_count' || cells._column.field == 'left_hour' || cells._column.field == 'left_minute' || cells._column.field == 'absence_num' || cells._column.field == 'oneside_num' || cells._column.field == 'zero_num') {
                            cell.getElement().style.color = "#ff4560";
                        }
                        if (cells._column.field == 'normal_hour' || cells._column.field == 'normal_minute') {
                            cell.getElement().style.color = "#673AB7";
                        }
                        if (cells._column.field == 'paid_num') {
                            cell.getElement().style.color = "#40a598";
                        }
                    },
                    dataFiltered: function(filters, rows) { // テーブルデータのフィルター時
                        var list_data = [];
                        if (rows.length > 0) {
                            for (var i = 0; i < rows.length; i++) {
                                list_data.push(rows[i]._row.data);
                            }
                            model.listData(list_data);
                            download_data = list_data;
                            view.renderListData();
                            Cookies.set('listUserFilters', table_list_user.getHeaderFilters());
                        }
                    },
                    dataSorted: function(sorters, rows) { // テーブルデータのソート時
                        var list_data = [];
                        if (rows.length > 0) {
                            for (var i = 0; i < rows.length; i++) {
                                list_data.push(rows[i]._row.data);
                            }
                            model.listData(list_data);
                            download_data = list_data;
                            view.renderListData();
                        }
                    },
                    rowClick: function(e, row) {
                        var row_data = row.getData();
                        if (!row_data.user_id) {
                            return;
                        }
                        Cookies.set('toListsMonth', date);
                        Cookies.set('toListsuserID', parseInt(row_data.user_id));
                        Cookies.set('listUserFilters', table_list_user.getHeaderFilters());
                        window.location.href = 'admin_lists';
                    },
                    invalidOptionWarnings: false,
                    dataLoaded: function(data) {
                        if (data.length > 0 && Cookies.get('listUserFilters')) {
                            var filterData = JSON.parse(Cookies.get('listUserFilters'));
                            filterData.forEach(function(item) {
                                table_list_user.setHeaderFilterValue(item.field, item.value);
                            })
                        }
                    },
                });
                view.renderTableData();
            });
        },
        renderTableData: function() {
            table_list_user.replaceData('../data/admin_list_user/table_data', {
                year: formatDate(new Date(date), 'YYYY'),
                month: formatDate(new Date(date), 'MM'),
                end_day: end_day
            }, 'POST');
            table_list_user.hideColumn('work_hour2');
            table_list_user.hideColumn('over_hour2');
            table_list_user.hideColumn('night_hour2');
            table_list_user.hideColumn('late_hour2');
            table_list_user.hideColumn('left_hour2');
            table_list_user.hideColumn('normal_hour2');
        },
        renderListData: function() {
            setTimeout(function() {
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
                $('.tabulator-calcs-top > [tabulator-field="work_count"]').text(all_data['work_count']);
                $('.tabulator-calcs-top > [tabulator-field="over_count"]').text(all_data['over_count']);
                $('.tabulator-calcs-top > [tabulator-field="night_count"]').text(all_data['night_count']);
                $('.tabulator-calcs-top > [tabulator-field="late_count"]').text(all_data['late_count']);
                $('.tabulator-calcs-top > [tabulator-field="left_count"]').text(all_data['left_count']);
                $('.tabulator-calcs-top > [tabulator-field="paid_num"]').text(all_data['paid_num']);
                $('.tabulator-calcs-top > [tabulator-field="absence_num"]').text(all_data['absence_num']);
                $('.tabulator-calcs-top > [tabulator-field="oneside_num"]').text(all_data['oneside_num']);
                $('.tabulator-calcs-top > [tabulator-field="zero_num"]').text(all_data['zero_num']);
            }, 100);
        },
    }

    $(function() {
        view.renderDate();
        view.renderDateText(date);
        view.renderTableColumns();

        // 日付操作
        function changeDate(diff) {
            date = diff ? new Date(date.setMonth(date.getMonth() + diff)) : new Date();
            table_list_user.clearData();
            view.renderTableData();
            view.renderDateText(date);
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

        // ファイルダウンロードボタン
        $('#download_btn_excel').on('click', function() {
            model.downloadData('xlsx');
        });
        $('#download_btn_pdf').on('click', function() {
            model.downloadData('pdf');
        });
    });
})();
