
function getData(id) {
    if (!id) return;
    return $.ajax({
        type: 'POST',
        url: 'data.php',
        dataType: 'json',
        data: {
            id: id,
            date: $('#select_date').val()
        }
    });
}



$(function() {
    $('.select').on('change', function() {
        let now = new Date();
        let nowYear = now.getFullYear();
        let nowMonth = now.getMonth()+1;
        let nowDay = now.getDate();
        let nowDate = `${nowYear}年${nowMonth}月${nowDay}日`;
        let last = new Date(nowYear, nowMonth, 0);
        let lastDay = last.getDate();
        let lastDate = `${nowYear}年${nowMonth}月${lastDay}日`;
        let selectDate = $('#select_date').children(':selected').text();
        $('.field-value').val("");
        let id = $('#select_company').val();
        if (id) {
            getData(id).done(function(data) {
                $('#field_no').val("W"+nowYear+nowMonth+nowDay+data.company.bill_no);
                $('#field_outputdate').val(nowDate);
                $('#field_limitdate').val(lastDate);
                $('#field_to').val(data.company.company_fullname+" 御中");
                $('#field_title').val("システム利用料");
                $('#field_item1').val("打刻keeper利用料 "+selectDate);
                let bill_type = data.company.bill_type;
                let note = bill_type == 1 ? "単価請求タイプ" : "基本料金＋利用料タイプ";
                $('#note').text(note+"　"+data.company.bill_note);
                let num = Number(data.data_num);
                if (num > 0) {
                    if (bill_type == 1) {
                        $('#field_item_detail1').val("ID数 "+num+" アカウント利用");
                        let price = Number(data.company.bill_price);
                        let total_price = price * num;
                        let tax =  Math.floor(total_price * 0.1);
                        let field_total_price = total_price + tax;
                        $('#field_price1').val(price+"円");
                        $('#field_num1').val(num);
                        $('#field_unit1').val("ID");
                        $('#field_item_price1').val(total_price.toLocaleString() + "円");
                        $('#field_price').val(total_price.toLocaleString() + "円");
                        $('#field_tax').val(tax.toLocaleString() + "円");
                        $('#field_total_price').val(field_total_price.toLocaleString() + "円");
                    }
                    if (bill_type == 2) {
                        let basicPrice = Math.floor(num/30)*1000+1000;
                        let accountPrice = Math.ceil(num/5)*1000;
                        let total_price = basicPrice + accountPrice;
                        let tax =  Math.floor(total_price * 0.1);
                        let field_total_price = total_price + tax;
                        $('#field_item_detail1').val("基本料金");
                        $('#field_price1').val(basicPrice.toLocaleString()+"円");
                        $('#field_num1').val('1.0');
                        $('#field_item_price1').val(basicPrice.toLocaleString() + "円");
                        $('#field_item_detail2').val("アカウント料金（"+num+"アカウント）");
                        $('#field_price2').val(accountPrice.toLocaleString()+"円");
                        $('#field_num2').val('1.0');
                        $('#field_item_price2').val(accountPrice.toLocaleString() + "円");
                        $('#field_price').val(total_price.toLocaleString() + "円");
                        $('#field_tax').val(tax.toLocaleString() + "円");
                        $('#field_total_price').val(field_total_price.toLocaleString() + "円");
                    }
                }
            }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            });
        }
    });
});