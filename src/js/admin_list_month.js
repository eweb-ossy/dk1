(function() {
    var table_list_month;
    var datepicker;
    var download_column;
    var download_data; // ダウンロード用データ格納

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

    const model = {
        getTableColumns: function() {
            return $.ajax({
                url: '../data/columns/list_month',
                dataType: 'json',
                type: 'POST',
                data: {
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM'),
                    end_day: end_day
                }
            })
        },
        getGraphData: function() {
            return $.ajax({
                url: '../data/list_month/graph_data',
                dataType: 'json',
                type: 'POST',
                data: {
                    year: formatDate(new Date(date), 'YYYY'),
                    month: formatDate(new Date(date), 'MM')
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
            input3.setAttribute('value', 'month');
            form.appendChild(input3);
            var input4 = document.createElement('input');
            input4.setAttribute('type', 'hidden');
            input4.setAttribute('name', 'data_date');
            input4.setAttribute('value', formatDate(new Date(date), 'YYYY年MM月'));
            form.appendChild(input4);
            form.submit();
        },
    }

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
                    table_list_month.clearData();
                    model.getTableColumns().done(function(data) {
                        download_column = data.filter(function(o) {
                            return o.output;
                        });
                        table_list_month.setColumns(data);
                        view.renderTableColumns();
                        view.renderDateText(date);
                    });
                }
            });
            date = datepicker.selectedDates[0];
            date.setDate(1);
            // cookie 
            if (Cookies.get('listMonth')) {
                date = new Date(Cookies.get('listMonth'));
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
            Cookies.set('listMonth', date);
        },
        renderTableColumns: function() {
            model.getTableColumns().done(function(data) {
                download_column = data.filter(function(o) {
                    return o.output;
                });
                table_list_month = new Tabulator('#data_table', {
                    height: 'calc(100vh - 262px)',
                    layout: 'fitDataStretch',
                    columns: data,
                    tooltips: function(cell) {
                        var cells = cell.getColumn();
                        if (cell.getValue()) {
                            if (cells._column.field !== 'user_id' && cells._column.field !== 'user_name' && cells._column.field !== 'group1_name' && cells._column.field !== 'group2_name' && cells._column.field !== 'group3_name') {
                                cell.getElement().style.backgroundColor = "rgba(199, 249, 222, 0.7)";
                            }
                        }
                        if (cells._column.field >= 1 && cells._column.field <= 31) {
                            cell.getElement().style.color = "#1591a2";
                        }
                        if (cells._column.field == 'sum' || cells._column.field == 'time') {
                            cell.getElement().style.color = "#1591a2";
                            cell.getElement().style.backgroundColor = "rgba(244, 244, 244, 0.5)";
                            cell.getElement().style.fontWeight = "bold";
                        }
                    },
                    dataFiltered: function(filters, rows) {
                        var list_data = [];
                        if (rows.length > 0) {
                            for (var i = 0; i < rows.length; i++) {
                                list_data.push(rows[i]._row.data);
                            }
                            download_data = list_data;
                        }
                    },
                    dataSorted: function(sorters, rows) {
                        var list_data = [];
                        if (rows.length > 0) {
                            for (var i = 0; i < rows.length; i++) {
                                list_data.push(rows[i]._row.data);
                            }
                            download_data = list_data;
                        }
                    },
                    rowClick: function(e, row) {
                        var row_data = row.getData();
                        if (!row_data.user_id) {
                            return;
                        }
                        Cookies.set('toListsMonth', date);
                        Cookies.set('toListsuserID', parseInt(row_data.user_id));
                        window.location.href = 'admin_lists';
                    },
                    invalidOptionWarnings: false,
                });
                view.renderTableData();
            });
        },
        renderTableData: function() {
            table_list_month.replaceData('../data/admin_list_month/table_data', {
                year: formatDate(new Date(date), 'YYYY'),
                month: formatDate(new Date(date), 'MM'),
                // flag: 1,
                // end_day: end_day
            }, 'POST');
            table_list_month.hideColumn('group1_name');
            table_list_month.hideColumn('group2_name');
            table_list_month.hideColumn('group3_name');
        },
    }

    $(function() {
        view.renderDate();
        view.renderDateText(date);
        view.renderTableColumns();

        // 月操作
        function changeDate(diff) {
            date = diff ? new Date(date.setMonth(date.getMonth() + diff)) : new Date();
            table_list_month.clearData();
            model.getTableColumns().done( data => {
                download_column = data.filter(function(o) {
                    return o.output;
                });
                table_list_month.setColumns(data);
                view.renderTableColumns();
                view.renderDateText(date);
            });
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
        
        // 詳細表示ボタン　クリック
        $('#table_window_btn').on('click', function() {
            $(this).toggleClass('on');
            table_list_month.toggleColumn('group1_name');
            table_list_month.toggleColumn('group2_name');
            table_list_month.toggleColumn('group3_name');
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