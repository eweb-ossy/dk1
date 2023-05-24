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
    // model モーダル用　表示　時刻　取得
    get_time_val: function() {
      return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../data/gateway/mail_time_val'
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
    // model シフトデータ保存
    save_data: function() {
      return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../data/admin_shift/saveData',
        data: {
          user_id: userId,
          status: shift_status,
          dk_date: $('#time_edit_submit').attr('data-date'),
          in_time_h: $('#select-in_hour').val(),
          in_time_m: $('#select-in_min').val(),
          out_time_h: $('#select-out_hour').val(),
          out_time_m: $('#select-out_min').val(),
          rest: $('#rest_range').val()
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
          flag: 'admin'
        }
      })
    },
    // model register single 反映 
    ref_register: function() {
      return $.ajax({
        type: 'POST',
        url: '../data/admin_shift/refRegister',
        data: {
          user_id: userId,
          status: shift_status,
          dk_date: $('#time_edit_submit').attr('data-date'),
          in_time_h: $('#select-in_hour').val(),
          in_time_m: $('#select-in_min').val(),
          out_time_h: $('#select-out_hour').val(),
          out_time_m: $('#select-out_min').val()
        }
      })
    },
    // model register all month 反映 
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
  var view = {
    // view 日付　表示
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
      // cookie 
      if (Cookies.get('shiftMonth')) {
        date = new Date(Cookies.get('shiftMonth'));
        view.renderDateText(date);
      }
    },
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
    // view テーブル ユーザーコラム　表示
    renderUsersTableColumns: function() {
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
              view.render_del_select();
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
        view.renderUsersTableData();
        
        table_users.toggleColumn('group1_name');
        table_users.toggleColumn('group2_name');
        table_users.toggleColumn('group3_name');

        table_users.hideColumn('shift');
        table_users.hideColumn('register');
      });
    },
    // view テーブル シフトコラム　表示
    renderShiftTableColumns: function() {
      model.getShiftTableColumns().done(function(data) {
        table_shift = new Tabulator('#shift_table', {
          height: '100%',
          columns: data,
          placeholder: '従業員を選択',
          resizableColumns: false,
          rowFormatter: function(row) {
            if (row.getData().date === 'shift_today') {
              row.getElement().style.background = "#fcf8e3";
            }
            if (row.getData().date === 'shift_past') {
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
            var row_data = row.getData();
            view.render_modal(row_data);
          },
          invalidOptionWarnings: false,
        });
        view.renderShiftTableData();
      });
    },
    // view テーブル ユーザーデータ表示
    renderUsersTableData: function() {
      table_users.clearData();
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
      view.render_all();
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
    // view モーダル　時刻　表示
    render_time_val: function(data) {
      model.get_time_val().done(function(data) {
        $('.time-hour, .time-min').html('');
        $('.time-hour').append('<option value="">--</option>');
        $('.time-min').append('<option value="">--</option><option value="0">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option>');
        $.each(data['hour'], function(key, elem) {
          var text = ("0" + elem).slice(-2);
          $('.time-hour').append('<option value="' + elem + '">' + text + '</option>');
        });
      });
    },
    // view 時刻修正　モーダル　表示
    render_modal: function(row_data) {
      $('#shift_btn_area').show();
      var in_time = '';
      var out_time = '';
      var rest = '';
      register_save_flag = 0;
      $('#time_edit_submit').text('登録');
      $('#time_edit_submit').css('background-color', '#1591a2');
      if (shift_view_flag == 0) {
        $('#modal_date').text($('#month').val() + row_data.day + '日(' + row_data.week + ')');
        if (row_data.in_time) {
          in_time = row_data.in_time;
        }
        if (row_data.out_time) {
          out_time = row_data.out_time;
        }
        if (row_data.rest2) {
          rest = row_data.rest2;
        }
        view.render_shift_btn(row_data.status);
        var now_date = formatDate(new Date(Cookies.get('shiftMonth')), 'YYYY-MM');
        $('#time_edit_submit').attr('data-date', now_date + '-' + row_data.day);
      }
      if (shift_view_flag == 1) {
        var list_date = formatDate(new Date(row_data.event.start), 'YYYY年MM月DD日');
        var dayOfWeek = new Date(row_data.event.start).getDay();
        var dayOfWeekStr = ["（日）", "（月）", "（火）", "（水）", "（木）", "（金）", "（土）"][dayOfWeek];
        $('#modal_date').text(list_date + dayOfWeekStr);
        var status_id = row_data.event.id;
        var title = row_data.event.title;
        // title = title.substr(1);
        if (status_id.length > 1) {
          in_time = status_id.slice(0, 5);
          out_time = status_id.slice(-5);
          title = '出勤';
        }
        view.render_shift_btn_cal(title);
        var now_date = formatDate(new Date(row_data.event.start), 'YYYY-MM-DD');
        $('#time_edit_submit').attr('data-date', now_date);
      }
      if (in_time) {
        $('#in_time').text(in_time);
        $('#select-in_hour > option[value="' + Number(in_time.slice(0, 2)) + '"]').prop('selected', true);
        $('#select-in_min > option[value="' + Number(in_time.slice(-2)) + '"]').prop('selected', true);
      } else {
        $('#in_time').text('--');
        $('#select-in_hour').val('');
        $('#select-in_min').val('');
      }
      if (out_time) {
        $('#out_time').text(out_time);
        $('#select-out_hour > option[value="' + Number(out_time.slice(0, 2)) + '"]').prop('selected', true);
        $('#select-out_min > option[value="' + Number(out_time.slice(-2)) + '"]').prop('selected', true);
      } else {
        $('#out_time').text('--');
        $('#select-out_hour').val('');
        $('#select-out_min').val('');
      }
      $('#rest_range').prop('disabled', false);
      $('#rest_range').val(Number(row_data.rest2));
      $('.rest-btn-area').show();
      if (rest == '') {
        var rest_val = 0;
      } else {
        var rest_val = rest;
      }
      view.render_rest_btn(rest_val);
      $('#rest_value').text(rest_val + '分');
      
      $('.iziModal-header-title').text($('#user_name').text());
      $('.iziModal-header-subtitle').text('シフト修正');
      $('#modal2').iziModal('setIcon', 'icon-shift');
      
      if (shift_view_flag === 1) {
        if (row_data.el.classList.contains('register')) {
          $('.iziModal-header-title').text($('#user_name').text()+' の申請');
          $('.iziModal-header-subtitle').text('申請の反映登録をおこないます');
          $('#time_edit_submit').removeClass('disable');
          $('#time_edit_submit').text('反映');
          $('#time_edit_submit').css('background-color', '#464646');
          $('#modal2').iziModal('setIcon', 'icon-notice');
          $('#modal2').iziModal('setHeaderColor', '#464646');
          register_save_flag = 1;
        }
      }
      
      $('#modal2').iziModal('open');
    },
    // view 時刻修正　モーダル　休憩　定時時刻　アクティブ表示
    render_rest_btn: function(rest_val) {
      $('.r_btn').removeClass('active');
      $('#rest_' + rest_val).addClass('active');
    },
    // view モーダル　シフト用ボタン　表示
    render_shift_btn: function(status) {
      $('.rest-time-area').hide();
      var color = '#3788d8';
      switch (status) {
        case '未登録':
          $('.shift-btn').removeClass('disabled');
          $('.time_edit_area').hide();
          color = '#ccc';
          break;
        case '出勤':
          $('.shift-btn').removeClass('disabled');
          $('#state_work').addClass('disabled');
          $('.time_edit_area').show();
          shift_status = 0;
          break;
        case '公休':
          $('.shift-btn').removeClass('disabled');
          $('#state_rest').addClass('disabled');
          $('.time_edit_area').hide();
          shift_status = 1;
          color = '#ff7d73';
          break;
        case '有給':
          $('.shift-btn').removeClass('disabled');
          $('#state_raid').addClass('disabled');
          $('.time_edit_area').hide();
          shift_status = 2;
          color ='#40a598';
          break;
        default:
          $('.shift-btn').removeClass('disabled');
          $('.time_edit_area').hide();
      }
      $('#status_name').text(status);
      $('#status_name').css('color', color);
      $('#modal2').iziModal('setHeaderColor', color);
      return;
    },
    // view モーダル　シフト用ボタン　表示 calendar用
    render_shift_btn_cal: function(status) {
      $('.rest-time-area').hide();
      
      $('.shift-btn').removeClass('disabled');
      $('#state_work').addClass('disabled');
      $('.time_edit_area').show();
      shift_status = 0;
      var color = '#3788d8';
      if (status === '未登録' || status === '・未登録') {
        $('.shift-btn').removeClass('disabled');
        $('.time_edit_area').hide();
        status = '未登録';
        color = '#ccc';
      }
      if (status === '公休' || status === '・公休') {
        $('.shift-btn').removeClass('disabled');
        $('#state_rest').addClass('disabled');
        $('.time_edit_area').hide();
        shift_status = 1;
        status = '公休';
        color = '#ff7d73';
      }
      if (status === '有給' || status === '・有給') {
        $('.shift-btn').removeClass('disabled');
        $('#state_raid').addClass('disabled');
        $('.time_edit_area').hide();
        shift_status = 2;
        status = '有給';
        color ='#40a598';
      }
      $('#status_name').text(status);
      $('#status_name').css('color', color);
      $('#modal2').iziModal('setHeaderColor', color);
      return;
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
          view.render_modal(info);
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
      model.clear_shift_data();
      if (userId) {
        model.get_work_data().done(function(data) {
          calendar.addEventSource(data);
          model.get_calendar_shift_data().done(function(data) {
            calendar.addEventSource(data);
            model.get_register_data().done(function(data) {
              calendar.addEventSource(data);
              view.renderUserData();
              view.rendar_register_submit_btn(data.length);
            });
          });
        });
      }
      view.render_all();
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
    // view select del 
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
    // 日付表示
    view.renderDate();
    view.renderDateText(date);
    view.render_time_val();
    view.renderUsersTableColumns();
    
    if (shift_view_flag === 0) { // shift list table view 
      $('#register_submit_btn').hide();
      view.renderShiftTableColumns();
    }
    if (shift_view_flag === 1) { // shift calender view 
      $('#register_submit_btn').show();
      view.render_calendar();
    }

    if (userId) {
      $('#user_select_disable').removeClass('disabled').addClass('on');
    }

    $('#modal2').iziModal({ // モーダル設定
      headerColor: '#1591a2',
      onClosed: function() {
        $('#time_edit_submit').addClass('disable'); // 登録ボタン 非アクティブにする
      }
    });
    // 日付操作ボタン
    $('#less_month').on('click', function(e) {
      date = addDate(new Date(date), -1, 'MM');
      view.renderDateText(date);
      view.renderUsersTableData();
      if (shift_view_flag === 0) {
        view.renderShiftTableData();
      }
      if (shift_view_flag === 1) {
        calendar.prev();
        view.render_cal_data();
        view.rendar_cal_holiday();
      }
    });
    $('#add_month').on('click', function(e) {
      date = addDate(new Date(date), 1, 'MM');
      view.renderDateText(date);
      view.renderUsersTableData();
      if (shift_view_flag === 0) {
        view.renderShiftTableData();
      }
      if (shift_view_flag === 1) {
        calendar.next();
        view.render_cal_data();
        view.rendar_cal_holiday();
      }
    });
    $('#this_month_mark, #this_month').on('click', function(e) {
      date = new Date();
      date.setDate(1);
      view.renderDateText(date);
      view.renderUsersTableData();
      if (shift_view_flag === 0) {
        view.renderShiftTableData();
      }
      if (shift_view_flag === 1) {
        calendar.today();
        view.render_cal_data();
        view.rendar_cal_holiday();
      }
    });
    // authority
    if (authority === 2) {
      $('#csv_download_btn').addClass('disabled');
      $('.up-file').addClass('disabled');
    }
    // シフト登録用ファイルダウンロードボタン
    $('#csv_download_btn').on('click', function() {
      model.downloadCsv();
    });
    // シフト登録用ファイル　アップロードボタン
    $('input[type="file"]').change(function() {
      $('#loader').show();
      var files = $(this).prop('files');
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
    });
    // 詳細表示ボタン　クリック
    $('#users_table_btn').on('click', function() {
      $('#users_table_area').toggleClass('detaile_view');
      $(this).toggleClass('on');
      table_users.toggleColumn('group1_name');
      table_users.toggleColumn('group2_name');
      table_users.toggleColumn('group3_name');
    });
    // 時刻修正モーダル　操作
    $(document).delegate('#rest_range', 'input', function() { // 休憩スライダー操作　時間表示
      var rest_val = $(this).val();
      view.render_rest_btn(rest_val);
      $('#rest_value').text(rest_val + '分');
    });
    $(document).delegate('.r_btn', 'click', function() { // 休憩定時ボタン　クリック
      var rest_val = $(this).attr('data-time');
      view.render_rest_btn(rest_val);
      $('#rest_range').val(rest_val);
      $('#rest_value').text(rest_val + '分');
      $('#time_edit_submit').removeClass('disable'); // 登録ボタンアクティブ
    });
    $(document).delegate('.time-val', 'input change', function() { // モーダル入力変更があった場合　登録ボタンアクティブ
      $('#time_edit_submit').removeClass('disable');
    });
    $(document).delegate('.shift-btn', 'click', function() {
      view.render_shift_btn($(this).text());
      $('#time_edit_submit').removeClass('disable'); // 登録ボタンアクティブ
    });
    // 従業員　時刻データ修正登録　ボタン　クリック
    $(document).delegate('#time_edit_submit', 'click', function() {
      $('#time_edit_submit').addClass('disable'); // 登録ボタン 非アクティブにする
      
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
      
      if (register_save_flag === 1) {
        if (shift_status === 0 && $('#select-in_hour').val() === "" && $('#select-out_hour').val() === "") {
          return;
        }
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
              view.rendar_cal_holiday();
            }
          } else {
            view.show_err_toast('通信エラー');
          }
        }).fail(function() {
          view.show_err_toast('通信エラー');
        });
      }
    });

    $(document).delegate('#shift_view_change', 'click', function() {
      if (shift_view_flag === 0) { // list -> calender 
        $('#register_submit_btn').show();
        $('#shift_table').html('');
        $('#shift_table').removeClass('tabulator');
        $('#shift_view_title').html('<i class="far fa-calendar"></i> カレンダー');
        view.render_calendar();
        shift_view_flag = 1;
        return;
      }
      if (shift_view_flag === 1) { // calender -> list 
        $('#register_submit_btn').hide();
        $('#shift_table').html('');
        $('#shift_table').removeClass('fc fc-ltr fc-unthemed');
        model.clear_shift_data();
        view.renderShiftTableColumns();
        $('#shift_view_title').html('<i class="fas fa-list"></i> リスト');
        shift_view_flag = 0;
        return;
      }
    });
    
    $(document).delegate('#register_submit_btn', 'click', function() {
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
        view.rendar_cal_holiday();
      }).fail(function() {
        view.show_err_toast('通信エラー');
      });
    });
    
    //
    $(document).delegate('#select_group_1', 'change', function() {
      table_users.removeFilter('group1_name', '=', select_group_1);
      select_group_1 = $(this).val();
      if (select_group_1 !== 'ALL') {
        table_users.addFilter('group1_name', '=', select_group_1);
      }
    });
    $(document).delegate('#select_group_2', 'change', function() {
      table_users.removeFilter('group2_name', '=', select_group_2);
      select_group_2 = $(this).val();
      if (select_group_2 !== 'ALL') {
        table_users.addFilter('group2_name', '=', select_group_2);
      }
    });
    $(document).delegate('#select_group_3', 'change', function() {
      table_users.removeFilter('group3_name', '=', select_group_3);
      select_group_3 = $(this).val();
      if (select_group_3 !== 'ALL') {
        table_users.addFilter('group3_name', '=', select_group_3);
      }
    });
    
    $(document).delegate('#shift_status', 'change', function() {
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
    
    //
    $(document).delegate('#user_select_disable', 'click', function() {
      view.render_del_select();
    });
    
  });

}());