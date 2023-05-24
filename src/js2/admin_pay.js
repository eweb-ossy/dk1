import toast from 'siiimple-toast';
import {TabulatorFull as Tabulator} from 'tabulator-tables';
import Swal from 'sweetalert2'

(() => {

    // 給与ファイル読み込みボタン
    $('#pay_file_upload_btn').on('click', function() {
        $('#pay_file_input').click();
    });
    $('#pay_file_input').on('change', function() {
        const file = $(this)[0].files[0];
        if (file.type !== 'text/csv') {
            toast.alert('ファイルタイプが違います！<br>登録できるファイルは csv のみとなります。', {position: 'top|right', duration: 5000});
            return;
        }
        const reader = new FileReader();
        reader.readAsText(file, 'Shift_JIS');
        reader.onload = function(event) {
            const results = event.target.result.split('\n');
            const data = results.map(row => row.split(','));
            $.ajax({
                url: '../data/admin_pay/uploadFile',
                type: 'POST',
                dataType: 'json',
                data: {data:data},
            }).done(function(res) {
                console.log(res);
                getPayData().done(function(data) {
                    table.replaceData(data.data);
                    createSelector(data);
                })
            })
        }
    });

    // 給与データ読み込み
    function getPayData() {
        return $.ajax({
            url: '../data/admin_pay/getPayData',
            type: 'POST',
            dataType: 'json'
        })
    }
    getPayData().done(function(data) {
        createTable(data);
        createSelector(data);
    });

    // create selector 
    function createSelector(data) {
        const dateArray = data.data.map(item => item['year']+'年'+item['month']+'月:'+item['year']+'-'+item['month']);
        const nameArray = data.data.map(item => item['name']+'('+item['user_id']+'):'+item['user_id'] );
        const dateSelector = dateArray.filter(function (x, i, self) {
            return self.indexOf(x) === i;
        });
        const nameSelector = nameArray.filter(function (x, i, self) {
            return self.indexOf(x) === i;
        });
        let selectHtml = '<option value ="">年月選択</option>';
        dateSelector.forEach(element => {
            const result = element.split(':');
            selectHtml += `<option value="${result[1]}">${result[0]}</option>`;
        });
        $('#select_date').html(selectHtml);
        selectHtml = '<option value ="">名前選択</option>';
        nameSelector.forEach(element => {
            const result = element.split(':');
            selectHtml += `<option value="${result[1]}">${result[0]}</option>`;
        });
        $('#select_name').html(selectHtml);
    }
    // filter select
    $('#select_date').on('change', function() {
        const val = $(this).val();
        console.log(table.getFilters(true));
        if (val) {
            const result = val.split('-');
            table.addFilter('year', '=',  result[0]);
            table.addFilter('month', '=',  result[1]);
        } else {
            table.clearFilter();
        }
    });
    $('#select_name').on('change', function() {
        const val = $(this).val();
        if (val) {
            table.addFilter('user_id', '=', val);
        } else {
            table.clearFilter();
        }
    });
    $('#select_open').on('change', function() {
        const val = $(this).val();
        if (val) {
            table.addFilter('open', '=', val);
        } else {
            table.clearFilter();
        }
    });

    // update open data 
    function updateOpen(data) {
        return $.ajax({
            url: '../data/admin_pay/updateOpen',
            type: 'POST',
            dataType: 'json',
            data: data,
        });
    }

    // create table 
    let table;
    function createTable(data) {
        let column = data.column;
        column.unshift({field: 'open', title: '公開', editor: 'tickCross', hozAlign: 'center', formatter: function(cell, formatterParams) {var value = cell.getValue(); if (value == 1 || value == '公開') {return "公開"} else {return "";}}}, {field: 'name', title: '名前'}, {field: 'id', title: 'id', visible: false});
        table = new Tabulator('#table', {
            columns: column,
            data: data.data,
        });
        const title = data.title;
        table.on('cellEdited', function(cell) {
            const value = cell.getValue();
            const row = cell.getRow().getData();
            const data = {
                id: row.id,
                val: value === '公開' || value == 1 ? 1 : 0
            }
            updateOpen(data).done(function(data) {
                console.log(data);
            });
        });
        table.on('rowDblClick', function(e, row) {
            const data = row.getData();
            const name = data.name;
            const year = data.year;
            const month = data.month;
            let wareki = '';
            if (year > 2019) {
                wareki = `令和${year - 2019 + 1}`;
            } else if (year > 1989) {
                wareki = `平成${year - 1989 + 1}`;
            }
            let view_title = title['title'].title;
            view_title = view_title.replace('{name}', name);
            view_title = view_title.replace('{year}', year);
            view_title = view_title.replace('{wareki}', wareki);
            view_title = view_title.replace('{month}', month);
            const html = `
            <div class="pay-detail-view">
                <div class="inner">
                    <table id="pay_table">
                        <thead>
                        <tr>
                            <td class="pay-title" colspan="8">${view_title}</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="head1" rowspan="4">${title['work'].title}</td>
                            <td class="head2">${data.work1 ? title['work1'].title : '' }</td>
                            <td class="head2">${data.work2 ? title['work2'].title : '' }</td>
                            <td class="head2">${data.work3 ? title['work3'].title : '' }</td>
                            <td class="head2">${data.work4 ? title['work4'].title : '' }</td>
                            <td class="head2">${data.work5 ? title['work5'].title : '' }</td>
                            <td class="head2">${data.work6 ? title['work6'].title : '' }</td>
                            <td class="head2">${data.work7 ? title['work7'].title : '' }</td>
                        </tr>
                        <tr>
                            <td>${data.work1 ? data.work1 : ''}</td>
                            <td>${data.work2 ? data.work2 : ''}</td>
                            <td>${data.work3 ? data.work3 : ''}</td>
                            <td>${data.work4 ? data.work4 : ''}</td>
                            <td>${data.work5 ? data.work5 : ''}</td>
                            <td>${data.work6 ? data.work6 : ''}</td>
                            <td>${data.work7 ? data.work7 : ''}</td>
                        </tr>
                        <tr>
                            <td class="head2">${data.work8 ? title['work8'].title : '' }</td>
                            <td class="head2">${data.work9 ? title['work9'].title : '' }</td>
                            <td class="head2">${data.work10 ? title['work10'].title : '' }</td>
                            <td class="head2">${data.work11 ? title['work11'].title : '' }</td>
                            <td class="head2">${data.work12 ? title['work12'].title : '' }</td>
                            <td class="head2">${data.work13 ? title['work13'].title : '' }</td>
                            <td class="head2">${data.work14 ? title['work14'].title : '' }</td>
                        </tr>
                        <tr>
                            <td>${data.work8 ? data.work8 : ''}</td>
                            <td>${data.work9 ? data.work9 : ''}</td>
                            <td>${data.work10 ? data.work10 : ''}</td>
                            <td>${data.work11 ? data.work11 : ''}</td>
                            <td>${data.work12 ? data.work12 : ''}</td>
                            <td>${data.work13 ? data.work13 : ''}</td>
                            <td>${data.work14 ? data.work14 : ''}</td>
                        </tr>
                        <tr class="margin">
                            <td class="margin" colspan="8"></td>
                        </tr>
                        <tr>
                            <td class="head1" rowspan="4">${title['pay'].title}</td>
                            <td class="head2">${data.pay1 ? title['pay1'].title : '' }</td>
                            <td class="head2">${data.pay2 ? title['pay2'].title : '' }</td>
                            <td class="head2">${data.pay3 ? title['pay3'].title : '' }</td>
                            <td class="head2">${data.pay4 ? title['pay4'].title : '' }</td>
                            <td class="head2">${data.pay5 ? title['pay5'].title : '' }</td>
                            <td class="head2">${data.pay6 ? title['pay6'].title : '' }</td>
                            <td class="head2">${data.pay7 ? title['pay7'].title : '' }</td>
                        </tr>
                        <tr>
                            <td>${data.pay1 ? Number(data.pay1).toLocaleString() : ''}</td>
                            <td>${data.pay2 ? Number(data.pay2).toLocaleString() : ''}</td>
                            <td>${data.pay3 ? Number(data.pay3).toLocaleString() : ''}</td>
                            <td>${data.pay4 ? Number(data.pay4).toLocaleString() : ''}</td>
                            <td>${data.pay5 ? Number(data.pay5).toLocaleString() : ''}</td>
                            <td>${data.pay6 ? Number(data.pay6).toLocaleString() : ''}</td>
                            <td>${data.pay7 ? Number(data.pay7).toLocaleString() : ''}</td>
                        </tr>
                        <tr>
                            <td class="head2">${data.pay8 ? title['pay8'].title : '' }</td>
                            <td class="head2">${data.pay9 ? title['pay9'].title : '' }</td>
                            <td class="head2">${data.pay10 ? title['pay10'].title : '' }</td>
                            <td class="head2">${data.pay11 ? title['pay11'].title : '' }</td>
                            <td class="head2">${data.pay12 ? title['pay12'].title : '' }</td>
                            <td class="head2">${data.pay13 ? title['pay13'].title : '' }</td>
                            <td class="head2">${data.pay14 ? title['pay14'].title : '' }</td>
                        </tr>
                        <tr>
                            <td>${data.pay8 ? Number(data.pay8).toLocaleString() : ''}</td>
                            <td>${data.pay9 ? Number(data.pay9).toLocaleString() : ''}</td>
                            <td>${data.pay10 ? Number(data.pay10).toLocaleString() : ''}</td>
                            <td>${data.pay11 ? Number(data.pay11).toLocaleString() : ''}</td>
                            <td>${data.pay12 ? Number(data.pay12).toLocaleString() : ''}</td>
                            <td>${data.pay13 ? Number(data.pay13).toLocaleString() : ''}</td>
                            <td>${data.pay14 ? Number(data.pay14).toLocaleString() : ''}</td>
                        </tr>
                        <tr class="margin">
                            <td class="margin" colspan="8"></td>
                        </tr>
                        <tr>
                            <td class="head1" rowspan="4">${title['deduct'].title}</td>
                            <td class="head2">${data.deduct1 ? title['deduct1'].title : '' }</td>
                            <td class="head2">${data.deduct2 ? title['deduct2'].title : '' }</td>
                            <td class="head2">${data.deduct3 ? title['deduct3'].title : '' }</td>
                            <td class="head2">${data.deduct4 ? title['deduct4'].title : '' }</td>
                            <td class="head2">${data.deduct5 ? title['deduct5'].title : '' }</td>
                            <td class="head2">${data.deduct6 ? title['deduct6'].title : '' }</td>
                            <td class="head2">${data.deduct7 ? title['deduct7'].title : '' }</td>
                        </tr>
                        <tr>
                            <td>${data.deduct1 ? Number(data.deduct1).toLocaleString() : ''}</td>
                            <td>${data.deduct2 ? Number(data.deduct2).toLocaleString() : ''}</td>
                            <td>${data.deduct3 ? Number(data.deduct3).toLocaleString() : ''}</td>
                            <td>${data.deduct4 ? Number(data.deduct4).toLocaleString() : ''}</td>
                            <td>${data.deduct5 ? Number(data.deduct5).toLocaleString() : ''}</td>
                            <td>${data.deduct6 ? Number(data.deduct6).toLocaleString() : ''}</td>
                            <td>${data.deduct7 ? Number(data.deduct7).toLocaleString() : ''}</td>
                        </tr>
                        <tr>
                            <td class="head2">${data.deduct8 ? title['deduct8'].title : '' }</td>
                            <td class="head2">${data.deduct9 ? title['deduct9'].title : '' }</td>
                            <td class="head2">${data.deduct10 ? title['deduct10'].title : '' }</td>
                            <td class="head2">${data.deduct11 ? title['deduct11'].title : '' }</td>
                            <td class="head2">${data.deduct12 ? title['deduct12'].title : '' }</td>
                            <td class="head2">${data.deduct13 ? title['deduct13'].title : '' }</td>
                            <td class="head2">${data.deduct14 ? title['deduct14'].title : '' }</td>
                        </tr>
                        <tr>
                            <td>${data.deduct8 ? Number(data.deduct8).toLocaleString() : ''}</td>
                            <td>${data.deduct9 ? Number(data.deduct9).toLocaleString() : ''}</td>
                            <td>${data.deduct10 ? Number(data.deduct10).toLocaleString() : ''}</td>
                            <td>${data.deduct11 ? Number(data.deduct11).toLocaleString() : ''}</td>
                            <td>${data.deduct12 ? Number(data.deduct12).toLocaleString() : ''}</td>
                            <td>${data.deduct13 ? Number(data.deduct13).toLocaleString() : ''}</td>
                            <td>${data.deduct14 ? Number(data.deduct14).toLocaleString() : ''}</td>
                        </tr>
                        <tr class="margin">
                            <td class="margin" colspan="8"></td>
                        </tr>
                        <tr>
                            <td class="head1" rowspan="2">${title['total'].title}</td>
                            <td class="head2">${data.total1 ? title['total1'].title : '' }</td>
                            <td class="head2">${data.total2 ? title['total2'].title : '' }</td>
                            <td class="head2">${data.total3 ? title['total3'].title : '' }</td>
                            <td class="head2"></td>
                            <td class="head2"></td>
                            <td class="head2"></td>
                            <td class="head2"></td>
                        </tr>
                        <tr>
                            <td>${data.total1 ? Number(data.total1).toLocaleString() : ''}</td>
                            <td>${data.total2 ? Number(data.total2).toLocaleString() : ''}</td>
                            <td>${data.total3 ? Number(data.total3).toLocaleString() : ''}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="margin">
                            <td class="margin" colspan="8"></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="comment" colspan="8">${data.memo ? data.memo : ''}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>`;
            Swal.fire({
                html: html,
                width: '90%'
            });
        });
    }



})();