(function() {
  var table_list_weekly;
  var datepicker;
  var date;
  var startDate;
  var endDate;
  
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
  var formedDateOfThisWeek = function(today) {
    if (!today) {
      var today = new Date();
    }
    var this_year = today.getFullYear();
    var this_month = today.getMonth();
    var this_date = today.getDate();
    var day_num = today.getDay();
    var this_monday = this_date - day_num + firstDayOfWeek;
    var this_sunday = this_monday + 6;
    var day = new String('日月火水木金土');
    //月曜日の年月日
    var start_date = new Date(this_year, this_month, this_monday);
    var start_date_w = start_date.getFullYear() + "年" + (start_date.getMonth() + 1) + "月" + start_date.getDate() + "日" + "(" + day.charAt( start_date.getDay() ) + ")";
    //日曜日の年月日
    var end_date = new Date(this_year, this_month, this_sunday);
    var end_date_w =  end_date.getFullYear() + "年" + (end_date.getMonth() + 1) + "月" + end_date.getDate() + "日" + "(" + day.charAt( end_date.getDay() ) +")";
    //文字列を作成
    var target_week = start_date_w + " ～ " + end_date_w;
    
    startDate = formatDate(start_date, 'YYYY-MM-DD');
    endDate = formatDate(end_date, 'YYYY-MM-DD');
    
    return target_week;
  }
  
  var model = {
    // model テーブルコラムデータ取得
    getTableColumns: function() {
      return $.ajax({
        url: '../data/columns/list_weekly',
        dataType: 'json'
      })
    }
  }
  
  var view = {
    // view テーブルコラム設定表示
    renderTableColumns: function(data) {
      table_list_weekly = new Tabulator("#data_table", {
        height: 'calc(100vh - 259px)',
        columns: data,
      })
    },
    // view datepicker 
    renderDate: function() {
      datepicker = flatpickr('#datepicker', {
        "plugins": [new weekSelect({})],
        locale: {
          firstDayOfWeek: firstDayOfWeek
        },
        dateFormat: 'Y年 第W週',
        defaultDate: 'today',
        "onChange": function() {
          date = datepicker.selectedDates[0];
          var date_text = formedDateOfThisWeek(date);
          $('#to_from_date').text(date_text);
        }
      });
      date = datepicker.selectedDates[0];
    },
    // view date form - to text 
    renderDateText: function(date) {
      if (!date) {
        date = new Date();
      }
      datepicker.setDate(date);
      var date_text = formedDateOfThisWeek(date);
      $('#to_from_date').text(date_text);
      var date_format = formatDate(new Date(date), 'YYYY-MM-DD');
      var now_date = formatDate(new Date(), 'YYYY-MM-DD');
      if (date_format === now_date) {
        $('#date_weekly').addClass('disable');
        $('#today_mark').addClass('today');
        $('#today_mark').text('今週');
      } else {
        $('#date_weekly').removeClass('disable');
        $('#today_mark').removeClass('today');
      }
    },
    // view テーブルデータ表示
    renderTableData: function() {
      table_list_weekly.clearData();
      table_list_weekly.replaceData('../data/admin_list_weekly/table_data', {
        start_date: startDate,
        end_date: endDate
      }, 'POST');
    }
  }
  
  $(function() {
    view.renderDate();
    view.renderDateText(date);
    model.getTableColumns().done(function(data) {
      view.renderTableColumns(data);
    });
    
    $('#less_weekly').on('click', function(e) {
      date = addDate(new Date(date), -7);
      view.renderDateText(date);
    });
    $('#add_weekey').on('click', function(e) {
      date = addDate(new Date(date), 7);
      view.renderDateText(date);
    });
    $(document).delegate('#today_mark, #date_weekly', 'click', function() { // 今週ボタン
      date = new Date();
      view.renderDateText(date);
    });
    
  });
}());