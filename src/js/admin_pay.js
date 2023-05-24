(function() {

  var table_pay; // table

  if (Cookies.get('payMonth')) {
    var date = Cookies.get('payMonth');
  } else {
    var date = new Date();
    date .setDate(1);
  }
  var month_diff = Cookies.get('payMonthDiff');

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
    // model テーブルコラム取得
    getTableColumns: function() {
      return $.ajax({
        url: '../data/columns/pay',
        dataType: 'json',
        type: 'POST'
      })
    },
  }

  var view = {
    renderDate: function() {
      var now_date = new Date(date);
      if (end_day > 0) {
        var pre_date = new Date(date);
        pre_date.setMonth(pre_date.getMonth() - 1);
        var pre_day = end_day + 1;
        var first_date = formatDate(pre_date, 'YYYY年MM月') + pre_day + '日';
        var end_date = formatDate(now_date, 'YYYY年MM月') + end_day + '日';
        var pre_month_days = new Date(pre_date.getFullYear(), pre_date.getMonth() + 1, 0);
        pre_month_days = pre_month_days.getDate();
        var days_num = (pre_month_days - end_day) + end_day;
      } else {
        var first_date = formatDate(now_date, 'YYYY年MM月') + '01日';
        var end_date = formatDate(new Date(now_date.getFullYear(), now_date.getMonth() + 1, 0), 'YYYY年MM月DD日');
        var pre_month_days = new Date(now_date.getFullYear(), now_date.getMonth() + 1, 0);
        var days_num = pre_month_days.getDate();
      }
      $('#to_from_date').text(first_date + 'から' + end_date + 'までの' + days_num + '日間');
      $('#month').val(formatDate(now_date, 'YYYY年MM月'));
      Cookies.set('payMonth', date);
      Cookies.set('payMonthDiff', month_diff);
    },
    // view テーブルコラム　表示
    renderTableColumns: new Promise(function(resolve, reject) {
      model.getTableColumns().done(function(data) {
        table_pay = new Tabulator('#data_table', {
          height: 'calc(100vh - 260px)',
          columns: data,
          tooltips: function(cell) {
            var cells = cell.getColumn();
            if (cells._column.field == 'work_num' || cells._column.field == 'work_time') {
              cell.getElement().style.color = "#1591a2";
            }
          },
          invalidOptionWarnings: false,
        });
        // table_pay.hideColumn('resign_date');
        resolve();
        table_pay.hideColumn('group1_name');
        table_pay.hideColumn('group2_name');
        table_pay.hideColumn('group3_name');
        table_pay.hideColumn('entry_date');
        table_pay.hideColumn('interval');
        table_pay.hideColumn('start_month');
        table_pay.hideColumn('paid_month');
        table_pay.hideColumn('put_paid');
        table_pay.hideColumn('work_hour');
        table_pay.hideColumn('total_work_hour');
      });
    }),
    renderTableData: function() {
      var date_format = formatDate(new Date(date), 'YYYY-MM');
      var now_date = formatDate(new Date(), 'YYYY-MM');
      if (date_format === now_date) {
        $('#this_month').addClass('disable');
        $('#this_month_mark').addClass('this-month');
        $('#this_month_mark').text('今月');
        $('#add_month').addClass('disable');
        month_diff = 0;
      } else {
        $('#this_month').removeClass('disable');
        $('#this_month_mark').removeClass('this-month');
        $('#add_month').removeClass('disable');
        if (month_diff > 0) {
          $('#this_month_mark').text(month_diff + 'ヶ月後');
        } else {
          $('#this_month_mark').text(Math.abs(month_diff) + 'ヶ月前');
        }
      }
      table_pay.replaceData('../data/admin_pay/table_data', {
        year: formatDate(new Date(date), 'YYYY'),
        month: formatDate(new Date(date), 'MM'),
        end_day: end_day
      }, 'POST');
    },
  }

  $(function() {
    view.renderDate();
    Promise.all([view.renderTableColumns]).then(function() {
      view.renderTableData();
    });

    $('#less_month').on('click', function(e) {
      date = addDate(new Date(date), -1, 'MM');
      month_diff--;
      table_pay.clearData();
      view.renderTableData();
      view.renderDate();
    });
    $('#add_month').on('click', function(e) {
      date = addDate(new Date(date), 1, 'MM');
      month_diff++;
      table_pay.clearData();
      view.renderDate();
      view.renderTableData();
    });
    $('#this_month_mark, #this_month').on('click', function(e) {
      date = new Date();
      table_pay.clearData();
      view.renderTableData();
      view.renderDate();
    });

    // 詳細表示ボタン
    $('#users_table_btn').on('click', function() {
      $('#users_table_area').toggleClass('detaile_view');
      $(this).toggleClass('on');
      table_pay.toggleColumn('group1_name');
      table_pay.toggleColumn('group2_name');
      table_pay.toggleColumn('group3_name');
      table_pay.toggleColumn('entry_date');
      table_pay.toggleColumn('interval');
      table_pay.toggleColumn('start_month');
      table_pay.toggleColumn('paid_month');
      table_pay.toggleColumn('put_paid');
    });

  });

}());