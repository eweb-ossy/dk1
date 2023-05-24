import io from 'socket.io-client';
import toast from 'siiimple-toast';
import iziModal from 'izimodal';
import flatpickr from 'flatpickr';
import { Japanese } from "flatpickr/dist/l10n/ja.js";
flatpickr.localize(Japanese);

import { renderClock } from './modules/clock';
import { viewUsersStatus } from './modules/users_status_view';
import { judgeInput } from './modules/judge_input';

console.log(config.system_id);
console.log(config.gateway_status_view_flag);

// 出退勤一覧　表示
const usersSocket = io.connect('wss://dakoku.work:3010/nowusers', {'force new connection' : true});
usersSocket.emit('system_id', 'esna'); // 注意！
usersSocket.on('nowusers_server_to_client', data => {
    viewUsersStatus(data, Number(config.gateway_status_view_flag))
});

// 時計
renderClock();

// メッセージ
const showMessage = () => {
    $('#input_area').addClass('display-no');
    $('#message_area').removeClass('display-no');
}
const hideMessage = () => {
    $('#input_area').removeClass('display-no');
    $('#message_area').addClass('display-no');
}
if (config.mypage_flag === '1') {
    showMessage();
} else {
    hideMessage();
}

// 
const renderUser = (inputKey) => {
    if (inputKey.length > 0) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '../../data/gateway/user',
            data: { user_id: inputKey }
        }).done(data => {
            if (data.management_flag !== 1 && data.user_name) {
                $('#user_name').text(data.user_name).css('color', '#1dbdd2');
                $('#group1').text(data.group1_name);
                $('#group2').text(data.group2_name);
                $('#group3').text(data.group3_name);
                $('#count').text(data.count);
                $('#time').text(data.time);
                $('.header-btn').removeClass('disable');
                hideMessage();

                judgeInput(data.in_flag, data.out_flag, data.rest_flag, data.auto_rest);
                clearUser(10000);
            } else {
                $('#user_name').text('未登録').css('color', '#e65d5d');
                clearUser(5000);
            }
        })
    }
}
let setTimer;
const clearUser = (timer) => {
    clearTimeout(setTimer);
    setTimer = setTimeout(() => {
        inputKey = "";
        $('#user_id').text('　');
        $('.user_data').text('　');
        $('.header-btn').addClass('disable');
        $('#input_btn').addClass('disable');
        $('#output_btn').addClass('disable');
        $('.rest-bloc').addClass('disable');
        if (config.mypage_flag === '1') {
            showMessage();
        }
    }, timer);
}

// user id 入力操作
const $userIdElem = $('#user_id');
let inputKey = "";
$('.num-btn').on('click', function() {
    const num = $(this).attr('id').slice(-1);
    inputKey = inputKey.length < Number(config.id_size) ? inputKey += num : inputKey;
    $userIdElem.text(inputKey);
});
$('#num_clear').on('click', function() {
    clearUser();
});
$('#submit_userid').on('click', function() {
    renderUser(inputKey);
});
document.addEventListener('keydown', event => {
    const isNumberString = n => typeof n === "string" && n !== "" &&  !isNaN( n );
    const keyName = event.key;
    if (isNumberString(keyName)) {
        inputKey = inputKey.length < Number(config.id_size) ? inputKey += keyName : inputKey;
        $userIdElem.text(inputKey);
    }
    if (keyName === 'Clear') {
        clearUser();
    }
    if (keyName === 'Backspace') {
        inputKey = inputKey.slice(0, -1);
        if (inputKey.length > 0) {
            $userIdElem.text(inputKey);
        } else {
            clearUser();
        }
    }
    if (keyName === 'Enter') {
        renderUser(inputKey);
    }
});