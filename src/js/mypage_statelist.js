// MyPage 従業員 勤務状況（集計）

(function() {
  var table_list_user;
  var datepicker;
  var all_data = {};
  var row_data = {};
  var download_column;
  var download_data;

  var date = new Date();
  date.setDate(1);

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

  var model = {
    getTableColumns: function() {
      return $.ajax({
        url: '../data/columns/list_user',
        dataType: 'json',
        type: 'POST'
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
      var all_work_count = list_data.filter(function(element) {
        return element.work_count > 0;
      });
      all_data['work_count'] = all_work_count.reduce(function(result, current) {
        return result + Number(current.work_count)
      }, 0);
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
      // 残業日数
      var all_over_count = list_data.filter(function(element) {
        return element.over_count > 0;
      });
      all_data['over_count'] = all_over_count.reduce(function(result, current) {
        return result + Number(current.over_count)
      }, 0);
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
      // 深夜日数
      var all_night_count = list_data.filter(function(element) {
        return element.night_count > 0;
      });
      all_data['night_count'] = all_night_count.reduce(function(result, current) {
        return result + Number(current.night_count)
      }, 0);
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
      // 遅刻日数
      var all_late_count = list_data.filter(function(element) {
        return element.late_count > 0;
      });
      all_data['late_count'] = all_late_count.reduce(function(result, current) {
        return result + Number(current.late_count)
      }, 0);
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
      // 早退日数
      var all_left_count = list_data.filter(function(element) {
        return element.left_count > 0;
      });
      all_data['left_count'] = all_left_count.reduce(function(result, current) {
        return result + Number(current.left_count)
      }, 0);
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
    }
  }

  var view = {
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
    },
    renderDateText: function(date) {
      // 
      datepicker.setDate(date);
      // 
      if (mypage_end_day > 0) {
        var pre_date = new Date(date);
        pre_date.setMonth(pre_date.getMonth() - 1);
        var pre_day = mypage_end_day + 1;
        var first_date = formatDate(pre_date, 'YYYY年MM月') + pre_day + '日';
        var end_date = formatDate(date, 'YYYY年MM月') + mypage_end_day + '日';
        var pre_month_days = new Date(pre_date.getFullYear(), pre_date.getMonth() + 1, 0);
        pre_month_days = pre_month_days.getDate();
        var days_num = (pre_month_days - mypage_end_day) + mypage_end_day;
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
          height: 'calc(100vh - 142px)',
          columns: data,
          tooltips: function(cell) {
            var cells = cell.getColumn();
            if (cells._column.field == 'work_count' || cells._column.field == 'work_hour' || cells._column.field == 'work_minute') {
              cell.getElement().style.color = "#1591a2";
            }
            if (cells._column.field == 'over_count' || cells._column.field == 'over_hour' || cells._column.field == 'over_minute' || cells._column.field == 'night_count' || cells._column.field == 'night_hour' || cells._column.field == 'night_minute' || cells._column.field == 'late_count' || cells._column.field == 'late_hour' || cells._column.field == 'late_minute' || cells._column.field == 'left_count' || cells._column.field == 'left_hour' || cells._column.field == 'left_minute') {
              cell.getElement().style.color = "#ff4560";
            }
            if (cells._column.field == 'normal_hour' || cells._column.field == 'normal_minute') {
              cell.getElement().style.color = "#673AB7";
            }
          },
          dataFiltered: function(filters, rows) {
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
          dataSorted: function(sorters, rows) {
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
            row_data = row.getData();
            if (!row_data.user_id) {
              return;
            }
            Cookies.set('MypagetoListsMonth', date);
            Cookies.set('MypagetoListsuserID', parseInt(row_data.user_id));
            window.location.href = 'mypage_list';
          },
          invalidOptionWarnings: false,
        });
        view.renderTableData();
      });
    },
    // view テーブルデータ表示
    renderTableData: function() {
      table_list_user.replaceData('../data/admin_list_user/table_data', {
        year: formatDate(new Date(date), 'YYYY'),
        month: formatDate(new Date(date), 'MM'),
        end_day: mypage_end_day,
        user_id: userId
      }, 'POST');
      table_list_user.hideColumn('work_hour2');
      table_list_user.hideColumn('over_hour2');
      table_list_user.hideColumn('night_hour2');
      table_list_user.hideColumn('late_hour2');
      table_list_user.hideColumn('left_hour2');
      table_list_user.hideColumn('normal_hour2');
      
      table_list_user.hideColumn('group1_name');
      table_list_user.hideColumn('group2_name');
      table_list_user.hideColumn('group3_name');
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

    view.renderDate();
    view.renderDateText(date);
    view.renderTableColumns();
    // 日付操作
    $(document).delegate('#less_month', 'click', function() { // 戻るボタン
      date = addDate(new Date(date), -1, 'MM');
      table_list_user.clearData();
      view.renderTableData();
      view.renderDateText(date);
    });
    $(document).delegate('#add_month', 'click', function() { // 次へボタン
      date = addDate(new Date(date), 1, 'MM');
      table_list_user.clearData();
      view.renderTableData();
      view.renderDateText(date);
    });
    $(document).delegate('#this_month_mark, #this_month', 'click', function() { // 今月ボタン
      date = new Date();
      date.setDate(1);
      table_list_user.clearData();
      view.renderTableData();
      view.renderDateText(date);
    });
    
    // 詳細表示ボタン　クリック
    $('#table_window_btn').on('click', function() {
      $(this).toggleClass('on');
      table_list_user.toggleColumn('group1_name');
      table_list_user.toggleColumn('group2_name');
      table_list_user.toggleColumn('group3_name');
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