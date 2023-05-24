import io from 'socket.io-client';
import Cookies from 'js-cookie';

const noticeId = Cookies.get('notice_id') ? Cookies.get('notice_id') : null;

function createNoticeData(data) {
    let statusText = "", icon = '', color = '', noticeTitle1 = "", noticeTime = "", typeColor = '';
    if (data.notice_status === '0') {
        if (data.user_id == userId) {
            statusText = "承認依頼中";
            icon = '<i class="far fa-paper-plane"></i>';
            color = 'alert-warning';
        } else {
            statusText = "申請";
            icon = '<i class="fas fa-bell"></i>';
            color = 'alert-info';
        }
    }
    if (data.notice_status === '1') {
        if (data.user_id == userId) {
            statusText = `${data.from_user_name}から承認されました`;
            icon = '<i class="fas fa-thumbs-up"></i>';
            color = 'alert-success';
        } else {
            statusText = `${data.from_user_name}が承認しました`;
            icon = '<i class="fas fa-thumbs-up"></i>';
            color = 'alert-success';
        }
    }
    if (data.notice_status === '2') {
        if (data.user_id == userId) {
            statusText = "申請NGです";
            icon = '<i class="fas fa-exclamation-up"></i>';
            color = 'alert-danger';
        } else {
            statusText = 'NG送信中';
            icon = '<i class="fas fa-exclamation-up"></i>';
            color = 'alert-danger';
        }
    }
    if (data.notice_flag === '1') {
        noticeTitle1 = "修正依頼";
        noticeTime = `${data.before_in_time}〜${data.before_out_time}を ${data.notice_in_time}〜${data.notice_out_time}に時刻修正を申請`;
        typeColor = ' type-color01';
    }
    if (data.notice_flag === '2') {
        noticeTitle1 = "削除依頼";
        noticeTime = `${data.before_in_time}〜${data.before_out_time}を 削除申請`;
        typeColor = ' type-color02';
    }
    if (data.notice_flag === '3') {
        noticeTitle1 = "遅刻依頼";
        noticeTime = `${data.notice_in_time} 出勤に 遅刻申請`;
        typeColor = ' type-color03';
    }
    if (data.notice_flag === '4') {
        noticeTitle1 = "早退依頼";
        noticeTime = `${data.notice_out_time} 退勤に 早退申請`;
        typeColor = ' type-color04';
    }
    if (data.notice_flag === '5') {
        noticeTitle1 = "残業依頼";
        noticeTime = `${data.notice_out_time} まで 残業申請`;
        typeColor = ' type-color05';
    }
    if (data.notice_flag === '6') {
        noticeTitle1 = "有給依頼";
        typeColor = ' type-color06';
    }
    if (data.notice_flag === '7') {
        noticeTitle1 = "欠勤依頼";
        typeColor = ' type-color07';
    }
    if (data.notice_flag === '8') {
        noticeTitle1 = "その他依頼";
        typeColor = ' type-color08';
    }
    if (data.notice_flag === '11') {
        noticeTitle1 = "休暇依頼";
        typeColor = ' type-color11';
    }
    const toDate = new Date(data.to_date);
    const toDateView = `${toDate.getFullYear()}年${(toDate.getMonth()+1).toString().padStart(2, '0')}月${toDate.getDate().toString().padStart(2, '0')}日(${['日', '月', '火', '水', '木', '金', '土'][toDate.getDay()]})`;
    const endDate = data.end_date ? new Date(data.end_date) : null;
    const endDateView = endDate ? `${endDate.getFullYear()}年${(endDate.getMonth()+1).toString().padStart(2, '0')}月${endDate.getDate().toString().padStart(2, '0')}日(${['日', '月', '火', '水', '木', '金', '土'][endDate.getDay()]})` : "";
    return {
        statusText: statusText,
        icon: icon,
        color: color,
        noticeTitle1: noticeTitle1,
        noticeTime: noticeTime,
        typeColor: typeColor,
        toDateView: toDateView,
        endDateView: endDateView
    };
        
}

// 申請
const socketNotice = io.connect('wss://dakoku.work:3010/notice/data', {'force new connection' : true});
socketNotice.emit('notice_client_to_server', {system_id: sysId, user_id: userId});
socketNotice.on('notice_server_to_client', notice_data => {
    $('.notice-menu-area').html('');
    notice_data.forEach(element => {
        if (element.user_id != userId && element.high_user_id.indexOf(String(userId)) < 0) { return true; }

        const view = createNoticeData(element);

        const userDataView = element.user_id == userId ? `${view.noticeTitle1}を申請<br><br>` : `${element.user_name} から ${view.noticeTitle1}<br><br>`;
        const selected = noticeId == element.notice_id ? ' notice-select' : '';

        const noticeHtml = `<div id="${element.notice_id}" class="notice-menu alert ${view.color}${selected}"><div class="title">${view.icon}<div class="notice-title">${view.statusText}<span class="${view.color} notice-type${view.typeColor}">${view.noticeTitle1}</span></div></div><div class="sub-title"><i class="far fa-clock"></i> ${element.notice_datetime.slice(0, -3)}</div><div class="box-main">${userDataView}${view.toDateView}${view.endDateView} <i class="fas fa-angle-double-right"></i> ${view.noticeTitle1} ${view.noticeTime}</div></div>`;

        $('.notice-menu-area').append(noticeHtml);
    });
});

// 申請内容
const $messageTextElem = $('textarea[name="message_text"]');
let noticeData = {
    notice_id: noticeId
};
const nl2br = str => str.replace(/\n/g, '<br>');
const socketNoticeText = io.connect('wss://dakoku.work:3010/notice/text', {'force new connection' : true});
if (noticeId) {
    socketNoticeText.emit('notice_text_client_to_server', {system_id: sysId, user_id: userId, notice_id: noticeId});
    socketNoticeText.on('notice_text_server_to_client', notice_text => {
        $('#message_area').html('');

        const view = createNoticeData(notice_text);
        const permit = notice_text.permit_high_user_auth.indexOf(String(userId));
        const authBtn = permit < 0 ? '' : '<div class="inner auth-btn"><div id="ok_submit" class="notice-header-btn">承認</div><div id="ng_submit" class="notice-header-btn">N G</div></div>';
        const name = notice_text.to_user_id == userId ? "" : `<b>${notice_text.user_name}</b>より`;

        const titleHtml = `<div class="inner"><div class="notice-header-icon alert ${view.color}">${view.icon}</div><div class="notice-header-text-area"><div class="notice-header-title"><i class="fas fa-user"></i> ${name}<span class="title${view.typeColor} ${view.color}">${view.noticeTitle1}</span><span class="state">${view.statusText}</span></div><div class="notice-header-subtitle"><span class="date-title">希望日</span><span class="date">${view.toDateView}${view.endDateView}</span>${view.noticeTime}</div></div></div>`;

        $('#notice_title').html(titleHtml+authBtn);

        notice_text.massage.forEach(element => {
            const messageName = element.user_id == userId ? "" : `${element.user_name} より`;
            const nameClass = element.user_id == userId ? ' my' : '';
            const status = element.notice_status === '1' ? 'permit' : element.notice_status === '2' ? 'ng' : '';
            const message = nl2br(element.message_text);

            const messageHtml = `<div class="inner${nameClass}"><div class="notice-icon"></div><div class="notice-message-box"><div class="notice-user-name">${messageName}<span class="notice-date">${element.text_datetime.slice(0, -3)}</span></div><div class="notice-message ${status}">${message}</div></div><div class="message-img"></div></div>`;

            $('#message_area').append(messageHtml);
        });
        
        noticeData.notice_id = notice_text.notice_id;
        noticeData.notice_flag = notice_text.notice_flag;
        noticeData.notice_date = notice_text.notice_datetime.slice(0, 10);
        noticeData.from_user_id = notice_text.to_user_id;
        noticeData.end_date = notice_text.end_date;
        noticeData.to_date = notice_text.to_date;

        $messageTextElem.trigger("focus");
    });
}

// サイド 通知 選択
$(document).on('click', '.notice-menu', function() {
    $('.notice-menu').removeClass('notice-select');
    $(this).addClass('notice-select');
    const noticeId = $(this).attr('id');
    Cookies.set('notice_id', noticeId);
    location.reload();
});



// メッセージ　送信
const submitMessage = () => {
    return $.ajax({
        type: 'POST',
        url: '../../data/notice/submit_message',
        dataType: 'json',
        data: {
            notice_id: Number(noticeData.notice_id),
            message: noticeData.message,
            user_id: userId,
            flag: noticeData.flag,
            notice_flag: Number(noticeData.notice_flag),
            notice_date: noticeData.to_date,
            from_userId: Number(noticeData.from_user_id),
            notice_end_date: noticeData.end_date
        }
    })
}
const $basicSubmitElem = $('#basic_submit');
$basicSubmitElem.on('click', function() {
    noticeData.message = $messageTextElem.val();
    if (!noticeData.message) return;
    $basicSubmitElem.addClass('disabled');
    noticeData.flag = 0;
    $messageTextElem.val("");
    submitMessage().done(data => {
        socketNoticeText.emit('notice_text_client_to_server', {system_id: sysId, user_id: userId, notice_id: noticeData.notice_id});
        $basicSubmitElem.removeClass('disabled');
    }).fail(data => {
        console.log(data)
    })
});
const $okBtnElem = $('#ok_submit');
$(document).on('click', '#ok_submit', function() {
    $okBtnElem.addClass('disabled');
    noticeData.flag = 1;
    submitMessage().done(data => {
        socketNoticeText.emit('notice_text_client_to_server', {system_id: sysId, user_id: userId, notice_id: noticeData.notice_id});
        socketNotice.emit('notice_client_to_server', {system_id: sysId, user_id: userId});
        $okBtnElem.removeClass('disabled');
    })
});
const $ngBtnElem = $('#ng_submit');
$(document).on('click', '#ng_submit', function() {
    $ngBtnElem.addClass('disabled');
    noticeData.flag = 2;
    submitMessage().done(data => {
        socketNoticeText.emit('notice_text_client_to_server', {system_id: sysId, user_id: userId, notice_id: noticeData.notice_id});
        socketNotice.emit('notice_client_to_server', {system_id: sysId, user_id: userId});
        $ngBtnElem.removeClass('disabled');
    })
});





let mobile_menu_visible = 0;
$(document).on('click', '.navbar-toggler', function() {
    if (mobile_menu_visible === 1) {
        $('html').removeClass('nav-open');
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
})