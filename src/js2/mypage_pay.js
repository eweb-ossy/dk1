import Swal from 'sweetalert2';
import toast from 'siiimple-toast';

(()=> {

    function exit() { // このページから抜ける
        const url = location.hostname === 'localhost' ? 'http://localhost:8000' : 'https://'+location.hostname;
        location.href = url+'/mypage_dashboard';
    }

    // 初期パスワード設定
    let newPassword;
    let input = 0;
    function newPasswordSet() {
        const text = input === 0 ? 'パスワードを数字4桁以内で入力してください' : 'もう一度入力して下さい';
        Swal.fire({
            title: 'パスワード設定',
            text: text,
            input: 'password',
            inputAttributes: {
                maxlength: 4,
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    if ($.isNumeric(value) && value.length === 4) {
                        if (input === 0) {
                            newPassword = value;
                            input = 1;
                            resolve();
                        } else if (input === 1 && value === newPassword) {
                            input = 2;
                            resolve();
                        } else {
                            resolve('入力数値が一致しません');
                        }
                    } else {
                        resolve('数字4桁で入力してください')
                    }
                })
            },
            validationMessage: '入力エラー',
        }).then(resp => {
            if (resp.isConfirmed && input === 1) {
                newPasswordSet();
            } else if (resp.isConfirmed && input === 2) {
                setPassword().done(function(res) { // 新規パスワード登録
                    toast.message('<b>パスワード登録完了しました</b><br>次回からこの給与明細を閲覧するために必要となります<br>パスワードは忘れないで下さい', {position: 'top|center', duration: 8000});
                })
            } else {
                exit(); // ページを抜ける
            }
        });
    }
    function setPassword() { // 新規パスワード登録
        return $.ajax({
            url: '../data/mypage_pay/setPassword',
            type: 'POST',
            data: {
                user_id: userId,
                password: newPassword
            }
        });
    }

    // 設定データ読み込み
    $.ajax({
        url: '../data/mypage_pay/getConfigData',
        dataType: 'json'
    }).done(function(data) {
        if (data.pay_password_flag == 1) { // 給与明細閲覧個人パスワード設定がありの場合
            checkSetPassword().done(function(data) { // パスワードデータの有無をチェック
                if (!data) { // パスワードデータがない場合、警告モーダルを表示
                    Swal.fire({
                        text: '給与明細を閲覧するにはパスワードを設定する必要があります',
                        icon: 'warning',
                        confirmButtonText: 'パスワード設定',
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed === false) { // キャンセル
                            exit(); // ページを抜ける
                        } else {
                            newPasswordSet();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'パスワード入力',
                        text: '閲覧するには設定したパスワードを入力してくだい',
                        showCancelButton: true,
                        input: 'password',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocorrect: 'off',
                        },
                        showLoaderOnConfirm: true,
                        preConfirm: (password) => {
                            return $.ajax({
                                url: '../data/mypage_pay/checkPassword',
                                type: 'POST',
                                data: {
                                    user_id: userId,
                                    password: password
                                }
                            }).done(function(data) {
                                if (data === 'ng') {
                                    Swal.showValidationMessage('パスワードが違います');
                                }
                            }).fail(function(XMLHttpRequest, textStatus, errorThrown){
                                Swal.showValidationMessage('エラー');
                            })
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then(resp => {
                        if (!resp.isConfirmed) {
                            exit(); // ページを抜ける
                        }
                    })
                }
            });
        }
    });

    function checkSetPassword() { // payment password 有無　チェック
        return $.ajax({
            url: '../data/mypage_pay/checkSetPassword',
            type: 'POST',
            dataType: 'json',
            data: {
                user_id: userId
            }
        });
    }

    
    let payData;
    let titleData;
    function getPayData(userId) {
        return $.ajax({
            url: '../data/mypage_pay/getPayData',
            type: 'POST',
            dataType: 'json',
            data: {
                user_id: userId
            }
        })
    }
    getPayData(userId).done(function(data) {
        payData = data.data;
        titleData = data.title;
        createSelector(data.data);
    });

    // 対象年月セレクター設定
    function createSelector(data) {
        const dateSelector = data.map(item => item['year']+'年'+item['month']+'月:'+item['year']+'-'+item['month']);
        let selectHtml = '<option value ="">年月選択</option>';
        dateSelector.forEach((element, key) => {
            const result = element.split(':');
            selectHtml += `<option value="${key}">${result[0]}</option>`;
        });
        $('#select_date').html(selectHtml);
        if (data.length === 0) {
            $('#pay_view').html('<p class="pay-text">明細データなし</p>');
        }
    }

    // select date 
    let data;
    $('#select_date').on('change', function() {
        data = payData[$(this).val()];
        if (!data) {
            $('#pay_view').empty();
            $('#pdf').addClass('disabled');
            return;
        }
        $('#pdf').removeClass('disabled');
        const name = data.name;
        const year = data.year;
        const month = data.month;
        let wareki = '';
        if (year > 2019) {
            wareki = `令和${year - 2019 + 1}`;
        } else if (year > 1989) {
            wareki = `平成${year - 1989 + 1}`;
        }
        let view_title = titleData['title'].title;
        view_title = view_title.replace('{name}', name);
        view_title = view_title.replace('{year}', year);
        view_title = view_title.replace('{wareki}', wareki);
        view_title = view_title.replace('{month}', month);
        const html = `
        <div class="inner">
            <table id="pay_table">
                <thead>
                <tr>
                    <td class="pay-title" colspan="8">${view_title}</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="head1" rowspan="4">${titleData['work'].title}</td>
                    <td class="head2">${data.work1 ? titleData['work1'].title : '' }</td>
                    <td class="head2">${data.work2 ? titleData['work2'].title : '' }</td>
                    <td class="head2">${data.work3 ? titleData['work3'].title : '' }</td>
                    <td class="head2">${data.work4 ? titleData['work4'].title : '' }</td>
                    <td class="head2">${data.work5 ? titleData['work5'].title : '' }</td>
                    <td class="head2">${data.work6 ? titleData['work6'].title : '' }</td>
                    <td class="head2">${data.work7 ? titleData['work7'].title : '' }</td>
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
                    <td class="head2">${data.work8 ? titleData['work8'].title : '' }</td>
                    <td class="head2">${data.work9 ? titleData['work9'].title : '' }</td>
                    <td class="head2">${data.work10 ? titleData['work10'].title : '' }</td>
                    <td class="head2">${data.work11 ? titleData['work11'].title : '' }</td>
                    <td class="head2">${data.work12 ? titleData['work12'].title : '' }</td>
                    <td class="head2">${data.work13 ? titleData['work13'].title : '' }</td>
                    <td class="head2">${data.work14 ? titleData['work14'].title : '' }</td>
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
                    <td class="head1" rowspan="4">${titleData['pay'].title}</td>
                    <td class="head2">${data.pay1 ? titleData['pay1'].title : '' }</td>
                    <td class="head2">${data.pay2 ? titleData['pay2'].title : '' }</td>
                    <td class="head2">${data.pay3 ? titleData['pay3'].title : '' }</td>
                    <td class="head2">${data.pay4 ? titleData['pay4'].title : '' }</td>
                    <td class="head2">${data.pay5 ? titleData['pay5'].title : '' }</td>
                    <td class="head2">${data.pay6 ? titleData['pay6'].title : '' }</td>
                    <td class="head2">${data.pay7 ? titleData['pay7'].title : '' }</td>
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
                    <td class="head2">${data.pay8 ? titleData['pay8'].title : '' }</td>
                    <td class="head2">${data.pay9 ? titleData['pay9'].title : '' }</td>
                    <td class="head2">${data.pay10 ? titleData['pay10'].title : '' }</td>
                    <td class="head2">${data.pay11 ? titleData['pay11'].title : '' }</td>
                    <td class="head2">${data.pay12 ? titleData['pay12'].title : '' }</td>
                    <td class="head2">${data.pay13 ? titleData['pay13'].title : '' }</td>
                    <td class="head2">${data.pay14 ? titleData['pay14'].title : '' }</td>
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
                    <td class="head1" rowspan="4">${titleData['deduct'].title}</td>
                    <td class="head2">${data.deduct1 ? titleData['deduct1'].title : '' }</td>
                    <td class="head2">${data.deduct2 ? titleData['deduct2'].title : '' }</td>
                    <td class="head2">${data.deduct3 ? titleData['deduct3'].title : '' }</td>
                    <td class="head2">${data.deduct4 ? titleData['deduct4'].title : '' }</td>
                    <td class="head2">${data.deduct5 ? titleData['deduct5'].title : '' }</td>
                    <td class="head2">${data.deduct6 ? titleData['deduct6'].title : '' }</td>
                    <td class="head2">${data.deduct7 ? titleData['deduct7'].title : '' }</td>
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
                    <td class="head2">${data.deduct8 ? titleData['deduct8'].title : '' }</td>
                    <td class="head2">${data.deduct9 ? titleData['deduct9'].title : '' }</td>
                    <td class="head2">${data.deduct10 ? titleData['deduct10'].title : '' }</td>
                    <td class="head2">${data.deduct11 ? titleData['deduct11'].title : '' }</td>
                    <td class="head2">${data.deduct12 ? titleData['deduct12'].title : '' }</td>
                    <td class="head2">${data.deduct13 ? titleData['deduct13'].title : '' }</td>
                    <td class="head2">${data.deduct14 ? titleData['deduct14'].title : '' }</td>
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
                    <td class="head1" rowspan="2">${titleData['total'].title}</td>
                    <td class="head2">${data.total1 ? titleData['total1'].title : '' }</td>
                    <td class="head2">${data.total2 ? titleData['total2'].title : '' }</td>
                    <td class="head2">${data.total3 ? titleData['total3'].title : '' }</td>
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
        </div>`;
        $('#pay_view').html(html);
    });

    // ダウンロード
    $('#pdf').on('click', function() {
        const form = document.createElement('form');
        form.setAttribute('action', '../data/download/pdf_pay');
        form.setAttribute('method', 'post');
        document.body.appendChild(form);
        const input = document.createElement('input');
        input.setAttribute('name', 'columns');
        input.setAttribute('value', JSON.stringify(titleData));
        form.appendChild(input);
        const input2 = document.createElement('input');
        input2.setAttribute('name', 'data');
        input2.setAttribute('value', JSON.stringify(data));
        form.appendChild(input2);
        form.submit();
    });
})();
