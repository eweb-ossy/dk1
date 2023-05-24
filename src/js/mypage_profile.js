(function() {
    var mobile_menu_visible = 0; // モバイルサイドバー用

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
        // model 従業員データ　取得
        getUserData: function() {
            return $.ajax({
                url: '../../data/admin_user_detail/user_data',
                dataType: 'json',
                type: 'POST',
                data: {
                    user_id: userId
                }
            })
        },
        // model 従業員データ　保存
        saveUserData: function() {
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '../../data/admin_user_detail/save_mypage',
                data: {
                    id: $('input[name="id"]').val(),
                    name_sei: $('input[name="name_sei"]').val(),
                    name_mei: $('input[name="name_mei"]').val(),
                    kana_sei: $('input[name="kana_sei"]').val(),
                    kana_mei: $('input[name="kana_mei"]').val(),
                    sex: $('input[name="sex"]:checked').val(),
                    birth_date: $('input[name="birth_date"]').val(),
                    zip_code: $('input[name="zip_code"]').val(),
                    address: $('input[name="address"]').val(),
                    phone_number1: $('input[name="phone_number1"]').val(),
                    phone_number2: $('input[name="phone_number2"]').val(),
                    email1: $('input[name="email1"]').val(),
                    email2: $('input[name="email2"]').val(),
                    user_password: $('input[name="user_password"]').val(),
                }
            })
        },
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
    }

    var view = {
    // view 従業員データ　フォーム
    renderUserDataForm: function(data) {
        var entry_date = data.entry_date === '0000-00-00' ? '' : data.entry_date;
        var resign_date = data.resign_date === '0000-00-00' ? '' : data.resign_date;
        var birth_date = data.birth_date === '0000-00-00' ? '' : data.birth_date;
        $('input[name="id"]').val(data.id);
        var kana_sei = data.kana_sei === null ? '' : data.kana_sei;
        var kana_mei = data.kana_mei === null ? '' : data.kana_mei;
        var zip_code = data.zip_code === null ? '' : data.zip_code;
        $('input[name="user_id"]').val(data.user_id);
        $('input[name="name_sei"]').val(data.name_sei);
        $('input[name="name_mei"]').val(data.name_mei);
        $('input[name="kana_sei"]').val(kana_sei);
        $('input[name="kana_mei"]').val(kana_mei);
        $('input[name="zip_code"]').val(zip_code);
        $('input[name="address"]').val(data.address);
        $('[name="sex"][value="' + data.sex + '"]').prop('checked', true);
        $('input[name="birth_date"]').val(birth_date);
        $('input[name="phone_number1"]').val(data.phone_number1);
        $('input[name="phone_number2"]').val(data.phone_number2);
        $('input[name="email1"]').val(data.email1);
        $('input[name="email2"]').val(data.email2);
        if (birth_date) {
            $('#old').val(BirthDay_to_age(birth_date) + '歳');
            datepicker.setDate(birth_date, 'Y-m-d');
        } else {
            $('#old').val('');
        }
    },
    }

    $(function() {
        model.get_notice_data(); // 通知データ取得
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
        // デイトピッカー設定
        datepicker = flatpickr('#birth_date', {
            dateFormat: 'Y-m-d',
            defaultDate: '1990-01-01',
            onChange: function(date, dateStr) {
                if (dateStr) {
                    $('#old').val(BirthDay_to_age(dateStr) + '歳');
                }
            }
        });
        // 従業員データ
        model.getUserData().done(function(data) {
            view.renderUserDataForm(data);
        });
        $('#profile_submit').on('click', function() {
            $(this).prop('disabled', true);
            model.saveUserData().done(function(data) {
                switch (data.message) {
                    case 'ok_edit':
                        siiimpleToast.message('修正登録完了', {
                            position: 'top|right'
                        });
                        model.getUserData().done(function(data) {
                            view.renderUserDataForm(data);
                        });
                        break;
                    case 'err_update':
                        siiimpleToast.alert('修正登録エラー', {
                            position: 'top|right'
                        });
                        break;
                    default:
                        siiimpleToast.alert('通信エラー', {
                            position: 'top|right'
                        });
                }
                $('#profile_submit').prop('disabled', false);
            })
        });
        // header menu クリック
        $(document).on('click', '.header-menu-btn', function() {
            $(this).next('.dropdown-menu').toggleClass('show');
        });
        // 
        $(document).click(function(event) {
            if(!$(event.target).closest('.dropdown-menu').length && !$(event.target).closest('.header-menu-btn').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });
        // 通知クリック
        $(document).on('click', '.dropdown-item', function() {
            Cookies.set('notice_id', $(this).attr('id'));
            location.href = '/mypage_notice';
        });

    });
}());