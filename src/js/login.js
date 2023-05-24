(function() {
  Cookies.remove('confTab');
  Cookies.remove('listMonth');
  Cookies.remove('listUser');
  Cookies.remove('listUserDiff');
  Cookies.remove('shiftMonth');
  Cookies.remove('shiftMonthDiff');
  Cookies.remove('shiftUserId');
  Cookies.remove('toListsMonth');
  Cookies.remove('toListsMonthDiff');
  Cookies.remove('toListsuserID');
  Cookies.remove('userDetailUserId');
  Cookies.remove('userDetailTabKey');
  Cookies.remove('notice_id');
  Cookies.remove('payMonth');
  Cookies.remove('payMonthDiff');
  Cookies.remove('mypageStateList');
  Cookies.remove('mypageStateListDiff');
  Cookies.remove('MypagetoListsMonth');
  Cookies.remove('MypagetoListsMonthDiff');
  Cookies.remove('MypagetoListsuserID');
  Cookies.remove('listUserFilters');

  // var agent;

  var model = {
    // getAgent: function() {
    //   var ua = navigator.userAgent.toLowerCase();
    //   var agent;
    //   if (ua.indexOf('iphone') > 0) {
    //     agent = 'iphone';
    //   } else if (ua.indexOf('ipod') > 0) {
    //     agent = 'ipod';
    //   } else if (ua.indexOf('android') > 0 && ua.indexOf('mobile') > 0) {
    //     agent = 'android';
    //   } else if (ua.indexOf('ipad') > 0) {
    //     agent = 'ipad';
    //   } else if (ua.indexOf('android') > 0) {
    //     agent = 'an';
    //   } else {
    //     agent = 'pc';
    //   }
    //   return agent;
    // },
    // getGps: function() {
    //   if (gps_flag === 0) {
    //     $('input[name="agent"]').val(model.getAgent());
    //     $('#login_id, #password, input, button').prop('disabled', false);
    //     $('.system-message').text('ログインIDとパスワードを入力してください。');
    //     return;
    //   }
    //   if (gps_flag === 2 && agent === 'pc') {
    //     $('input[name="agent"]').val(model.getAgent());
    //     $('#login_id, #password, input, button').prop('disabled', false);
    //     $('.system-message').text('ログインIDとパスワードを入力してください。');
    //     return;
    //   }
    //
    //   function success(position) {
    //     $('input[name="latitude"]').val(position.coords.latitude);
    //     $('input[name="longitude"]').val(position.coords.longitude);
    //     $('input[name="gps_info"]').val('取得');
    //     $('input[name="agent"]').val(agent);
    //     $('#login_id, #password, input, button').prop('disabled', false);
    //     $('.system-message').text('ログインIDとパスワードを入力してください。');
    //     return;
    //   }
    //
    //   function error(e) {
    //     var info = '不明エラー';
    //     switch (e.code) {
    //       case 1:
    //         info = '拒否';
    //         break;
    //       case 2:
    //         info = '失敗';
    //         break;
    //       case 3:
    //         info = 'タイムアウト';
    //         break;
    //     }
    //     $('.error-message').text('位置情報取得エラー ' + info);
    //   }
    //   if (navigator.geolocation) {
    //     navigator.geolocation.getCurrentPosition(success, error, {
    //       enableHighAccuracy: true
    //     });
    //   } else {
    //     $('.error-message').text('位置情報取得エラー 不明');
    //   }
    // }
  }

  $(function() {
    $('.system-message').text('Loading...');
    // agent = model.getAgent();
    // model.getGps();
    $('#login_id, #password, input, button').prop('disabled', false);
    $('.system-message').text('ログインIDとパスワードを入力してください。');
    $('#login_id').focus();
  });

}());
