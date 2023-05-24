(function() {

  var mobile_menu_visible = 0; // モバイルサイドバー用
  var notice_count = 0; // 通知数

  if (Cookies.get('notice_id')) {
    var notice_id = Cookies.get('notice_id');
  } else {
    var notice_id;
  }
  var notice_flag;
  var notice_date;
  var notice_end_date;
  var from_userId;

  var noticeData = [];

  var model = {
    // model 通知情報取得
    get_notice_data: function() {
      var data = {
        system_id: sysId,
        user_id: userId
      };
      socket.emit('notice_client_to_server', data);
      socket.on('notice_server_to_client', function(notice_data) {
        noticeData = notice_data;
        view.render_notice(noticeData);
      });
    },
    // model 通知情報取得
    get_notice_id_data: function(notice_id) {
      var data = {
        system_id: sysId,
        notice_id: notice_id,
        user_id: userId
      };
      socket2.emit('notice_text_client_to_server', data);
      socket2.on('notice_text_server_to_client', function(notice_text) {
        view.render_message(notice_text);
      });
    },
    // model メッセージ送信
    submit_message: function(flag) {
      return $.ajax({
        type: 'POST',
        url: '../../data/notice/submit_message',
        data: {
          notice_id: notice_id,
          message: $('textarea[name="message_text"]').val(),
          user_id: userId,
          flag: flag, // 通知ステータス
          notice_flag: notice_flag, // 申請内容フラグ
          notice_date: notice_date,
          from_userId: from_userId, // 申請者ID
          notice_end_date: notice_end_date
        }
      })
    },
    // 既読用データ push
    push_notice_user: function(notice_id) {
      return $.ajax({
        type: 'POST',
        url: '../../data/notice/push_user',
        data: {
          notice_id: notice_id,
          user_id: userId
        }
      })
    }
  }

  var view = {
    // view 通知情報　表示
    render_notice: function(data) {
      var non_read_mark = 0;
      $('.notice-menu-area').html('');
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
          var user_data_w = notice_title1 + 'を申請<br><br>';
        } else {
          var user_data_w = 'ID:' + val.user_id + '<br>' + val.user_name + ' から ' + notice_title1 + '<br><br>';
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

        if (notice_id == val.notice_id) {
          var selected = ' notice-select';
        } else {
          var selected = '';
        }

        var notice_html = '<div id="' + val.notice_id + '" class="notice-menu alert ' + color + selected + '"><div class="title">' + icon + '<div class="notice-title">' + status_text + '<span class="' + color + ' notice-type' + type_color + '">' + notice_title1 + '</span></div></div><div class="sub-title"><i class="far fa-clock"></i> ' + val.notice_datetime.slice(0, -3) + '</div><div class="box-main">' + user_data_w + toDate_w + endDate_w + '  <i class="fas fa-angle-double-right"></i> ' + notice_title1 + ' ' + notice_time + '</div>' + non_read_w + '</div>';

        $('.notice-menu-area').append(notice_html);

      });
    },
    // view メッセージ表示
    render_message: function(data) {
      $('#message_area').html('');
      if (data.notice_status == 0) {
        if (data.to_user_id == userId) { // 自分の場合
          var status_text = '承認依頼中';
          var icon = '<i class="far fa-paper-plane"></i>';
          var color = 'alert-warning';
        } else { // 申請依頼
          var status_text = '申請があります';
          var icon = '<i class="fas fa-bell"></i>';
          var color = 'alert-info';
        }
      }
      if (data.notice_status == 1) {
        if (data.to_user_id == userId) { // 自分の場合
          var status_text = data.from_user_name + ' から承認されました';
          var icon = '<i class="fas fa-thumbs-up"></i>';
          var color = 'alert-success';
        } else { // 申請依頼
          var status_text = data.from_user_name + ' が承認しました';
          var icon = '<i class="fas fa-thumbs-up"></i>';
          var color = 'alert-success';
        }
      }
      if (data.notice_status == 2) {
        if (data.to_user_id == userId) { // 自分の場合
          var status_text = '申請NGです';
          var icon = '<i class="fas fa-exclamation-circle"></i>';
          var color = 'alert-danger';
        } else { // 申請依頼
          var status_text = 'NG送信中';
          var icon = '<i class="fas fa-exclamation-circle"></i>';
          var color = 'alert-danger';
        }
      }
      if (data.notice_flag == 1) {
        var notice_title1 = '修正依頼';
        var type_color = ' type-color01';
        notice_flag = 1;
        var notice_time = data.before_in_time + '〜' + data.before_out_time + ' を ' + data.notice_in_time + '〜' + data.notice_out_time + ' に時刻修正を申請';
      }
      if (data.notice_flag == 2) {
        var notice_title1 = '削除依頼';
        var type_color = ' type-color02';
        notice_flag = 2;
        var notice_time = data.before_in_time + '〜' + data.before_out_time + ' を 削除申請';
      }
      if (data.notice_flag == 3) {
        var notice_title1 = '遅刻依頼';
        var type_color = ' type-color03';
        notice_flag = 3;
        var notice_time = data.notice_in_time + ' 出勤に 遅刻申請';
      }
      if (data.notice_flag == 4) {
        var notice_title1 = '早退依頼';
        var type_color = ' type-color04';
        notice_flag = 4;
        var notice_time = data.notice_out_time + ' 退勤に 早退申請';
      }
      if (data.notice_flag == 5) {
        var notice_title1 = '残業依頼';
        var type_color = ' type-color05';
        notice_flag = 5;
        var notice_time = data.notice_out_time + ' まで 残業申請';
      }
      if (data.notice_flag == 6) {
        var notice_title1 = '有給依頼';
        var type_color = ' type-color06';
        notice_flag = 6;
        var notice_time = '';
      }
      if (data.notice_flag == 7) {
        var notice_title1 = '欠勤依頼';
        var type_color = ' type-color07';
        notice_flag = 7;
        var notice_time = '';
      }
      if (data.notice_flag == 8) {
        var notice_title1 = 'その他依頼';
        var type_color = ' type-color08';
        notice_flag = 8;
        var notice_time = '';
      }
      if (data.notice_flag == 11) {
        var notice_title1 = '休暇依頼';
        var type_color = ' type-color11';
        notice_flag = 11;
        var notice_time = '';
      }

      var nowDatetime = new Date(data.to_date);
      var year = nowDatetime.getFullYear();
      var month = nowDatetime.getMonth() + 1;
      var day = nowDatetime.getDate();
      var week = nowDatetime.getDay();
      var weekStr = ['日', '月', '火', '水', '木', '金', '土'][week];
      var toDate_w = year + '年' + ('0' + month).slice(-2) + '月' + ('0' + day).slice(-2) + '日' + '(' + weekStr + ')';
      notice_date = year + '-' + month + '-' + day;
      if (data.end_date) {
        var nowDatetime = new Date(data.end_date);
        var year = nowDatetime.getFullYear();
        var month = nowDatetime.getMonth() + 1;
        var day = nowDatetime.getDate();
        var week = nowDatetime.getDay();
        var weekStr = ['日', '月', '火', '水', '木', '金', '土'][week];
        var endDate_w = ' から ' + year + '年' + ('0' + month).slice(-2) + '月' + ('0' + day).slice(-2) + '日' + '(' + weekStr + ')';
        notice_end_date = year + '-' + month + '-' + day;
      } else {
        var endDate_w = '';
      }

      from_userId = data.to_user_id;

      // タイトル作成
      var permit = data.permit_high_user_auth.indexOf(String(userId)); // 承認権限
      if (permit < 0) {
        var auth_btn = '';
      } else {
        var auth_btn = '<div class="inner auth-btn"><div id="ok_submit" class="notice-header-btn">承認</div><div id="ng_submit" class="notice-header-btn">N G</div></div>';
      }
      if (data.to_user_id == userId) { // 申請者が自分の場合
        var name = '';
      } else {
        var name = '<b>' + data.user_name + '</b> より';
      }
      var title_html = '<div class="inner"><div class="notice-header-icon alert ' + color + '">' + icon + '</div><div class="notice-header-text-area"><div class="notice-header-title"><i class="fas fa-user"></i> ' + name + '<span class="title' + type_color + ' ' + color + '">' + notice_title1 + '</span><span class="state">' + status_text + '</span></div><div class="notice-header-subtitle"><span class="date-title">希望日</span><span class="date">' + toDate_w + endDate_w + '</span>' + notice_time + '</div></div></div>';
      // タイトル表示
      $('#notice_title').html(title_html + auth_btn);

      var nl2br = function(str) {
        return str.replace(/\n/g, '<br>');
      };

      $.each(data.massage, function(key, val) {
        if (val.user_id == userId) {
          var massage_name = '';
          var name_class = ' my';

        } else {
          var massage_name = val.user_name + ' より';
          var name_class = '';
        }
        var no_read_w = '';
        // 未読-既読は一旦やめる
        // if (val.read_users.indexOf(String(userId)) < 0) {
        //   var no_read_w = ' no-read';
        // } else {
        //   var no_read_w = '';
        // }
        var status = '';
        if (Number(val.notice_status) === 1) {
          var status = ' permit';
        }
        if (Number(val.notice_status) === 2) {
          var status = ' ng';
        }
        var message = nl2br(val.message_text);
        var message_html = '<div class="inner' + no_read_w + name_class + '"><div class="notice-icon"></div><div class="notice-message-box"><div class="notice-user-name">' + massage_name + '<span class="notice-date">' + val.text_datetime.slice(0, -3) + '</span></div><div class="notice-message' + status + '">' + message + '</div></div><div class="message-img"></div></div>';
        $('#message_area').append(message_html);
      });
      // model.push_notice_user(notice_id); // 既読データ保存
      // model.get_notice_data(); // 通知データ取得
      view.render_notice(noticeData);
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
    model.get_notice_data(); // 通知データ取得
    if (notice_id) {
      model.get_notice_id_data(notice_id);
    }
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
    // タブ操作
    $(document).on('click', '.nav-link', function() {
      $('.tab-pane, .nav-link').removeClass('active');
      $(this).addClass('active');
      $('#' + $(this).attr('data-tab')).addClass('active');
    });
    // サイドメニュー　通知選択
    $(document).on('click', '.notice-menu', function() {
      $('.notice-menu').removeClass('active');
      $(this).addClass('active');
      notice_id = $(this).attr('id');
      Cookies.set('notice_id', notice_id);
      model.get_notice_id_data(notice_id);
    });
    // 返信ボタン
    $(document).on('click', '#basic_submit', function() {
      if ($('textarea[name="message_text"]').val() === '') {
        return;
      }
      $('#basic_submit').addClass('disabled');
      model.submit_message(0).done(function(data) {
        // model.push_notice_user(notice_id).done(function(data) {
          model.get_notice_id_data(notice_id);
          $('#basic_submit').removeClass('disabled');
          $('textarea[name="message_text"]').val('');
        // });
      })
    });
    // 承認ボタン
    $(document).on('click', '#ok_submit', function() {
      $('#ok_submit').addClass('disabled');
      model.submit_message(1).done(function(data) {
        // model.push_notice_user(notice_id).done(function(data) {
          $('#ok_submit').removeClass('disabled');
          model.get_notice_id_data(notice_id);
          model.get_notice_data(); // 通知データ取得
          view.render_notice(noticeData);
        // });
      })
    });
    // NGボタン
    $(document).on('click', '#ng_submit', function() {
      $('#ng_submit').addClass('disabled');
      model.submit_message(2).done(function(data) {
        // model.push_notice_user(notice_id).done(function(data) {
          $('#ng_submit').removeClass('disabled');
          model.get_notice_id_data(notice_id);
          model.get_notice_data(); // 通知データ取得
          view.render_notice(noticeData);
        // });
      })
    });

  });

}());
