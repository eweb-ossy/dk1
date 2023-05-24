(function() {
  var table_list_to;
  var aporan_data;
  var advance_pay_data;

  var model = {
    // model テーブルコラム取得
    getTableColumns: function() {
      return $.ajax({
        url: '../data/columns/to',
        dataType: 'json',
        type: 'POST'
      })
    },
    // model 
    listData: function(list_data) {
      list__data = [];
      var data = list_data.filter(function(element) {
        return element.aporan === true;
      });
      aporan_data = [];
      $.each(data, function(key, val) {
        aporan_data.push(val['user_id']);
      });
      var advance_pay = list_data.filter(function(element) {
        return element.advance_pay === true;
      });
      advance_pay_data = [];
      $.each(advance_pay, function(key, val) {
        advance_pay_data.push(val['user_id']);
      });
    },
    // model data 保存
    saveData: function(change_data) {
      return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../../data/admin_to/save_data',
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
    renderTableColumns: function(data) {
      table_list_to = new Tabulator('#data_table', {
        height: 'calc(100vh - 280px)',
        columns: data,
        cellEdited: function(cell) {
          var change_data = [];
          change_data['field'] = cell.getField();
          change_data['value'] = cell.getValue();
          change_data['user_id'] = cell.getData().user_id;
          model.saveData(change_data);
        },
        invalidOptionWarnings: false,
      });
      table_list_to.hideColumn('state');
    },
    // view テーブルデータ表示
    renderTableData: function() {
      table_list_to.replaceData('../data/admin_to/table_data', {}, 'POST');
    },
    // view テーブルフィルター　従業員ステータス
    renderFilter: function(user_state_filter) {
      if (user_state_filter === '0') { // ALL
        table_list_to.clearFilter();
      }
      if (user_state_filter === '1') { // 既存
        table_list_to.setFilter('state', '=', user_state_filter);
      }
      if (user_state_filter === '2') { // 退職
        table_list_to.setFilter('state', '=', user_state_filter);
      }
    },
  }

  $(function() {
    model.getTableColumns().done(function(data) {
      view.renderTableColumns(data);
      view.renderTableData();
      table_list_to.setFilter('state', '=', 1); // フィルター
    });
    // フィルターセレクト操作
    $('select[name="user_state_filter"]').on('change', function() {
      var user_state_filter = $('select[name="user_state_filter"] option:selected').val();
      view.renderFilter(user_state_filter);
    });

  });

}());