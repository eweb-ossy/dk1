(function() {
  var mobile_menu_visible = 0; // モバイルサイドバー用
  var notice_flag;
  var calendar1;
  var to_date = '';
  var end_date = '';

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
    get_mail_time: function(date) {
      to_date = date;
      // mail_date_flag = 1;
      return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../../data/gateway/mail_time',
        data: {
          user_id: userId,
          date: date
        }
      })
    },
    // model 申請送信
    mail_submit: function(notice_data) {
      return $.ajax({
        type: 'POST',
        url: '../../data/gateway/notice',
        data: {
          to_user_id: userId,
          to_date: to_date,
          notice_flag: notice_flag,
          notice_in_time: notice_data.notice_in_time,
          notice_out_time: notice_data.notice_out_time,
          notice_text: $('#textarea1').val(),
          user_name: user_name,
          before_in_time: $('input[name="before_in_time"]').val(),
          before_out_time: $('input[name="before_out_time"]').val(),
          end_date: end_date
        }
      })
    },
  }

  var view = {
    // view 申請タイトル表示　日付ピッカー設定　表示
    render_notice_title: function(notice_flag) {
      view.reset_view();
      switch (notice_flag) {
        case 1:
          var notice_title = '時刻修正申請<span class="sub-text">出退勤時刻の修正依頼をします</span>';
          calendar1.set('maxDate', new Date());
          $('#step03_title').text('申請する修正時刻を入力');
          break;
        case 2:
          var notice_title = '時刻削除申請';
          calendar1.set('maxDate', new Date());
          break;
        case 3:
          var notice_title = '遅刻申請';
          calendar1.set('minDate', new Date());
          $('#step03_title').text('申請する遅刻時刻を入力');
          break;
        case 4:
          var notice_title = '早退申請';
          calendar1.set('minDate', new Date());
          $('#step03_title').text('申請する早退時刻を入力');
          break;
        case 5:
          var notice_title = '残業申請';
          calendar1.set('minDate', new Date());
          $('#step03_title').text('申請する残業時刻を入力');
          break;
        case 6:
          var notice_title = '有給申請';
          calendar1.set('minDate', new Date());
          calendar1.set('mode', 'range')
          break;
        case 7:
          var notice_title = '欠勤申請';
          calendar1.set('minDate', new Date());
          break;
        case 8:
          var notice_title = 'その他申請';
          break;
        case 9:
          var notice_title = '有給申請（半日）';
          calendar1.set('minDate', new Date());
          break;
        case 11:
          var notice_title = '休暇申請';
          calendar1.set('minDate', new Date());
          break;
      }
      $('#notice_notice_flag').html(notice_title);
      $('#step02_area').show();
    },
    // view カレンダー日付選択後の表示処理
    render_now_time: function(data) {
      var before_in_time = data.in_work_time ? data.in_work_time.substr(0, 5) : '未出勤';
      $('.now-in-time').text(before_in_time);
      $('input[name="before_in_time"]').val(before_in_time);
      var before_out_time = data.out_work_time ? data.out_work_time.substr(0, 5) : '未退勤';
      $('.now-out-time').text(before_out_time);
      $('input[name="before_out_time"]').val(before_out_time);
      if (notice_flag === 1) {
        $('#now_in_time').val(before_in_time);
        $('#now_out_time').val(before_out_time);
        $('#step03_area').show();
        $('#step04_area').show();
        $('#step02_now_time').show();
        $('#step02_now_time').css('display', 'flex');
      }
      if (notice_flag === 2) {
        $('.del-in-time').text(before_in_time);
        $('.del-out-time').text(before_out_time);
        if (data.in_work_time || data.out_work_time) {
          $('#step03_2_title').html('<b style="font-size:20px;color:#f00">下記時刻を削除申請</b>');
          $('#step04_area').show();
        } else {
          $('#step03_2_title').html('<i class="fas fa-exclamation-triangle"></i> 削除する時刻がありません');
        }
        $('#step03_2_area').show();
        $('#step02_now_time').show();
        $('#step02_now_time').css('display', 'flex');
      }
      if (notice_flag === 3) {
        $('#input_in_time_area, #input_out_time_area').show();
        $('#input_out_time_area').hide();
        $('#step03_area').show();
        $('#step04_area').show();
      }
      if (notice_flag === 4 || notice_flag === 5) {
        $('#input_in_time_area, #input_out_time_area').show();
        $('#input_in_time_area').hide();
        $('#step03_area').show();
        $('#step04_area').show();
      }
      if (notice_flag === 6 || notice_flag === 7 || notice_flag === 8 || notice_flag === 11 || notice_flag === 9) {
        $('#step04_area').show();
      }
      if (to_date.length > 10) {
        end_date = to_date.slice(-10);
        to_date = to_date.slice(0, 10);
      }
      $('#step02_title').text(to_date + ' から ' + end_date);
      $('#step02_title').addClass('active');
      $('#step02_return_area').show();
    },
    // view  表示リセット
    reset_view: function() {
      to_date = '';
      end_date = '';
      $('.area-none').hide();
      $('#step02_title').removeClass('active');
      $('#step02_title').text('申請日を選択');
      $('#datepicker1').removeClass('cale-on');
      calendar1.destroy();
      calendar1 = flatpickr("#datepicker1", {
        inline: true,
        locale: 'ja',
        onChange: function(selectedDates, dateStr, instance) {
          $('#datepicker1').addClass('cale-on');
          model.get_mail_time(dateStr).done(function(data) {
            view.render_now_time(data);
          })
        }
      });
      $('#now_in_time, #now_out_time').val('');
      $('#input_in_time_area, #input_out_time_area').show();
      $('#step02_now_time').hide();
      $('#step02_return_area').hide();
    },
    show_mail_error: function() {
      $('.mail-err').show();
      setTimeout(function() {
        $('.mail-err').hide();
      }, 2000);
    },
  }

  $(function() {
    model.get_notice_data();
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
    calendar1 = flatpickr("#datepicker1", {
      inline: true,
      locale: 'ja'
    });
    // タイムピッカーの設定
    var edit_in_time_h = $('#now_in_time').attr('data-edit-in-time').slice(0, 2);
    var edit_in_time_m = $('#now_in_time').attr('data-edit-in-time').slice(3, 2);
    var edit_out_time_h = $('#now_out_time').attr('data-edit-out-time').slice(0, 2);
    var edit_out_time_m = $('#now_out_time').attr('data-edit-out-time').slice(3, 2);
    $("#now_in_time").timepicki({
      show_meridian: false,
      disable_keyboard_mobile: true,
      min_hour_value: 0,
      max_hour_value: 23,
      reset: true,
      increase_direction: 'down',
      step_size_minutes: edit_min,
      start_time: [edit_in_time_h, edit_in_time_m]
    });
    $("#now_out_time").timepicki({
      show_meridian: false,
      disable_keyboard_mobile: true,
      min_hour_value: 0,
      max_hour_value: 23,
      reset: true,
      increase_direction: 'down',
      step_size_minutes: edit_min,
      start_time: [edit_out_time_h, edit_out_time_m]
    });

    // 申請ボタンクリック
    $(document).delegate('.notice_flag', 'click', function() {
      notice_flag = Number($(this).attr('id').slice(-2));
      $('.notice_flag').removeClass('disabled');
      $(this).addClass('disabled');
      view.render_notice_title(notice_flag);
    });
    // リセットに戻るボタン　クリック
    $(document).delegate('#step02_return', 'click', function() {
      view.reset_view();
      view.render_notice_title(notice_flag);
    });
    // 申請ボタンアクティブ
    $('#textarea1').keyup(function() {
      if ($('#textarea1').val()) {
        $('#mail_submit').removeClass('disabled');
      } else {
        $('#mail_submit').addClass('disabled');
      }
    });
    // 申請送信ボタン　クリック
    $('#mail_submit').on('click touchstart', function(e) {
      e.preventDefault();
      $(this).addClass('disabled');

      function check_time(str) {
        if (str == null || str.length != 5) {
          return false;
        }
        return true;
      }
      if (check_time($('#now_in_time').val())) {
        var notice_in_time = $('#now_in_time').val() + ':00';
      } else {
        var notice_in_time = '';
      }
      if (check_time($('#now_out_time').val())) {
        var notice_out_time = $('#now_out_time').val() + ':00';
      } else {
        var notice_out_time = '';
      }
      if (notice_flag === 1) {
        if (notice_in_time === '') {
          view.show_mail_error();
          return;
        }
        if (notice_out_time !== '' && notice_in_time >= notice_out_time) {
          view.show_mail_error();
          return;
        }
      }

      var notice_data = {
        notice_in_time: notice_in_time,
        notice_out_time: notice_out_time
      };
      model.mail_submit(notice_data).done(function(data) {
        if (data == 'ok') {
          model.get_notice_data();
          siiimpleToast.message('申請完了しました', {
            position: 'top|right'
          });
          setTimeout(function() {
            location.href = '/mypage_dashboard';
          }, 3000);
        }
      }).fail(function(data) {
        siiimpleToast.alert('エラー', {
          position: 'top|right'
        });
        setTimeout("location.reload()", 3000);
      });
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