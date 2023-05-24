// MyPage 従業員 勤務状況（日別）
(function() {
  var mobile_menu_visible = 0; // モバイルサイドバー用
  var table_list_day; // table
  var datepicker; // date picker
  var date; // date
  var user_name;
  var list__data = [];
  var timepicker_in; // モーダル用picker
  var timepicker_out; // モーダル用picker
  var row_data = {};
  var download_column;
  var download_data; // ダウンロード用データ格納
  var all_data = {}; // 集計データ収納

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
  var addDate = function(date, num, interval) { // 日付の増減処理
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
    // model テーブルコラムデータ取得
    getTableColumns: function() {
      return $.ajax({
        url: '../data/columns/list_day',
        dataType: 'json'
      })
    },
    // model モーダル 保存
    save_data: function() {
      return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../../data/admin_list_day/save',
        data: {
          today: formatDate(new Date(date), 'YYYY-MM-DD'),
          user_id: row_data.user_id,
          in_time: $('#picker_in_time').val(),
          out_time: $('#picker_out_time').val(),
          rest: $('#rest_range').val(),
          memo: $('#memo').val(),
          area_id: $('select[name="place"] option:selected').attr('data-area-id'),
          flag: 'mypage'
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
      input3.setAttribute('value', 'day');
      form.appendChild(input3);
      var input4 = document.createElement('input');
      input4.setAttribute('type', 'hidden');
      input4.setAttribute('name', 'data_date');
      input4.setAttribute('value', $('#datepicker').val());
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
        return element.work_hour2 > 0
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
    },
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

  var view = {
    // view テーブルコラム設定表示
    renderTableColumns: new Promise(function(resolve, reject) {
      model.getTableColumns().done(function(data) {
        download_column = data;
        table_list_day = new Tabulator("#data_table", {
          height: 'calc(100vh - 142px)',
          columns: data,
          tooltips: function(cell) {
            var cells = cell.getColumn();
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
            var val = cell.getValue();
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
          dataFiltered: function(filters, rows) {
            var list_data = [];
            if (rows.length > 0) {
              for (var i = 0; i < rows.length; i++) {
                list_data.push(rows[i]._row.data);
              }
              model.listData(list_data);
              view.renderListData();
            }
            tableDataPush(rows);
          },
          dataSorted: function(sorters, rows) {
            var list_data = [];
            if (rows.length > 0) {
              for (var i = 0; i < rows.length; i++) {
                list_data.push(rows[i]._row.data);
              }
              model.listData(list_data);
              view.renderListData();
            }
            tableDataPush(rows);
          },
          rowClick: function(e, row) {
            row_data = row.getData();
            if (!row_data.user_id || edit_flag === 0) {
              return;
            }
            view.render_modal(row_data);
          },
          invalidOptionWarnings: false,
        });
        resolve();
        table_list_day.hideColumn('work_hour2');
        table_list_day.hideColumn('rest_hour2');
        table_list_day.hideColumn('over_hour2');
        table_list_day.hideColumn('night_hour2');
        table_list_day.hideColumn('group1_order');
        table_list_day.hideColumn('group2_order');
        table_list_day.hideColumn('group3_order');
        table_list_day.hideColumn('in_latitude');
        table_list_day.hideColumn('in_longitude');
        table_list_day.hideColumn('out_latitude');
        table_list_day.hideColumn('out_longitude');
        table_list_day.hideColumn('area_id');
        table_list_day.hideColumn('normal_hour2');
        table_list_day.hideColumn('shift_hour2');
        table_list_day.hideColumn('shift_rest');
        if (mypage_status_inout_view_flag === 1) {
          table_list_day.hideColumn('in_work_time');
          table_list_day.hideColumn('out_work_time');
        }
        if (mypage_status_inout_view_flag === 2) {
          table_list_day.hideColumn('in_time');
          table_list_day.hideColumn('out_time');
        }
        
        table_list_day.hideColumn('group1_name');
        table_list_day.hideColumn('group2_name');
        table_list_day.hideColumn('group3_name');
      });
    }),
    // view date picker 年月日表示
    renderDate: new Promise(function(resolve, reject) {
      datepicker = flatpickr('#datepicker', {
        locale: 'ja',
        dateFormat: 'Y年m月d日(D)',
        defaultDate: 'today',
        locale: {
          firstDayOfWeek: 0
        },
        onChange: function() {
          date = datepicker.selectedDates[0];
          view.renderTableData();
        }
      });
      resolve();
    }),
    // view テーブルデータ表示
    renderTableData: function() {
      datepicker.setDate(date);
      var date_format = formatDate(new Date(date), 'YYYY-MM-DD');
      var now_date = formatDate(new Date(), 'YYYY-MM-DD');
      if (date_format === now_date) {
        $('#date_today').addClass('disable');
        $('#today_mark').addClass('today');
        $('#today_mark').text('本日');
      } else {
        $('#date_today').removeClass('disable');
        $('#today_mark').removeClass('today');
        var diff_day = Math.ceil((date - new Date()) / 86400000);
        if (diff_day > 0) {
          $('#today_mark').text(diff_day + '日後');
        } else {
          $('#today_mark').text(Math.abs(diff_day) + '日前');
        }
      }
      
      var options = {era: 'long', year: 'numeric'};
      var wareki = new Intl.DateTimeFormat('ja-JP-u-ca-japanese', options).format(new Date(date));
      $('#date-area-wareki').text(wareki);
      
      table_list_day.clearData();
      table_list_day.replaceData('../data/admin_list_day/table_data', {
        date: date_format,
        user_id: userId
      }, 'POST');
    },
    // view 時刻修正　モーダル　表示
    render_modal: function(row_data) {
      // Mypageはシフト編集はなし
      $('#shift_view_btn').hide();
      // モーダル 日付表示
      $('#modal_date').text($('#datepicker').val());
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
      $('.iziModal-header-title').text(row_data.user_name);
      var group1_title = $('[tabulator-field="group1_name"] > .tabulator-col-content > .tabulator-col-title').text();
      var group2_title = $('[tabulator-field="group2_name"] > .tabulator-col-content > .tabulator-col-title').text();
      var group3_title = $('[tabulator-field="group3_name"] > .tabulator-col-content > .tabulator-col-title').text();
      $('.iziModal-header-subtitle').text('ID：' + row_data.user_id + '　' + group1_title + '：' + row_data.group1_name + '　' + group2_title + '：' + row_data.group2_name + '　' + group3_title + '：' + row_data.group3_name);
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

      //
      $('#time_edit_submit').attr('data-user-id', row_data.user_id);
      $('#time_edit_submit').addClass('disable');
      $('#modal2').iziModal('open');
    },
    // view 時刻修正　モーダル　休憩　定時時刻　アクティブ表示
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
    // view モーダル バリデーション
    render_modal_check: function() {
      $('#time_edit_submit').addClass('disable');
      $('.time-input, .rest-value, .shift-rest-value').removeClass('error');
      $('#work_value, #shift_value').text('--');
      $('#work_value2, #shift_value2').text('');
      var select_in_time_hour = new Date(timepicker_in.selectedDates).getHours();
      var select_in_time_minute = new Date(timepicker_in.selectedDates).getMinutes();
      var select_in_time = select_in_time_hour * 60 + select_in_time_minute;
      var select_out_time_hour = new Date(timepicker_out.selectedDates).getHours();
      var select_out_time_minute = new Date(timepicker_out.selectedDates).getMinutes();
      var select_out_time = select_out_time_hour * 60 + select_out_time_minute;
      var in_time_val = $('#picker_in_time').val();
      var out_time_val = $('#picker_out_time').val();
      if (over_day > 0) { // 日またぎ処理
        if (select_in_time_hour <= over_day) {
          select_in_time += 1440; // 24H 
          var hour = in_time_val.substr(0, 2);
          var minute = in_time_val.substr(-2);
          hour = Number(hour)+24;
          in_time_val = hour + ':' + minute;
        }
        if (select_out_time_hour <= over_day) {
          select_out_time += 1440; // 24H
          var hour = out_time_val.substr(0, 2);
          var minute = out_time_val.substr(-2);
          hour = Number(hour)+24;
          out_time_val = hour + ':' + minute;
        }
      }
      var rest = $('#rest_range').val();
      if (!select_in_time && !select_out_time && rest > 0) {
        $('.rest-value').addClass('error'); // 出退勤時刻なし+休憩時間あり -> error
        return;
      }
      if (select_in_time && !select_out_time && rest > 0) {
        $('.rest-value').addClass('error'); // 出勤時刻のみ+休憩時間あり -> error 
        return;
      }
      if (!select_in_time && select_out_time || !select_in_time && select_out_time === 0) {
        $('#picker_in_time').addClass('error'); // 退勤時刻のみ -> error 
        if (rest > 0) {
          $('.rest-value').addClass('error'); // 退勤時刻のみ+休憩時間あり -> error 
        }
        return;
      }
      if (select_in_time && select_out_time) { // 出勤時刻あり+退勤時刻あり
        var select_time_diff = select_out_time - select_in_time; // 勤務時間（休憩時間引かない）
        if ((select_time_diff - rest) <= 0) {
          $('#picker_out_time').addClass('error'); // 休憩時間が勤務時間より多い -> error 
          return;
        }
        if (select_time_diff <= rest) {
          $('.rest-value').addClass('error'); // 勤務時間が休憩時間より少ない -> error 
          return;
        }
        select_time_diff -= rest; // 勤務時間（休憩引く）
        $('#work_value').text(select_time_diff+'分');
        var hours = (select_time_diff / 60);
        var rhours = Math.floor(hours);
        var minutes = (hours - rhours) * 60;
        var rminutes = Math.round(minutes);
        $('#work_value2').text(rhours+':'+('0' + rminutes).slice(-2));
      }
      // 初期データと差異があった場合、修正ボタンをactiveにする
      if (row_data.in_work_time != in_time_val) {
        $('#time_edit_submit').removeClass('disable');
      }
      if (row_data.out_work_time != out_time_val) {
        $('#time_edit_submit').removeClass('disable');
      }
      var rest_hour2 = row_data.rest_hour2 ? row_data.rest_hour2 : 0;
      if (rest_hour2 != rest) {
        $('#time_edit_submit').removeClass('disable');
      }
      var area_id = $('select[name="place"] option:selected').attr('data-area-id');
      if (row_data.area_id != area_id) {
        $('#time_edit_submit').removeClass('disable');
      }
      var memo = $('#memo').val();
      if (row_data.memo != memo) {
        $('#time_edit_submit').removeClass('disable');
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
    // view 表上部　合計値表示
    renderListData: function() {
      setTimeout(function() {
        $('.tabulator-calcs-top > [tabulator-field="user_id"]').text('合計');
        $('.tabulator-calcs-top > [tabulator-field="work_hour"]').text(all_data['work_hour']);
        $('.tabulator-calcs-top > [tabulator-field="work_minute"]').text(all_data['work_minute']);
        $('.tabulator-calcs-top > [tabulator-field="rest_hour"]').text(all_data['rest_hour']);
        $('.tabulator-calcs-top > [tabulator-field="rest_minute"]').text(all_data['rest_minute']);
        $('.tabulator-calcs-top > [tabulator-field="late_hour"]').text(all_data['late_hour']);
        $('.tabulator-calcs-top > [tabulator-field="late_minute"]').text(all_data['late_minute']);
        $('.tabulator-calcs-top > [tabulator-field="left_hour"]').text(all_data['left_hour']);
        $('.tabulator-calcs-top > [tabulator-field="left_minute"]').text(all_data['left_minute']);
        $('.tabulator-calcs-top > [tabulator-field="over_hour"]').text(all_data['over_hour']);
        $('.tabulator-calcs-top > [tabulator-field="over_minute"]').text(all_data['over_minute']);
        $('.tabulator-calcs-top > [tabulator-field="night_hour"]').text(all_data['night_hour']);
        $('.tabulator-calcs-top > [tabulator-field="night_minute"]').text(all_data['night_minute']);
        $('.tabulator-calcs-top > [tabulator-field="normal_hour"]').text(all_data['normal_hour']);
        $('.tabulator-calcs-top > [tabulator-field="normal_minute"]').text(all_data['normal_minute']);
        $('.tabulator-calcs-top > [tabulator-field="shift_hour"]').text(all_data['shift_hour']);
      }, 100);
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
    // 初期データ読み込み・表示
    Promise.all([view.renderTableColumns, view.renderDate]).then(function() { // テーブルコラムと年月日表示
      date = datepicker.selectedDates[0]; // 表示されてる年月日取得
      view.renderTableData(); // テーブルデータ表示
    });
    $('#modal2').iziModal({ // モーダル設定
      headerColor: '#1591a2',
      focusInput: false,
      width: 700
    });
    // 日付操作
    $(document).delegate('#less_day', 'click', function() { // 戻るボタン
      date = addDate(new Date(date), -1);
      view.renderTableData();
    });
    $(document).delegate('#add_day', 'click', function() { // 次へボタン
      date = addDate(new Date(date), 1);
      view.renderTableData();
    });
    $(document).delegate('#today_mark, #date_today', 'click', function() { // 本日ボタン
      date = new Date();
      view.renderTableData();
    });
    // 詳細表示ボタン　クリック
    $('#table_window_btn').on('click', function() {
      $(this).toggleClass('on');
      table_list_day.toggleColumn('group1_name');
      table_list_day.toggleColumn('group2_name');
      table_list_day.toggleColumn('group3_name');
    });
    
    // モーダル　操作
    $(document).delegate('#rest_range', 'input', function() { // 休憩スライダー操作　時間表示
      var rest_val = $(this).val();
      view.render_rest_btn(rest_val);
    });
    $(document).delegate('.r_btn', 'click', function() { // 休憩定時ボタン　クリック
      var rest_val = $(this).attr('data-time');
      view.render_rest_btn(rest_val);
    });
    $(document).delegate('.time-val', 'input change', function() { // モーダル入力変更があった場合
      view.render_modal_check();
    });
    $(document).delegate('#picker_del_in_time', 'click', function() {
      timepicker_in.clear();
      view.render_modal_check();
    });
    $(document).delegate('#picker_del_out_time', 'click', function() {
      timepicker_out.clear();
      view.render_modal_check();
    });
    $(document).delegate('#shift_view_btn', 'click', function() { // shift view 
      $(this).toggleClass('active-shift');
      $('#shift_btn_area').slideToggle();
      view.render_shift_btn(row_data.shift_status);
    });
    $(document).delegate('.shift-btn', 'click', function() { // シフトステータスボタン
      view.render_shift_btn($(this).text());
    });
    $(document).delegate('#shift_rest_range', 'input', function() { // シフト休憩スライダー操作　時間表示
      var rest_val = $(this).val();
      view.render_shift_rest_btn(rest_val);
    });
    $(document).delegate('.rs_btn', 'click', function() { // シフト休憩定時ボタン　クリック
      var rest_val = $(this).attr('data-time');
      view.render_shift_rest_btn(rest_val);
    });
    $(document).delegate('#picker_shift_del_in_time', 'click', function() {
      timepicker_shift_in.clear();
      view.render_modal_check();
    });
    $(document).delegate('#picker_shift_del_out_time', 'click', function() {
      timepicker_shift_out.clear();
      view.render_modal_check();
    });
    $(document).delegate('#time_edit_submit', 'click', function() { // 修正登録ボタン　クリック
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

    // ファイルダウンロードボタン
    $('#download_btn_excel').on('click', function() {
      model.downloadData('xlsx');
    });
    $('#download_btn_pdf').on('click', function() {
        model.downloadData('pdf');
    });

  });

}());