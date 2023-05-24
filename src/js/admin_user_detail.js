(function() {

    var table_user_detail_notice;
    var notice_data;
    var permit_data;
    var pickr = [];
    var group_history_num = 0;
    var list__data;

    var user_id = 'new';
    var flag;
    if (Cookies.get('userDetailUserId')) {
        user_id = Cookies.get('userDetailUserId');
    }
    if (Cookies.get('userDetailTabKey') && user_id !== 'new') {
        var key = Cookies.get('userDetailTabKey');
    } else {
        var key = '01';
    }

    // 従業員ID 連番処理 22.10.28追加
    function getNextUserId() {
        return $.ajax({
            url: '../../data/admin_user_detail/getNextUserId',
            dataType: 'json',
        })
    }

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

    function BirthDay_to_age(birth_date) {
        var birthDate = new Date(birth_date);
        var y2 = birthDate.getFullYear().toString().padStart(4, '0');
        var m2 = (birthDate.getMonth() + 1).toString().padStart(2, '0');
        var d2 = birthDate.getDate().toString().padStart(2, '0');
        var today = new Date();
        var y1 = today.getFullYear().toString().padStart(4, '0');
        var m1 = (today.getMonth() + 1).toString().padStart(2, '0');
        var d1 = today.getDate().toString().padStart(2, '0');
        return Math.floor((Number(y1 + m1 + d1) - Number(y2 + m2 + d2)) / 10000);
    }

    var model = {
        // model edit 従業員データ　取得
        getUserData: function() {
            return $.ajax({
                url: '../../data/admin_user_detail/user_data',
                dataType: 'json',
                type: 'POST',
                data: {
                    user_id: user_id
                }
            })
        },
        // model new グループデータ　取得
        getGroupData: function() {
            return $.ajax({
                url: '../../data/admin_user_detail/group_data',
                dataType: 'json',
                type: 'POST'
            })
        },
        // model page 01 基本設定 保存
        savePage01: function() {
            var user_id = $('input[name="user_id"]').val();
            user_id = user_id.trim();
            var date = new Date(datepicker1.selectedDates);
            var entry_year = date.getFullYear();
            var entry_month = date.getMonth() + 1;
            var entry_day = date.getDate();
            if (isNaN(entry_year)) {
                var entry_date = '';
            } else {
                var entry_date = entry_year + '-' + entry_month + '-' + entry_day;
            }
            var date = new Date(datepicker2.selectedDates);
            var resign_year = date.getFullYear();
            var resign_month = date.getMonth() + 1;
            var resign_day = date.getDate();
            if (isNaN(resign_year)) {
                var resign_date = '';
            } else {
                var resign_date = resign_year + '-' + resign_month + '-' + resign_day;
            }
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_user_detail/save_page_01',
                data: {
                    flag: flag,
                    id: $('input[name="id"]').val(),
                    user_id: user_id,
                    name_sei: $('input[name="name_sei"]').val(),
                    name_mei: $('input[name="name_mei"]').val(),
                    kana_sei: $('input[name="kana_sei"]').val(),
                    kana_mei: $('input[name="kana_mei"]').val(),
                    state: $('input[name="state"]:checked').val(),
                    sex: $('select[name="sex"] option:selected').val(),
                    entry_date: entry_date,
                    resign_date: resign_date,
                    user_password: $('input[name="user_password"]').val(),
                    shift_alert_flag: $('input[name="shift_alert_flag"]:checked').val(),
                    management_flag: $('input[name="management_flag"]:checked').val(),
                    input_confirm_flag: $('input[name="input_confirm_flag"]:checked').val()
                }
            })
        },
        // model page 02 グループ 保存
        savePage02: function() {
            var group_history_id = [];
            var group_history_to_date = [];
            var group_history_groups = [];
            for (var i = 0; i < group_history_num; i++) {
                group_history_id[i] = $('input[name="group_history_id_' + i + '"]').val();
                group_history_to_date[i] = formatDate(new Date(pickr[i].selectedDates[0]), 'YYYY-MM-DD');
                group_history_groups[i] = [$('[name="group1_' + i + '"]').val(), $('[name="group2_' + i + '"]').val(), $('[name="group3_' + i + '"]').val()];
            }
            return $.ajax({
                type: 'POST',
                dateType: 'json',
                url: '../../data/admin_user_detail/save_page_02',
                data: {
                    id: $('input[name="id"]').val(),
                    user_id: user_id,
                    group_history_id: group_history_id,
                    group_history_to_date: group_history_to_date,
                    group_history_groups: group_history_groups
                }
            })
        },
        delPage02: function(group_history_id) {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_user_detail/del_page_02',
                data: {
                    id: group_history_id
                }
            })
        },
        // model page 03 詳細設定 保存
        savePage03: function() {
            var date = new Date(datepicker3.selectedDates);
            var brith_year = date.getFullYear();
            var brith_month = date.getMonth() + 1;
            var brith_day = date.getDate();
            if (isNaN(brith_year)) {
                var brith_date = '';
            } else {
                var brith_date = brith_year + '-' + brith_month + '-' + brith_day;
            }
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_user_detail/save_page_03',
                data: {
                    id: $('input[name="id"]').val(),
                    birth_date: brith_date,
                    phone_number1: $('input[name="phone_number1"]').val(),
                    phone_number2: $('input[name="phone_number2"]').val(),
                    email1: $('input[name="email1"]').val(),
                    email2: $('input[name="email2"]').val(),
                    zip_code: $('input[name="zip_code"]').val(),
                    address: $('input[name="address"]').val()
                }
            })
        },
        // model page 04 テーブルカラム
        getTableColumns: function() {
            return $.ajax({
                url: '../../data/columns/user_detail_notice',
                dataType: 'json',
                type: 'POST'
            })
        },
        // model
        listData: function(list_data) {
            list__data = list_data;
            var data = list_data.filter(function(element) {
                return element.notice === true;
            });
            notice_data = [];
            $.each(data, function(key, val) {
                notice_data.push(val['user_id']);
            });
            var permit = list_data.filter(function(element) {
                return element.permit === true;
            });
            permit_data = [];
            $.each(permit, function(key, val) {
                permit_data.push(val['user_id']);
            });
            var notice_count = notice_data.length;
            if (notice_count > 0) {
                $('#notice_batch').text(notice_count);
                $('#notice_batch').addClass('on');
            } else {
                $('#notice_batch').removeClass('on');
            }
        },
        // model page 04 保存
        savePage04: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_user_detail/save_page_04',
                data: {
                    user_id: user_id,
                    notice_data: notice_data,
                    permit_data: permit_data
                }
            })
        },
        // model page 05 保存
        savePage05: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_user_detail/save_page_05',
                data: {
                    id: $('input[name="id"]').val(),
                    aporan_flag: $('input[name="aporan_flag"]:checked').val(),
                    advance_pay_flag: $('input[name="advance_pay_flag"]:checked').val(),
                    esna_pay_flag: $('input[name="esna_pay_flag"]:checked').val(),
                    api_output: $('input[name="api_output"]:checked').val()
                }
            })
        },
        // model page 99 削除
        savePage99: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_user_detail/save_page_99',
                data: {
                    user_id: user_id
                }
            })
        },
    }

    var view = {
        // view タブ表示
        renderTab: function(key) {
            $('.tab-page').removeClass('active-page');
            $('#page_' + key).addClass('active-page');
            $('.tab').removeClass('active-menu');
            $('#tab_' + key).addClass('active-menu');
        },
        // view 従業員データ　名前
        renderUserData: function() {
            let kana__sei = $('input[name="kana_sei"]').val();
            let kana__mei = $('input[name="kana_mei"]').val();
            let name__sei = $('input[name="name_sei"]').val();
            let name__mei = $('input[name="name_mei"]').val();
            let user__id = $('input[name="user_id"]').val();
            if (kana__sei || kana__mei) {
                $('#user_kana').text(kana__sei + ' ' + kana__mei);
            } else {
                $('#user_kana').text('');
            }
            if (name__sei || name__mei) {
                $('#user_name').text(name__sei + ' ' + name__mei);
            } else {
                if (user_id === 'new') {
                    $('#user_name').text("新規従業員を登録します");
                } else {
                    $('#user_name').text('');
                }
            }
            if (user__id) {
                $('#user_id').text(user__id);
            } else {
                $('#user_id').text('');
            }
        },
        // view 従業員データ　フォーム
        renderUserDataForm: function(data) {
            var entry_date = data.entry_date === '0000-00-00' ? '' : data.entry_date;
            var resign_date = data.resign_date === '0000-00-00' ? '' : data.resign_date;
            var birth_date = data.birth_date === '0000-00-00' ? '' : data.birth_date;
            $('input[name="id"]').val(data.id);
            var kana_sei = data.kana_sei === null ? '' : data.kana_sei;
            var kana_mei = data.kana_mei === null ? '' : data.kana_mei;
            $('input[name="user_id"]').val(data.user_id);
            $('input[name="name_sei"]').val(data.name_sei);
            $('input[name="name_mei"]').val(data.name_mei);
            $('input[name="kana_sei"]').val(kana_sei);
            $('input[name="kana_mei"]').val(kana_mei);
            $('[name="sex"] option[value="' + data.sex + '"]').prop('selected', true);
            $('input[name="entry_date"]').val(entry_date);
            $('[name="state"][value="' + data.state + '"]').prop('checked', true);
            $('input[name="birth_date"]').val(birth_date);
            $('input[name="phone_number1"]').val(data.phone_number1);
            $('input[name="phone_number2"]').val(data.phone_number2);
            $('input[name="email1"]').val(data.email1);
            $('input[name="email2"]').val(data.email2);
            $('input[name="zip_code"]').val(data.zip_code);
            $('input[name="address"]').val(data.address);
            $('[name="aporan_flag"][value="' + data.aporan_flag + '"]').prop('checked', true);
            $('[name="advance_pay_flag"][value="' + data.advance_pay_flag + '"]').prop('checked', true);
            $('[name="management_flag"][value="0"]').prop('checked', true);
            $('[name="management_flag"][value="' + data.management_flag + '"]').prop('checked', true);
            $('[name="esna_pay_flag"][value="' + data.esna_pay_flag + '"]').prop('checked', true);
            $('[name="api_output"][value="' + data.api_output + '"]').prop('checked', true);
            if (data.state === '2') {
                $('#resign_date_area').show();
                $('input[name="resign_date"]').val(resign_date);
            } else {
                $('#resign_date_area').hide();
            }
            datepicker1.setDate(entry_date, 'Y-m-d');
            datepicker2.setDate(resign_date, 'Y-m-d');
            datepicker3.setDate(birth_date, 'Y-m-d');
            if (birth_date) {
                $('#old').text(BirthDay_to_age(birth_date) + '歳');
            } else {
                $('#old').text('');
            }
            if (data.shift_alert_flag) {
                if (data.shift_alert_flag == 0) {
                    $('[name="shift_alert_flag"][value="0"]').prop('checked', true);
                } else {
                    $('[name="shift_alert_flag"][value="2"]').prop('checked', true);
                }
            } else {
                $('[name="shift_alert_flag"][value="0"]').prop('checked', true);
            }
            if (data.input_confirm_flag) {
                if (data.input_confirm_flag == 0) {
                    $('[name="input_confirm_flag"][value="0"]').prop('checked', true);
                } else {
                    $('[name="input_confirm_flag"][value="2"]').prop('checked', true);
                }
            } else {
                $('[name="input_confirm_flag"][value="0"]').prop('checked', true);
            }

            // ここからグループ設定　表示
            var group1_option = group2_option = group3_option = '<option value="">---</option>';
            var group1 = [];
            var group2 = [];
            var group3 = [];
            $.each(data.group1, function(index, value) {
                group1_option += '<option value="' + value.id + '">' + value.group_name + '</option>';
                group1[value.id] = value.group_name;
            });
            $.each(data.group2, function(index, value) {
                group2_option += '<option value="' + value.id + '">' + value.group_name + '</option>';
                group2[value.id] = value.group_name;
            });
            $.each(data.group3, function(index, value) {
                group3_option += '<option value="' + value.id + '">' + value.group_name + '</option>';
                group3[value.id] = value.group_name;
            });
            var group_html = '';
            var group_select = {};
            var group_date_tmp = [];
            var group_name = [];
            var pickr_max = [];
            var pickr_min = [];
            group_name[1] = '---';
            group_name[2] = '---';
            group_name[3] = '---';
            $.each(data.group_history, function(index, value) {
                var group_end = '';
                group_date_tmp[index] = value.to_date;
                var row_color = '#fff';
                if (index === 0) {
                    group_end = '現在';
                    if (data.state === '2') {
                        group_end = data.resign_date + '退職';
                    }
                    if (value.group1_id > 0) {
                        group_name[1] = group1[value.group1_id];
                    }
                    if (value.group2_id > 0) {
                        group_name[2] = group2[value.group2_id];
                    }
                    if (value.group3_id > 0) {
                        group_name[3] = group3[value.group3_id];
                    }
                    row_color = '#e7fffd';
                    pickr_max[index] = formatDate(new Date(), 'YYYY-MM-DD');
                } else {
                    var arr = group_date_tmp[index - 1].split('-');
                    var date = new Date(arr[0], arr[1] - 1, arr[2]);
                    date.setDate(date.getDate() - 1); // 前日
                    group_end = formatDate(new Date(date), 'YYYY年MM月DD日');
                    pickr_max[index] = formatDate(new Date(date), 'YYYY-MM-DD');
                }

                group_select[index] = {
                    'group1': value.group1_id,
                    'group2': value.group2_id,
                    'group3': value.group3_id
                };

                group_html += '<input type="hidden" name="group_history_id_' + index + '" value="' + value.id + '">';
                group_html += '<tr style="font-size:9px;background:' + row_color + '">';
                group_html += '<td><i class="fas fa-calendar-alt"></i>&nbsp;<input class="field_page_02 group-pickr pickr_' + index + '" type="text" value="' + value.to_date + '" data-index="' + index + '"></td>';
                group_html += '<td id="group_end_' + index + '">' + group_end + '</td>';
                group_html += '<td><select name="group1_' + index + '" class="field_page_02">' + group1_option + '</select></td>';
                group_html += '<td><select name="group2_' + index + '" class="field_page_02">' + group2_option + '</select></td>';
                group_html += '<td><select name="group3_' + index + '" class="field_page_02">' + group3_option + '</select></td>';
                if (index + 1 === data.group_history.length) {
                    group_html += '<td class="group-btn-area"></td>';
                } else {
                    group_html += '<td class="group-btn-area"><div class="btn red group-history-del" data-group-history-id="' + value.id + '">削除</div></td>';
                }
                group_html += '</tr>';
                group_history_num++;
            });
            $('#group_body').html(group_html);
            $.each(group_select, function(index, value) {
                $('[name="group1_' + index + '"]').val(value.group1);
                $('[name="group2_' + index + '"]').val(value.group2);
                $('[name="group3_' + index + '"]').val(value.group3);
                if (group_date_tmp[Number(index) + 1]) {
                    var date = new Date(group_date_tmp[Number(index) + 1]);
                    date.setDate(date.getDate() + 1);
                    pickr_min[index] = formatDate(new Date(date), 'YYYY-MM-DD');
                } else {
                    pickr_min[index] = '1900-01-01';
                }
                pickr[index] = flatpickr('.pickr_' + index, {
                    dateFormat: 'Y年m月d日',
                    onChange: function(selectedDates, dateStr, instance) {
                        var index = $(instance.element).attr('data-index');
                        var next_id = Number(index) + 1;
                        var date = new Date(selectedDates);
                        date.setDate(date.getDate() - 1); // 前日
                        $('#group_end_' + next_id).text(formatDate(new Date(date), 'YYYY年MM月DD日'));
                        if (pickr[next_id]) {
                            pickr[next_id].set('maxDate', formatDate(new Date(date), 'YYYY-MM-DD'));
                        }
                    },
                    maxDate: pickr_max[index],
                    minDate: pickr_min[index]
                });
            });
            // for (var i = 1; i <= 3; i++) {
            //     if (data['group_title'][i - 1]['title']) {
            //         $('#group' + i + '_name').html("　" + data['group_title'][i - 1]['title'] + '<span></span>：<span class="user-group">' + group_name[i] + '</span>');
            //         $('#group_title' + i).text(data['group_title'][i - 1]['title']);
            //     }
            // }
        },
        // view 新規グループ追加
        addGroup: function(data) {
            var group_start = formatDate(new Date(), 'YYYY-MM-DD');
            if (formatDate(new Date(pickr[0].selectedDates), 'YYYY-MM-DD') === group_start) {
                return;
            }
            var group1_option = group2_option = group3_option = '<option value="">---</option>';
            var group1 = [];
            var group2 = [];
            var group3 = [];
            $.each(data.group1, function(index, value) {
                group1_option += '<option value="' + value.id + '">' + value.group_name + '</option>';
                group1[value.id] = value.group_name;
            });
            $.each(data.group2, function(index, value) {
                group2_option += '<option value="' + value.id + '">' + value.group_name + '</option>';
                group2[value.id] = value.group_name;
            });
            $.each(data.group3, function(index, value) {
                group3_option += '<option value="' + value.id + '">' + value.group_name + '</option>';
                group3[value.id] = value.group_name;
            });
            $('#group_add_btn').addClass('disabled');
            var new_group_html = '';
            new_group_html += '<tr style="font-size:9px;background:#fffae7" class="group_history_new">';
            new_group_html += '<input type="hidden" name="group_history_id_' + group_history_num + '" value="new" class="group_history_new">';
            new_group_html += '<td><i class="fas fa-calendar-alt"></i>&nbsp;<input class="field_page_02 group-pickr pickr_' + group_history_num + '" type="text" value="' + group_start + '"></td>';
            new_group_html += '<td id="group_end_' + group_history_num + '">現在</td>';
            new_group_html += '<td><select name="group1_' + group_history_num + '" class="field_page_02">' + group1_option + '</select></td>';
            new_group_html += '<td><select name="group2_' + group_history_num + '" class="field_page_02">' + group2_option + '</select></td>';
            new_group_html += '<td><select name="group3_' + group_history_num + '" class="field_page_02">' + group3_option + '</select></td>';
            new_group_html += '<td class="group-btn-area"><div class="btn red group-history-del" data-group-history-id="new">削除</div></td></tr>';

            $('#group_body').prepend(new_group_html);

            pickr[group_history_num] = flatpickr('.pickr_' + group_history_num, {
                dateFormat: 'Y年m月d日',
                onChange: function(selectedDates, dateStr, instance) {
                    var date = new Date(selectedDates);
                    date.setDate(date.getDate() - 1); // 前日
                    $('#group_end_0').text(formatDate(new Date(date), 'YYYY年MM月DD日'));
                    pickr[0].set('maxDate', formatDate(new Date(date), 'YYYY-MM-DD'));
                }
            });
            var date = new Date();
            date.setDate(date.getDate() - 1); // 前日
            $('#group_end_0').text(formatDate(new Date(date), 'YYYY年MM月DD日'));
            var next_date = new Date(data.group_history[0].to_date);
            next_date.setDate(next_date.getDate() + 1);
            pickr[group_history_num].set('minDate', formatDate(new Date(next_date), 'YYYY-MM-DD'));
            group_history_num++;
        },
        // view テーブル
        renderTableColumns: new Promise(function(resolve, reject) {
            model.getTableColumns().done(function(data) {
                table_user_detail_notice = new Tabulator('#user_notice_table', {
                    layout: 'fitColumns',
                    height: 'calc(100vh - 405px)',
                    columnMinWidth: 20,
                    columns: data,
                    dataFiltered: function(filters, rows) {
                        var list_data = [];
                        if (rows.length > 0) {
                            for (var i = 0; i < rows.length; i++) {
                                list_data.push(rows[i]._row.data);
                            }
                            model.listData(list_data);
                        }
                    },
                    dataEdited: function(data) {
                    model.listData(data);
                    },
                    invalidOptionWarnings: false,
                });
                resolve();
            });
        }),
        // view テーブルデータ
        renderTableData: function() {
            table_user_detail_notice.replaceData('../../data/admin_user_detail/table_notice_data', {
                user_id: user_id
            }, 'POST');
        },
    }


    $(function() {
        // タブ表示
        view.renderTab(key);
        // タブ操作
        $('.tab').on('click', function() {
            key = $(this).attr('id').substr(-2);
            view.renderTab(key);
            Cookies.set('userDetailTabKey', key);
            if (key === '04') {
                view.renderTableData();
            }
        });
        // デイトピッカー設定
        datepicker1 = flatpickr('#entry_date', {
            dateFormat: 'Y-m-d'
        });
        datepicker2 = flatpickr('#resign_date', {
            dateFormat: 'Y-m-d'
        });
        datepicker3 = flatpickr('#birth_date', {
            dateFormat: 'Y-m-d',
            defaultDate: '1990-01-01',
            onChange: function(date, dateStr) {
                if (dateStr) {
                    $('#old').text(BirthDay_to_age(dateStr) + '歳');
                }
            }
        });
        // モーダル設定
        $('#modal_group').iziModal({
            headerColor: '#1591a2',
            focusInput: false,
        });
        //
        if (user_id !== 'new') {
            $('.user-del-btn').show();
        }
        // // tooltip
        // tippy('.tips', {
        //   arrow: true,
        //   size: 'small'
        // });
        //
        if (mypage_flag === 0) {
            $('#user_password_area').hide();
            $('#tab_04').hide();
        }
        // 通知テーブル表示
        Promise.all([view.renderTableColumns]).then(function() {
            view.renderTableData();
        });
        // 従業員データ取得
        if (user_id !== 'new') {
            flag = 'edit';
            $('input[name="user_id"]').addClass('disabled');
            $('input[name="user_id"]').prop('disabled', true);
            $('#mark').addClass('edit');
            $('#mark').text('編集');
            $('#password_label_text').text('パスワード （変更する場合のみ記入）');
            model.getUserData().done(function(data) {
                view.renderUserDataForm(data);
                view.renderUserData(); // 上部 従業員名等表示
            });
        } else { // 新規
            flag = 'new';
            $('input[name="user_id"]').removeClass('disabled');
            $('input[name="user_id"]').prop('disabled', false);
            $('#resign_date_area, #resign_btn, #tab_02, #tab_03, #tab_04, #tab_05').hide();
            $('#mark').removeClass('edit');
            $('#mark').text('新規');
            $('#password_label_text').text('パスワード （入力をしない場合は従業員IDとなります）');
            model.getGroupData().done(function(data) {
                view.renderUserDataForm(data);
                view.renderUserData();
                if (user_id_define === 1) {
                    getNextUserId().done(function(data) {
                        const NextUserId = Number(data.maxUserID) + 1;
                        $('#input__user_id').val(NextUserId).css('color', '#1591a2');
                    })
                }
            });
        }

        // page 01 基本設定　操作
        function user_id_check(user__id) { // function 従業員IDの重複チェック
            return $.ajax({
                url: '../data/admin_user_detail/user_id_check',
                type: 'POST',
                data: { user_id: user__id }
            }).done(function(data) {
                error__message = data ? "この従業員IDは既に使用されてます" : (isNaN(user__id)) ? "従業員IDは数字で入力して下さい" : null;
                error(error__message, NAME_user_id);
            })
        }
        function error(message, view) { // function エラー表示
            $('.error-message').remove();
            if (message) {
                view.after(`<span class="error-message">${message}<span>`);
                $('#submit_page_01').addClass('disabled');
            }
        }
        const NAME_user_id = $('input[name="user_id"]');
        NAME_user_id.on('input change', function() { // 従業員IDの重複 入力エラーチェック
            let user__id = $(this).val();
            user_id_check(user__id);
        });
        const NAME_name_sei =  $('input[name="name_sei"]');
        const NAME_name_mei =  $('input[name="name_mei"]');
        $('.field_page_01').on('input change', function() { // page 01 のフィールド操作時
            if (NAME_user_id.val() && NAME_name_sei.val() && NAME_name_mei.val()) {
                $('#submit_page_01').removeClass('disabled');
                if (flag === 'new') {
                    user_id_check(NAME_user_id.val());
                }
            }
            view.renderUserData(); // 上部 従業員名等表示
        });

        $('[name="state"]').on('click', function() { // 状況操作
            if (user_id !== 'new') {
                $('#resign_date_area').toggle();
            }
        });
        $('#submit_page_01').on('click', function() { // 基本設定 保存
            $('#submit_page_01').addClass('disabled');
            model.savePage01().done(function(data) {
                switch (data.message) {
                    case 'ok_edit':
                        siiimpleToast.message('修正登録完了', {
                            position: 'top|right'
                        });
                        break;
                    case 'ok_create':
                        siiimpleToast.message('新規登録完了', {
                            position: 'top|right'
                        });
                        Cookies.set('userDetailUserId', parseInt($('#user_id').text()));
                        Cookies.set('userDetailTabKey', '02');
                        break;
                    case 'err_update':
                        siiimpleToast.alert('修正登録エラー', {
                            position: 'top|right'
                        });
                        break;
                    case 'err_insert':
                        siiimpleToast.alert('登録エラー', {
                            position: 'top|right'
                        });
                        break;
                    case 'err_ip':
                        siiimpleToast.alert('従業員ID重複エラー', {
                            position: 'top|right'
                        });
                        // return;
                        break;
                    default:
                        siiimpleToast.alert('通信エラー', {
                            position: 'top|right'
                    });
                }
                setTimeout("location.reload()", 3000);
            });
        });
        
        // page 02 グループ設定　操作
        $(document).delegate('.field_page_02', 'change', function() {
            $('#submit_page_02').removeClass('disabled');
        });
        $(document).delegate('#group_add_btn', 'click', function() {
            model.getUserData().done(function(data) {
                view.addGroup(data);
            });
        });
        $(document).delegate('#submit_page_02', 'click', function() {
            model.savePage02().done(function(data) {
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
        $(document).delegate('.group-history-del', 'click', function() {
            if ($(this).attr('data-group-history-id') === 'new') {
                group_history_num--;
                $('.group_history_new').remove();
                $('#group_add_btn').removeClass('disabled');
                $('#group_end_0').text('現在');
                return;
            }
            if (confirm('グループ履歴を削除します')) {
                model.delPage02($(this).attr('data-group-history-id')).done(function(data) {
                    if (data.message == 'ok_del') {
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
        });
        // page 03 詳細設定　操作
        $(document).delegate('#submit_page_03', 'click', function() { // 詳細設定　保存
            model.savePage03().done(function(data) {
                switch (data.message) {
                    case 'ok_edit':
                        siiimpleToast.message('修正登録完了', {
                            position: 'top|right'
                        });
                        break;
                    case 'err_update':
                        siiimpleToast.message('修正登録エラー', {
                            position: 'top|right'
                        });
                        break;
                    default:
                        siiimpleToast.alert('通信エラー', {
                            position: 'top|right'
                        });
                }
                setTimeout("location.reload()", 3000);
            });
        });
        // page 04 承認権限 保存
        $(document).delegate('#submit_page_04', 'click', function() {
            if (user_id === 'new') {
                return;
            }
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
            });
        });
        $(document).delegate('#all_notice', 'click', function() {
            var replace_data = [];
            $.each(list__data, function(index, val) {
                obj = {
                    'id': val.id,
                    'user_id': val.user_id,
                    'user_name': val.user_name,
                    'group1_name': val.group1_name,
                    'group2_name': val.group2_name,
                    'group3_name': val.group3_name,
                    'notice': true,
                    'permit': val.permit
                };
                replace_data.push(obj);
            });
            table_user_detail_notice.replaceData(replace_data);
        });
        $(document).delegate('#all_none_notice', 'click', function() {
            var replace_data = [];
            $.each(list__data, function(index, val) {
                obj = {
                    'id': val.id,
                    'user_id': val.user_id,
                    'user_name': val.user_name,
                    'group1_name': val.group1_name,
                    'group2_name': val.group2_name,
                    'group3_name': val.group3_name,
                    'notice': false,
                    'permit': val.permit
                };
                replace_data.push(obj);
            });
            table_user_detail_notice.replaceData(replace_data);
        });
        $(document).delegate('#all_permit', 'click', function() {
            var replace_data = [];
            $.each(list__data, function(index, val) {
                obj = {
                    'id': val.id,
                    'user_id': val.user_id,
                    'user_name': val.user_name,
                    'group1_name': val.group1_name,
                    'group2_name': val.group2_name,
                    'group3_name': val.group3_name,
                    'notice': val.notice,
                    'permit': true
                };
                replace_data.push(obj);
            });
            table_user_detail_notice.replaceData(replace_data);
        });
        $(document).delegate('#all_none_permit', 'click', function() {
            var replace_data = [];
            $.each(list__data, function(index, val) {
                obj = {
                    'id': val.id,
                    'user_id': val.user_id,
                    'user_name': val.user_name,
                    'group1_name': val.group1_name,
                    'group2_name': val.group2_name,
                    'group3_name': val.group3_name,
                    'notice': val.notice,
                    'permit': false
                };
                replace_data.push(obj);
            });
            table_user_detail_notice.replaceData(replace_data);
        });
        // page 05
        $(document).delegate('#submit_page_05', 'click', function() {
            model.savePage05().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('登録完了', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('登録エラー', {
                        position: 'top|right'
                    });
                }
                // setTimeout("location.reload()", 3000);
            });
        });

        // page 99 削除
        $(document).delegate('#submit_page_99', 'click', function() {
            $('#submit_page_99').addClass('disabled');
            model.savePage99().done(function(data) {
                if (data.message === 'ok') {
                    siiimpleToast.message('削除しました', {
                        position: 'top|right'
                    });
                } else {
                    siiimpleToast.alert('削除エラー', {
                        position: 'top|right'
                    });
                }
                Cookies.set('userDetailUserId', 'new');
                Cookies.set('userDetailTabKey', '01');
                setTimeout("location.replace('/admin_users')", 3000);
            });
        });
    });
}());
