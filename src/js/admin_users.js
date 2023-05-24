(function() {
    var table_list_users; // table
    var download_column;
    var download_data; // ダウンロード用データ格納

    var model = {
        // model テーブルコラム取得
        getTableColumns: function() {
            return $.ajax({
                url: '../data/columns/users',
                dataType: 'json',
                type: 'POST'
            })
        },
        // model 集計
        listData: function(list_data) {
            var all_count = list_data.length; // 登録従業員数
            var all_state = list_data.filter(function(element) { // 既存従業員データ
                return element.state == 1;
            });
            var all_resign = list_data.filter(function(element) { // 退職従業員データ
                return element.state == 2;
            });
            var total_data = {
                'all_count': all_count,
                'all_state_count': all_state.length,
                'all_resign_count': all_resign.length
            }
            view.renderTotal(total_data);
        },
        // model 集計　フィルター
        listFilterData: function(filter_data) {
            var sex_man_data = filter_data.filter(function(element) {
                return element.sex == '男';
            });
            var sex_woman_data = filter_data.filter(function(element) {
                return element.sex == '女';
            });
            var sex_no_data = filter_data.filter(function(element) {
                return element.sex == '';
            });
            var total_filter_data = {
                'count_all': filter_data.length,
                'sex_man': sex_man_data.length,
                'sex_woman': sex_woman_data.length,
                'sex_no': sex_no_data.length
            }
            view.renderFilterTotal(total_filter_data);
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
            input3.setAttribute('value', 'users');
            form.appendChild(input3);
            var input4 = document.createElement('input');
            input4.setAttribute('type', 'hidden');
            input4.setAttribute('name', 'data_date');
            input4.setAttribute('value', '');
            form.appendChild(input4);
            form.submit();
        },
        // model data 保存
        saveData: function(change_data) {
            return $.ajax({
                type: 'POST',
                // dataType: 'json',
                url: '../../data/admin_users/save_data',
                data: {
                    user_id: change_data['user_id'],
                    field: change_data['field'],
                    value: change_data['value']
                }
            })
        }
    }

    var view = {
        // view テーブルコラム　表示
        renderTableColumns: new Promise(function(resolve, reject) {
            model.getTableColumns().done(function(data) {
                // ダウンロード用コラムの生成
                download_column = data.filter(function(o) {
                    return o.output;
                });
                table_list_users = new Tabulator('#data_table', {
                    height: 'calc(100vh - 280px)',
                    columns: data,
                    dataLoaded: function(rows) {
                        model.listData(rows);
                    },
                    dataFiltered: function(filters, rows) {
                        var filter_data = [];
                        if (rows.length > 0) {
                            for (var i = 0; i < rows.length; i++) {
                                filter_data.push(rows[i]._row.data);
                            }
                            model.listFilterData(filter_data);
                            download_data = filter_data;
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
                    cellEdited: function(cell) {
                        var change_data = [];
                        change_data['field'] = cell.getField();
                        change_data['value'] = cell.getValue();
                        change_data['user_id'] = cell.getData().user_id;
                        model.saveData(change_data).done(function(data) {
                            console.log('ok');
                            console.log(data);
                        }).fail(function(data) {
                            console.log(data);
                        })
                    },
                    cellClick: function(e, cell) {
                        if (!cell.getData().user_id || authority === 2 || authority === 5 || cell.getField() === 'area_id' || cell.getField() === 'mypage_self') {
                            return;
                        }
                        Cookies.set('userDetailUserId', parseInt(cell.getData().user_id));
                        window.location.href = 'admin_user_detail';
                    },
                    invalidOptionWarnings: false,
                    rowFormatter: function(row) {
                        if (row.getData().management_flag == '管理のみ') {
                            row.getElement().style.backgroundColor = "rgba(204, 204, 204, 0.5)";
                        }
                    }
                });
                table_list_users.hideColumn('resign_date');
                resolve();
                table_list_users.hideColumn('management_flag');
            });
        }),
        // view テーブルデータ表示
        renderTableData: function() {
            table_list_users.replaceData('../data/admin_users/table_data', {}, 'POST');
        },
        // view テーブルフィルター　従業員ステータス
        renderFilter: function(user_state_filter) {
            if (user_state_filter === '0') { // ALL
                table_list_users.showColumn('resign_date');
                table_list_users.clearFilter();
            }
            if (user_state_filter === '1') { // 既存
                table_list_users.hideColumn('resign_date');
                table_list_users.setFilter('state', '=', user_state_filter);
            }
            if (user_state_filter === '2') { // 退職
                table_list_users.showColumn('resign_date');
                table_list_users.setFilter('state', '=', user_state_filter);
            }
        },
        // view 集計データ表示
        renderTotal: function(total_data) {
            $('#user_all_num').text(total_data.all_count);
            $('#user_state_num').text(total_data.all_state_count);
            $('#user_resign_num').text(total_data.all_resign_count);
            if (total_data.all_state_count > 0 && total_data.all_count > 0) {
                $('#user_state_rate').text((total_data.all_state_count / total_data.all_count * 100).toFixed(1));
            }
            if (total_data.all_resign_count > 0 && total_data.all_count > 0) {
                $('#user_resign_rate').text((total_data.all_resign_count / total_data.all_count * 100).toFixed(1));
            }
        },
        // view 集計　フィルター　表示
        renderFilterTotal: function(total_filter_data) {
            $('#sex_man').text(total_filter_data.sex_man);
            $('#sex_woman').text(total_filter_data.sex_woman);
            $('#sex_no').text(total_filter_data.sex_no);
            if (total_filter_data.sex_man > 0 && total_filter_data.count_all > 0) {
                $('#sex_man_rate').text((total_filter_data.sex_man / total_filter_data.count_all * 100).toFixed(1));
            } else {
                $('#sex_no_rate').text('-');
            }
            if (total_filter_data.sex_woman > 0 && total_filter_data.count_all > 0) {
                $('#sex_woman_rate').text((total_filter_data.sex_woman / total_filter_data.count_all * 100).toFixed(1));
            } else {
                $('#sex_no_rate').text('-');
            }
            if (total_filter_data.sex_no > 0 && total_filter_data.count_all > 0) {
                $('#sex_no_rate').text((total_filter_data.sex_no / total_filter_data.count_all * 100).toFixed(1));
            } else {
                $('#sex_no_rate').text('-');
            }
        }
    }

    $(function() {
        Promise.all([view.renderTableColumns]).then(function() {
            view.renderTableData();
            table_list_users.setFilter('state', '=', 1); // フィルター
            // view.renderGraphData();
        });
        // フィルターセレクト操作
        $('select[name="user_state_filter"]').on('change', function() {
            var user_state_filter = $('select[name="user_state_filter"] option:selected').val();
            view.renderFilter(user_state_filter);
        });
        // authority
        if (authority === 2 || authority === 5) {
            $('#create_user').addClass('disabled');
        }
        // 新規従業員登録ボタン
        $(document).delegate('#create_user', 'click', function() {
            Cookies.set('userDetailUserId', 'new');
            window.location.href = 'admin_user_detail';
        });
        // ファイルダウンロードボタン
        $(document).delegate('#download_btn_excel', 'click', function() {
            model.downloadData('xlsx'); // download excel
        });
        $(document).delegate('#download_btn_pdf', 'click', function() {
            model.downloadData('pdf'); // download pdf
        });
    });

}());
