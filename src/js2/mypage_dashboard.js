import io from 'socket.io-client';
import toast from 'siiimple-toast';
import iziModal from 'izimodal';
import flatpickr from 'flatpickr';
import { Japanese } from "flatpickr/dist/l10n/ja.js";
flatpickr.localize(Japanese);
import Swal from 'sweetalert2';
import Cookies from 'js-cookie';

import { renderClock } from './modules/clock';
import { viewUsersStatus } from './modules/users_status_view';
import { judgeInput } from './modules/judge_input';

// 申請表示
const socketNotice = io.connect('wss://dakoku.work:3010/notice/data', {'force new connection' : true});
socketNotice.emit('notice_client_to_server', {system_id: sysId, user_id: userId});
socketNotice.on('notice_server_to_client', notice_data => {
    const $noticeArea01elem = $('.notice_area01');
    const $noticeDashboardElem = $('.notice-dashboard');
    notice_data = notice_data ? notice_data : [];
    $noticeDashboardElem.hide();
    $noticeArea01elem.html('');
    let noticeNum = 0, notice = 0, permit = 0, ng = 0;
    notice_data.forEach(element => {
        if (element.user_id != userId && element.high_user_id.indexOf(String(userId)) < 0) { return true; }
        let statusText = "", icon = '', color = '', noticeTitle = "", noticeTime = "", typeColor = '', endDateW = "";
        if (element.notice_status == 0) {
            if (element.user_id == userId) {
                statusText = "承認依頼中";
                icon = '<i class="far fa-paper-plane"></i>';
                color = 'alert-warning';
            } else {
                statusText = "申請";
                icon = '<i class="far fa-bell"></i>';
                color = 'alert-info';
            }
            notice++;
        }
        if (element.notice_status == 1) {
            if (element.user_id == userId) {
                statusText = `${element.from_user_name}から<br>承認されました`;
                icon = '<i class="far fa-thumbs-up"></i>';
                color = 'alert-success';
            } else {
                statusText = `${element.from_user_name}が<br>承認しました`;
                icon = '<i class="far fa-thumbs-up""></i>';
                color = 'alert-success';
            }
            permit++;
        }
        if (element.notice_status == 2) {
            if (element.user_id == userId) {
                statusText = "申請NGです";
                icon = '<i class="fas fa-exclamation-circle"></i>';
                color = 'alert-danger';
            } else {
                statusText = "NG送信中";
                icon = '<i class="fas fa-exclamation-circle"></i>';
                color = 'alert-danger';
            }
            ng++;
        }

        switch (element.notice_flag) {
            case '1':
                noticeTitle = "修正依頼";
                noticeTime = `${element.before_in_time}〜${element.before_out_time}を ${element.notice_in_time}〜${element.notice_out_time} に時刻修正を申請`;
                typeColor = ' type-color01';
                break;
            case '2':
                noticeTitle = "削除依頼";
                noticeTime = `${element.before_in_time}〜${element.before_out_time}を 削除申請`;
                typeColor = ' type-color02';
            case '3':
                noticeTitle = "遅刻依頼";
                noticeTime = `${element.notice_in_time} 出勤に 遅刻申請`;
                typeColor = ' type-color03';
            case '4':
                noticeTitle = "早退依頼";
                noticeTime = `${element.notice_out_time} 退勤に 早退依頼`;
                typeColor = ' type-color04';
            case '5':
                noticeTitle = "残業依頼";
                noticeTime = `${element.notice_out_time} まで 残業依頼`;
                typeColor = ' type-color05';
            case '6':
                noticeTitle = "有給依頼";
                typeColor = ' type-color06';
            case '7':
                noticeTitle = "欠勤依頼";
                typeColor = ' type-color07';
            case '8':
                noticeTitle = "その他依頼";
                typeColor = ' type-color08';
            case '11':
                noticeTitle = "休暇依頼";
                typeColor = ' type-color11';
            default:
                break;
        }

        const userDataW = element.user_id == userId ? "" : `ID:${element.user_id} ${element.user_name}より<br>`;
        const date = new Date(element.to_date);
        const toDateW = `${date.getFullYear()}年${(date.getMonth()+1).toString().padStart(2, '0')}月${date.getDate().toString().padStart(2, '0')}日(${['日', '月', '火', '水', '木', '金', '土'][date.getDay()]})`;
        if (element.end_date) {
            const endDate = new Date(element.end_date);
            endDateW = ` から ${endDate.getFullYear()}年${(endDate.getMonth()+1).toString().padStart(2, '0')}月${endDate.getDate().toString().padStart(2, '0')}日(${['日', '月', '火', '水', '木', '金', '土'][endDate.getDay()]})`;
        }

        const noticeHtml = `<div id="${element.notice_id}" class="alert ${color}"><div class="alert-content"><div class="alert-icon">${icon}</div><div class="alert-title"><div class="title">${statusText}</div><div class="date">${element.notice_datetime.slice(0, -3)}</div></div><div class="notice-type ${color}${typeColor}">${noticeTitle}</div><div class="alert-text"><div class="box-main">${userDataW}${toDateW}${endDateW}  <i class="fas fa-angle-double-right"></i> ${noticeTitle}<br>${noticeTime}</div></div>`;

        $noticeArea01elem.append(noticeHtml);
        noticeNum++;
    });
    $('#notice_count_new, .badge-text1').hide();
    $('#notice_count_ng, .badge-text2').hide();
    if (noticeNum > 0) {
        $noticeDashboardElem.show();
        if (notice > 0) {
            $('#notice_count_new, .badge-text1').show();
            $('#notice_count_new').text(notice);
        }
        if (ng > 0) {
            $('#notice_count_ng, .badge-text2').show();
            $('#notice_count_ng').text(ng);
        }
    }
});

// 出退勤一覧　表示
const usersSocket = io.connect('wss://dakoku.work:3010/nowusers', {'force new connection' : true});
if (low_user === 1) {
    usersSocket.emit('system_id', sysId);
    usersSocket.on('nowusers_server_to_client', data => {
        let users = data ? data : [];
        users = users.filter(elem => low_users_list.indexOf(elem.user_id) >= 0 || elem.user_id == userId);
        viewUsersStatus(users, Number(mypage_status_view_flag));
    });
}

$('#modal1, #modal3').iziModal({
    headerColor: '#1591a2',
    focusInput: false,
});
$('#modal2').iziModal({
    headerColor: '#1591a2',
    focusInput: false,
    width: 750,
});

const getUserData = (userId) => {
    return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../../data/gateway/user',
        data: { user_id: userId }
    });
}

const getShiftData = (year, month) => {
    return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../data/admin_shift/table_shift_data',
        data: {
            year: year,
            month: month,
            user_id: userId,
            flag: 'cal'
        }
    });
}

const isPC = navigator.userAgent.match(/iPhone|Android.+Mobile/) ? false : true;
function getPosition() {
    return new Promise((resolve, reject) => 
        navigator.geolocation.getCurrentPosition(resolve, reject)
    );
}
let locationData = {info: '未取得', coords: {latitude: '', longitude: ''}};

(() => {
    renderClock();
    renderUser();
})();

let userInputConfirm;
function renderUser() {
    getUserData(userId).done(data => {
        const userName = data.user_name;
        $('#user_id').text(userId);
        $('#user_name').text(userName).css('color', '#1dbdd2');
        $('#user_group').text(`${data.group1_name} ${data.group2_name} ${data.group3_name}`);
    
        if (data.management_flag === 1) {
            $('#input_area').hide();
            $('#user_notice_area').hide();
            $('#mypage_menu_mystate').hide();
            $('#mypage_menu_shift').hide();
            return;
        }

        userInputConfirm = data.input_confirm_flag;
    
        $('#user_count').text(data.count);
        $('#user_time').text(data.time);
        $('.iziModal-header-title').text(userName);
    
        const inFlag = data.in_flag;
        const outFlag = data.out_flag;
        const restFlag = data.rest_flag;
        const autoRest = data.auto_rest;
    
        if (gps_flag === 1 || ( gps_flag === 2 && !isPC )) {
            getPosition().then(position => {
                locationData.state = true;
                locationData.info = '取得';
                locationData.coords = position.coords;
                judgeInput(inFlag, outFlag, restFlag, autoRest);
            }).catch(err => {
                locationData.state = false;
                locationData.info = err.code === 1 ? '拒否' : err.code === 2 ? '失敗' : 'タイムアウト';
                $('#input_message').html(`位置情報取得ができません。(エラー: ${locationData.info})<br>設定により位置情報取得が出退勤をおこなうのに必須となっております。<br>ブラウザの位置情報の取得を許可し再読み込みするか、管理者へ連絡して下さい。<br>ブラウザの位置情報管理については下記などをご参考にして下さい。<br><a href="https://support.google.com/chrome/answer/142065?hl=ja&co=GENIE.Platform%3DDesktop&oco=0">現在地情報を共有する - Google Chrome ヘルプ</a>`).addClass('danger').show();
            })
        } else {
            judgeInput(inFlag, outFlag, restFlag, autoRest);
        }
    
        if (mypage_shift_alert === 1 && data.shift_alert_flag === 0) {
            const now = new Date();
            const year = now.getFullYear();
            const month = now.getMonth()+1;
            const day = now.getDate();
            const lastDay = new Date(year, month, 0).getDate();
            getShiftData(year, month).done(data => {
                const shiftIn = data.filter(elem => elem.title !== '・未登録');
                if (shiftIn.length < lastDay) {
                    toast.alert('今月のシフトが登録されてません！', {position: 'top|right'});
                }
            });
            const shiftClosingDay = shift_closing_day === 0 ? lastDay : shift_closing_day;
            if (shiftClosingDay <= day) {
                const nextDate = new Date(year, month, day);
                const nextYear = nextDate.getFullYear();
                const nextMonth = nextDate.getMonth()+1;
                const nextLastDay = new Date(nextYear, nextMonth, 0).getDate();
                getShiftData(nextYear, nextMonth).done(data => {
                    const nextShiftIn = data.filter(elem => elem.title !== '・未登録');
                    if (nextShiftIn.length < nextLastDay) {
                        toast.alert('来月のシフト登録期限日もしくは過ぎてます！', {position: 'top|right'});
                    }
                })
            }
        }
    });
}


// ステータス表示
const getUserStatus = () => {
    return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../../data/gateway/status',
        data: { user_id: userId }
    });
}
$('#dashboard_status_btn').on('click', function() {
    getUserStatus().done(data => {
        $('.month_data').empty();
        let i = 0;
        data.forEach(statusData => {
            let userStatus = '', date = "";
            statusData.forEach(element => {
                const color = element.w === '0' || element.w === 7 ? 'red' : element.w === '6' ? 'blue' : '#3e3a39';
                const bgColor = element.today_flag === 1 ? ' style="background:rgba(120, 224, 255, .5)"' : '';
                let inTime = "", outTime = "";
                if (mypage_status_view_flag === 0) {
                    if (element.in_time) {
                        inTime = element.in_time;
                    }
                    if (!element.in_time && element.in_work_time) {
                        inTime = element.in_work_time;
                    }
                    if (element.out_time) {
                        outTime = element.out_time;
                    }
                    if (!element.out_time && element.out_work_time) {
                        outTime = element.out_work_time;
                    }
                    if (element.out_time === '未退勤' && element.out_work_time) {
                        outTime = element.out_work_time;
                    }
                }
                if (mypage_status_view_flag === 1) {
                    inTime = element.in_work_time;
                    outTime = element.out_work_time;
                }
                userStatus += `<tr${bgColor}><td>${element.day}</td><td style="color:${color}">${element.week}</td><td>${inTime}</td><td>${outTime}</td><td>${element.memo}</td><td class="mail-sys"><div class="to-mail-btn" data-date="${element.date}">作成</div></td></tr>`;
                date = ` ${element.year}年${element.month}月`;
            });
            $('#month_'+i).text(date);
            $('#month_data_'+i).append(userStatus);
            i++;
        });
    });
    $('#modal1').iziModal('open');
});

// 申請依頼
let noticeData = {};
const noticeCheck = () => {
    if (!noticeData.notice_text || !noticeData.notice_flag) {
        $('#time_edit_submit').addClass('disable');
        return;
    }
    if (noticeData.to_date && noticeData.notice_flag) {
        if (noticeData.notice_flag === '1') {
            if (noticeData.in_time === noticeData.notice_in_time && noticeData.out_time === noticeData.notice_out_time) {
                $('#time_edit_submit').addClass('disable');
                return;
            }
        }
    }
    $('#time_edit_submit').removeClass('disable');
    return;
}
const getUserWorkStatus = (dateStr) => {
    const date = dateStr.length > 10 ? dateStr.slice(0, 10) : dateStr;
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../../data/notice/user_work_status',
        data: {
            user_id: userId,
            date: date
        }
    }).done(data => {
        const shiftStatus = data.shift.status !== '' ? Number(data.shift.status) : '';
        const shiftStatusView = shiftStatus !== '' ? ['出勤', '公休', '有給'][shiftStatus] : '未定';
        $('#shiftStatus').text(shiftStatusView);
        const shiftInTime = data.shift.in_time !== '' ? data.shift.in_time.substr(0, 5) : null;
        const shiftOutTime = data.shift.out_time !== '' ? data.shift.out_time.substr(0, 5) : null;
        if (shiftStatus === 0 && shiftInTime && shiftOutTime) {
            $('.shift-time').show();
            $('#shift_in_time').text(shiftInTime);
            $('#shift_out_time').text(shiftOutTime);
        } else {
            $('.shift-time').hide();
        }
        const timeStatus = data.time.status !== '' ? data.time.status : '未出勤';
        const inTime = data.time.in_time !== '' ? data.time.in_time.substr(0, 5) : '';
        const outTime = data.time.out_time !== '' ? data.time.out_time.substr(0, 5) : '';
        $('#timeStatus').text(timeStatus);
        $('#in_time').text(inTime ? inTime : "--");
        $('#out_time').text(outTime ? outTime : "--");
        timePickerIn.setDate(inTime);
        timePickerOut.setDate(outTime);
        if (!inTime && !outTime) {
            $("button[data-id='2']").addClass('disabled');
        }
        noticeData.in_time = inTime;
        noticeData.out_time = outTime;
        noticeData.noticeHopeData = data;
        noticeData.notice_in_time = inTime;
        noticeData.notice_out_time = outTime;
    });
}
const renderNoticeDate = selectedDates => {
    noticeData.end_date = '';
    const selecedDate = new Date(selectedDates.length === 1 ? selectedDates : selectedDates[0]);
    $('#calendar_date').html(`${selecedDate.getFullYear()}年${selecedDate.getMonth()+1}月${selecedDate.getDate()}日(${['日', '月', '火', '水', '木', '金', '土'][selecedDate.getDay()]})`);
    const now = new Date();
    now.setHours(0);
    now.setMinutes(0);
    now.setSeconds(0);
    now.setMilliseconds(0);
    if (selecedDate.getTime() === now.getTime()) {
        $('#today_mark').addClass('today').text('本日');
        $('.past, .future').removeClass('disabled');
    } else {
        const diffDay = Math.ceil((selecedDate - now) / 86400000);
        let diffDayView;
        if (diffDay > 0) {
            diffDayView = diffDay.toString()+"日後";
            $('.past').addClass('disabled');
            $('.future').removeClass('disabled');
            if (noticeData.team === '0') {
                $('.time-status, .time-bloc').hide();
                $('.notice-btn').removeClass('notice-active');
                noticeData.notice_flag = '';
                noticeCheck();
            } 
        } else {
            diffDayView =  Math.abs(diffDay).toString()+"日前";
            $('.past').removeClass('disabled');
            $('.future').addClass('disabled');
            if (noticeData.team === '1') {
                $('.time-status, .time-bloc').hide();
                $('.notice-btn').removeClass('notice-active');
                noticeData.notice_flag = '';
                noticeCheck();
            } 
        }
        $('#today_mark').removeClass('today').text(diffDayView);
    }
    const wareki = new Intl.DateTimeFormat('ja-JP-u-ca-japanese', {era: 'long', year: 'numeric'}).format(selecedDate);
    $('#date-area-wareki').text(wareki);
    if (selectedDates.length === 2) {
        const selecedDateNext = new Date(selectedDates[1]);
        if (selecedDate.getTime() === selecedDateNext.getTime()) {
            $('.date-sub').remove();
        } else if ($('.status-block').find('.date-sub').length === 0) {
            const selectDiff =  Math.ceil((selecedDateNext - selecedDate) / 86400000)+1;
            $('.date-area').after(`<div class="date-sub">${selecedDateNext.getFullYear()}年${selecedDateNext.getMonth()+1}月${selecedDateNext.getDate()}日(${['日', '月', '火', '水', '木', '金', '土'][selecedDateNext.getDay()]})までの${selectDiff}日間</div>`);
            noticeData.end_date = `${selecedDateNext.getFullYear()}-${selecedDateNext.getMonth()+1}-${selecedDateNext.getDate()}`;
        }
    } else {
        $('.date-sub').remove();
    }
    noticeData.to_date = `${selecedDate.getFullYear()}-${selecedDate.getMonth()+1}-${selecedDate.getDate()}`;
}
const calendar1 = flatpickr('#modal_date', {
    inline: true,
    defaultDate: 'today',
    onReady: (selectedDates, dateStr) => {
        renderNoticeDate(selectedDates);
        getUserWorkStatus(dateStr);
        noticeCheck();
    },
    onChange: (selectedDates, dateStr) => {
        renderNoticeDate(selectedDates);
        getUserWorkStatus(dateStr);
        noticeCheck();
    }
});
const timePickerIn = flatpickr('#picker_in_time', {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    defaultHour: defaultInHour,
    defaultMinute: defaultInMinute,
    minuteIncrement: defaultMinuteIncrement,
    onChange: (selectedDates, dateStr) => {
        noticeData.notice_in_time = dateStr;
        noticeCheck();
    }
});
const timePickerOut = flatpickr('#picker_out_time', {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    defaultHour: defaultOutHour,
    defaultMinute: defaultOutMinute,
    minuteIncrement: defaultMinuteIncrement,
    onChange: (selectedDates, dateStr) => {
        noticeData.notice_out_time = dateStr;
        noticeCheck();
    }
});
$('#dashboard_notice_btn').on('click', function() {
    noticeData = {};
    $('.time-status, .time-bloc').hide();
    $('.notice-btn').removeClass('notice-active');
    $('#modal2').iziModal('open');
});
$('#today_mark').on('click', function() {
    const now = new Date();
    now.setHours(0);
    now.setMinutes(0);
    now.setSeconds(0);
    now.setMilliseconds(0);
    calendar1.setDate(now);
    renderNoticeDate([now]);
    getUserWorkStatus(`${now.getFullYear()}-${now.getMonth()+1}-${now.getDate()}`);
});
$('#picker_del_in_time').on('click', function() {
    timePickerIn.clear();
    noticeData.notice_in_time = '';
    noticeCheck();
});
$('#picker_del_out_time').on('click', function() {
    timePickerOut.clear();
    noticeData.notice_out_time = '';
    noticeCheck();
});
$('.notice-btn').on('click', function() {
    const $timeEditAreaElem = $('.time-edit-area');
    const $inTimeInputAreaElem = $('#in_time_input_area');
    const $outTimeInputAreaElem = $('#out_time_input_area');
    const $noticeCommentElem = $('.notice-comment-area.time-area');
    const $noticeComment = $('#noticeComment');
    $('.time-status, .time-bloc').show();

    noticeData.notice_in_time = '';
    noticeData.notice_out_time = '';

    noticeData.title = $(this).text();

    $('.notice-btn').removeClass('notice-active');
    $(this).addClass('notice-active');
    const noticeFlag = $(this).attr('data-id');
    const calendarTeam = $(this).attr('data-term');
    $('#time_edit_submit').text($(this).text()+"を送信する").show();
    switch (noticeFlag) {
        case '1':
            $timeEditAreaElem.show();
            $inTimeInputAreaElem.show();
            $outTimeInputAreaElem.show();
            $noticeComment.empty();
            break;
        case '2':
            $timeEditAreaElem.hide();
            $noticeComment.text('削除依頼する');
            break;
        case '3':
            $timeEditAreaElem.show();
            $outTimeInputAreaElem.hide();
            $inTimeInputAreaElem.show();
            $noticeComment.text('遅刻依頼する');
            break;
        case '4':
            $timeEditAreaElem.show();
            $inTimeInputAreaElem.hide();
            $outTimeInputAreaElem.show();
            $noticeComment.text('早退依頼する');
            break;
        case '5':
            $timeEditAreaElem.show();
            $inTimeInputAreaElem.hide();
            $outTimeInputAreaElem.show();
            $noticeComment.text('残業依頼する');
            break;
        case '7':
            $timeEditAreaElem.hide();
            $noticeCommentElem.show();
            $noticeComment.text('欠勤依頼する');
            break;
        case '11':
            $timeEditAreaElem.hide();
            $noticeCommentElem.show();
            $noticeComment.text('休暇依頼する');
            break;
        case '8':
            $timeEditAreaElem.hide();
            $noticeCommentElem.show();
            $noticeComment.text('その他依頼をする');
            break;
        case '6':
            $timeEditAreaElem.hide();
            $noticeCommentElem.show();
            $noticeComment.text('有給依頼する');
            break;
        default:
            break;
    }
    if (calendarTeam === '9') {
        calendar1.set({mode: 'range', maxDate: null, minDate: null});
    } else {
        calendar1.setDate(calendar1.selectedDates[0]);
        calendar1.set({mode: 'single', maxDate: null, minDate: null});
    }
    renderNoticeDate(calendar1.selectedDates);
    const date = new Date(calendar1.selectedDates[0]);
    date.setHours(0);
    date.setMinutes(0);
    date.setSeconds(0);
    date.setMilliseconds(0);
    getUserWorkStatus(`${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()}`);
    noticeData.notice_flag = noticeFlag;
    noticeData.team = calendarTeam;
    noticeCheck();
});
$('#memo').on('keyup', function() {
    noticeData.notice_text = $(this).val();
    noticeCheck();
});
$('#time_edit_submit').on('click', function() {
    $(this).addClass('disable');
    $('.time-status, .time-bloc').hide();
    $('.notice-btn').removeClass('notice-active');
    $('#memo').val('');
    $.ajax({
        type: 'POST',
        url: '../../data/notice/new',
        data: {
            to_user_id: userId,
            to_date: noticeData.to_date,
            notice_flag: noticeData.notice_flag,
            notice_in_time: noticeData.notice_in_time,
            notice_out_time: noticeData.notice_out_time,
            notice_text: noticeData.notice_text,
            user_name: $('#user_name').text(),
            end_date: noticeData.end_date,
            noticeHopeData: noticeData.noticeHopeData
        }
    }).done(data => {
        $('#modal2').iziModal('close', { transition: 'bounceOutUp' });
        toast.message(`${noticeData.title}を送信しました`, {position: 'top|right'});
        noticeData = {};
        socketNotice.emit('notice_client_to_server', {system_id: sysId, user_id: userId});
    })
});
$(document).on('click', '.to-mail-btn', function() {
    const dateStr = $(this).attr('data-date');
    const date = new Date(dateStr);
    date.setHours(0);
    date.setMinutes(0);
    date.setSeconds(0);
    date.setMilliseconds(0);
    noticeData = {};
    $('.time-status, .time-bloc').hide();
    $('.notice-btn').removeClass('notice-active');
    calendar1.setDate(dateStr);
    renderNoticeDate([date]);
    getUserWorkStatus(dateStr);
    noticeCheck();
    $('#modal2').iziModal('open');
});

// シフト表示
const getUserShiftData = () => {
    return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../../data/gateway/shift',
        data: { user_id: userId }
    });
}
$('#dashboard_shift_btn').on('click', function() {
    getUserShiftData().done(data => {
        $('.month_data').empty();
        let i = 0;
        data.forEach(shiftData => {
            let userShift = '', date = "";
            shiftData.forEach(element => {
                const color = element.w === '0' || element.w === 7 ? 'red' : element.w === '6' ? 'blue' : '#3e3a39';
                const bgColor = element.today_flag === 1 ? ' style="background:rgba(120, 224, 255, .5)"' : '';
                userShift += `<tr${bgColor}><td>${element.day}</td><td style="color:${color}">${element.week}</td><td>${element.status}</td><td>${element.in_time}</td><td>${element.out_time}</td></tr>`;
                date = `${element.year}年${element.month}月`;
            });
            $('#shift_month_'+i).text(date);
            $('#shift_month_data_'+i).append(userShift);
            i++;
        });
    });
    $('#modal3').iziModal('open');
});

// モーダル　タブ操作
$(document).on('click', '.tabs li', function() {
    const index = $(this).parent().parent().find('.tabs li').index(this);
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    $(this).parent().parent().find('.tab-content').removeClass('show').eq(index).addClass('show');
});

// 出勤
$('.input-btn').on('click', function() {
    $('.input-btn').addClass('disable');
    const flag = $(this).attr('id');
    const statusText = $(this).text();
    function insertData(flag) {
        let inputFlag;
        switch (flag) {
            case 'input_btn':
                inputFlag = 'in';
                break;
            case 'output_btn':
                inputFlag = 'out';
                break;
            case 'nonstop_in':
                inputFlag = 'nonstop_in';
                break;
            case 'nonstop_out':
                inputFlag = 'nonstop_out';
                break;
            default:
                break;
        }
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '../../data/gateway/insert',
            data: {
                flag: inputFlag,
                user_id: userId,
                user_name: $('#user_name').text(),
                latitude: locationData.coords.latitude,
                longitude: locationData.coords.longitude,
                info: locationData.info
            }
        }).done(data => {
            const title = inputFlag === 'in' ? "おはようございます" : "おつかれさまでした";
            toast.message(`${title}<br>${data.message}`, {position: 'top|right'});
            usersSocket.emit('system_id', sysId);
            setTimeout(() => {
                renderUser();
            }, 5000);
        })
    }
    function insertRestData(flag) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '../../data/gateway/insert_rest',
            data: {
                flag: flag,
                user_id: userId,
                user_name: $('#user_name').text()
            }
        }).done(data => {
            const title = flag === 'in' ? "休憩を開始します" : "休憩を終了します";
            toast.message(`${title}<br>${data.message}`, {position: 'top|right'});
            setTimeout(() => {
                renderUser();
            }, 3000);
        }).fail(error => {
            console.log(error);
        })
    }
    function gotoInsert(flag) {
        if (flag === 'input_btn' || flag === 'output_btn' || flag === 'nonstop_in' || flag === 'nonstop_out') {
            insertData(flag);
        }
        if (flag === 'rest_in_btn' || flag === 'rest_out_btn') {
            insertRestData(flag === 'rest_in_btn' ? 'in' : 'out');
        }
    }
    function confirmCheck(flag) {
        if (userInputConfirm === 0 && inputConfirmFlag === 1) {
            Swal.fire({
                title: `${statusText}をします`,
                text: `「はい」を選択しないと${statusText}いたしません！`,
                showCancelButton: true,
                confirmButtonColor: '#1b97a8',
                cancelButtonColor: '#b7b7b7',
                confirmButtonText: 'はい',
                cancelButtonText: 'キャンセル'
            }).then((result) => {
                if (result.isConfirmed) {
                    gotoInsert(flag)
                } else {
                    renderUser();
                }
            })
        } else {
            gotoInsert(flag)
        }
    }
    if (gps_flag === 1 || ( gps_flag === 2 && !isPC )) {
        getPosition().then(position => {
            locationData.state = true;
            locationData.info = '取得';
            locationData.coords = position.coords;
            confirmCheck(flag);
        }).catch(err => {
            locationData.state = false;
            locationData.info = err.code === 1 ? '拒否' : err.code === 2 ? '失敗' : 'タイムアウト';
            $('#input_message').html(`位置情報取得ができません。(エラー: ${locationData.info})<br>設定により位置情報取得が出退勤をおこなうのに必須となっております。<br>ブラウザの位置情報の取得を許可し再読み込みするか、管理者へ連絡して下さい。<br>ブラウザの位置情報管理については下記などをご参考にして下さい。<br><a href="https://support.google.com/chrome/answer/142065?hl=ja&co=GENIE.Platform%3DDesktop&oco=0">現在地情報を共有する - Google Chrome ヘルプ</a>`).addClass('danger').show();
        })
    } else {
        confirmCheck(flag);
    }
});

// 通知クリック
$(document).on('click', '.alert', function() {
    Cookies.set('notice_id', $(this).attr('id'));
    location.href = '/mypage_notice';
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