import Cookies from 'js-cookie';
import toast from 'siiimple-toast';
import {TabulatorFull as Tabulator} from 'tabulator-tables';

(()=> {
    const editor = authority !== 2 && authority !== 5 ? true : false; // 編集権限

    $.ajax({ // テーブルデータ取得
        url: '../data/admin_users/getData',
        type: 'POST',
        dataType: 'json'
    }).done(function(tableData) {
        let dlData; // ダウンロード用テーブルデータ格納用
        const table = new Tabulator('#data_table', { // テーブル設定
            height: 'calc(100vh - 280px)',
            rowHeight: 18,
            columns: tableData.columns,
            data: tableData.users,
            initialFilter: [
                {field: 'state', type: '=', value: 1} // ステータス:1のみフィルター
            ],
            rowFormatter: function(row) {
                const data = row.getData();
                if (data.management_flag) { // 管理のみ従業員
                    row.getElement().style.backgroundColor = "#ccc";
                }
            },
        });
        table.on('dataLoaded', data => { // データ読み込み後 - 集計表示　 既存退職者比率
            const all = data.length;
            const allState = data.filter(element => element.state == 1).length;
            const allResign = data.filter(element => element.state == 2).length;
            $('#user_all_num').text(all);
            $('#user_state_num').text(allState);
            $('#user_resign_num').text(allResign);
            if (all > 0 && allState > 0) {
                $('#user_state_rate').text((allState / all * 100).toFixed(1));
            }
            if (all > 0 && allResign > 0) {
                $('#user_resign_rate').text((allResign / all * 100).toFixed(1));
            }
        });
        table.on('dataFiltered', (filters, rows)=> { // データフィルター後 - 集計表示 男女比率
            const all = rows.length;
            const man = rows.filter(element => element.getData().sex == '男').length;
            const woman = rows.filter(element => element.getData().sex == '女').length;
            const etc = rows.filter(element => element.getData().sex == '').length;
            $('#sex_man').text(man);
            $('#sex_woman').text(woman);
            $('#sex_no').text(etc);
            if (man > 0 && all > 0) {
                $('#sex_man_rate').text((man / all * 100).toFixed(1));
            } else {
                $('#sex_man_rate').text('-');
            }
            if (woman > 0 && all > 0) {
                $('#sex_woman_rate').text((woman / all * 100).toFixed(1));
            } else {
                $('#sex_woman_rate').text('-');
            }
            if (etc > 0 && all > 0) {
                $('#sex_no_rate').text((etc / all * 100).toFixed(1));
            } else {
                $('#sex_no_rate').text('-');
            }
            dlData = rows.map(element => element.getData()); // ダウンロード用テーブルデータ格納
        });
        table.on('cellClick', (e, cell)=> { // テーブルクリック
            if (editor && cell.getField() !== 'mypage_self' && cell.getField() !== 'shift_alert_flag') { //　編集権限者のみ & エディタ(自己修正、シフト警告)で反応しないように
                // user id をクッキーし、従業員詳細編集ページへ移動
                // これでいいのか？　今後、要検討
                Cookies.set('userDetailUserId', parseInt(cell.getData().user_id));
                window.location.href = 'admin_user_detail';
            }
        });
        $('select[name="user_state_filter"]').on('change', function() { // 従業員表示セレクタ変更時
            switch ($(this).val()) { // 各フィルターを実行
                case '0':
                    table.clearFilter();
                    break;
                case '1':
                    table.setFilter('state', '=', '1');
                    table.hideColumn('resign_date_view');
                    break;
                case '2':
                    table.setFilter('state', '=', '2');
                    table.showColumn('resign_date_view');
                    break;
                default:
                    break;
            }
        });
        table.on('cellEdited', cell => { // テーブル編集時 - データ保存
            $.ajax({
                type: 'POST',
                url: '../../data/admin_users/dataSave',
                data: {
                    user_id: cell.getData().user_id,
                    field: cell.getField(),
                    value: cell.getValue()
                }
            }).done(function(message) {
                if (message === 'error') {
                    toast.alert('エラー', {position: 'top|right'});
                } else {
                    toast.message(message, {position: 'top|right'});
                }
            })
        });
        table.on('dataSorted', function(sorters, rows) { // テーブルソート時　
            dlData = rows.map(element => element.getData()); // ダウンロード用テーブルデータ格納
        });
        $('.download-btn').on('click', function() {
            const type = $(this).attr('id');
            const form = document.createElement('form');
            form.setAttribute('action', '../data/download/'+type);
            form.setAttribute('method', 'post');
            document.body.appendChild(form);
            const input = document.createElement('input');
            input.setAttribute('name', 'columns');
            input.setAttribute('value', JSON.stringify(table.getColumnDefinitions()));
            form.appendChild(input);
            const input2 = document.createElement('input');
            input2.setAttribute('name', 'data');
            input2.setAttribute('value', JSON.stringify(dlData));
            form.appendChild(input2);
            const input3 = document.createElement('input');
            input3.setAttribute('name', 'title');
            input3.setAttribute('value', '従業員一覧');
            form.appendChild(input3);
            form.submit();
        });
        // $('#create_user_all').on('click', function() { // 一括登録
        //     $('#users_file_upload').click();
        // });
    });

    if (editor) { // 編集権限ありの場合は、新規従業員登録、一括登録ボタンを有効にする
        $('#create_user').on('click', function() {
            Cookies.set('userDetailUserId', 'new');
            window.location.href = 'admin_user_detail';
        });
    } else { // 権限なしは、ボタンを無効にする
        $('#create_user').addClass('disabled');
        // $('#create_user_all').addClass('disabled'); // 一括登録
    }

})();