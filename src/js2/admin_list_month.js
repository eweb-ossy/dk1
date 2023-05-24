import Cookies from 'js-cookie';
import flatpickr from "flatpickr";
import monthSelectPlugin from 'flatpickr/dist/plugins/monthSelect';
import { Japanese } from "flatpickr/dist/l10n/ja";
import {TabulatorFull as Tabulator} from 'tabulator-tables';

let selectDate = Cookies.get('listMonth') ? new Date(Cookies.get('listMonth')) : new Date();
let type = 1;
let dlData; // ダウンロード用テーブルデータ格納用

// カレンダー準備
const datepicker = flatpickr('#month', {
    locale: Japanese,
    defaultDate: selectDate,
    plugins: [
        new monthSelectPlugin({
            dateFormat: "Y年m月"
        })
    ],
    onChange: (selectedDates)=> {
        selectDate = selectedDates[0];
        updateTable();
    }
});

// テーブル準備
let table;
// const $loadingTableElem = document.getElementById("loading_table");
const getData = function() {
    const date = new Date(selectDate);
    $.ajax({
        url: '../data/Admin_list_month2/getdata',
        dataType: 'json',
        type: 'POST',
        data: {
            year: date.getFullYear(),
            month: (date.getMonth()+1).toString().padStart(2, '0'),
            type: type
        }
    }).done(function(data) {
        $('#to_from_date').text(data.text);
        table = new Tabulator('#data_table', {
            height: 'calc(100vh - 262px)',
            layout: 'fitDataStretch',
            columns: data.columns,
            data: data.data,
            rowFormatter: function(row) {
                const cells = row.getCells()
                cells.forEach(element => {
                    const field = element.getField();
                    const value = element.getValue();
                    if (field !== 'user_id' && field !== 'user_name' && field !== 'group1_name' && field !== 'group2_name' && field !== 'group3_name' && value) {
                        element.getElement().style.backgroundColor = "rgba(199, 249, 222, 0.7)";
                    }
                });
            },
        });
        // table.on('renderStarted', function() {
        //     $loadingTableElem.style.display ="flex";
        //     console.log('show');
        // });
        // table.on('renderComplete', function() {
        //     $loadingTableElem.style.display ="none";
        //     console.log('hide');
        // });
        table.on('dataFiltered', (filters, rows)=> {
            dlData = rows.map(element => element.getData());
        });
        table.on('dataSorted', function(sorters, rows) {
            dlData = rows.map(element => element.getData());
        });
    });
}
getData();
checkDate();

let outputDate;
function checkDate() {
    const now = new Date();
    const nowYear = now.getFullYear();
    const nowMonth = now.getMonth()+1;
    const selectYear = selectDate.getFullYear();
    const selectMonth = selectDate.getMonth()+1;
    outputDate = `${selectYear}年${selectMonth}月`;
    const d1 = new Date(nowYear, nowMonth);
    const d2 = new Date(selectYear, selectMonth);
    const months =  d1.getMonth() - d2.getMonth() + (12 * (d1.getFullYear() - d2.getFullYear()));
    if (months === 0) {
        $('#this_month').addClass('disable');
        $('#this_month_mark').addClass('this-month').text('今月');
    } else {
        $('#this_month').removeClass('disable');
        $('#this_month_mark').removeClass('this-month');
        if (months > 0) {
            $('#this_month_mark').text(months + 'ヶ月前');
        } else {
            $('#this_month_mark').text(Math.abs(months) + 'ヶ月後');
        }
    }
}

function updateTable() {
    datepicker.setDate(selectDate);
    Cookies.set('listMonth', selectDate);
    table.clearData();
    getData();
    checkDate();
}

$('#table_window_btn').on('click', function() {
    $(this).toggleClass('on');
    table.toggleColumn('group1_name');
    table.toggleColumn('group2_name');
    table.toggleColumn('group3_name');
});
$('.view-change-btn').on('click', function() {
    $('.view-change-btn').removeClass('on');
    $(this).addClass('on');
    type = $(this).attr('id').slice(-1);
    table.clearData();
    getData();
    if (type == 3) {
        $('#pdf').addClass('disabled');
    } else {
        $('#pdf').removeClass('disabled');
    }
});
$('#less_month').on('click', function() {
    selectDate = new Date(selectDate.setMonth(selectDate.getMonth()-1));
    updateTable();
});
$('#add_month').on('click', function() {
    selectDate = new Date(selectDate.setMonth(selectDate.getMonth()+1));
    updateTable();
});
$('#this_month_mark, #this_month').on('click', function() {
    selectDate = new Date();
    updateTable();
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
    input3.setAttribute('value', `月別集計 ${outputDate}`);
    form.appendChild(input3);
    form.submit();
});