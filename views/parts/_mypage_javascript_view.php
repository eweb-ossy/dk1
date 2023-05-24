<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- 使用 JS URL -->
<?php
$js_url = [
  'jquery' => 'js/libs/jquery.min.js',
  'socket.io' => 'https://dakoku.work:3000/socket.io/socket.io.js',
  'socket.io2' => 'js/libs/socket.io.slim.js',
  'siiimple-toast' => 'js/libs/siiimple-toast.min.js',
  'js-cookie' => 'js/libs/js.cookie.js',
  'tabulator' => 'js/libs/tabulator.min.js',
  'izimodal' => 'js/libs/iziModal.min.js',
  'flatpickr' => 'js/libs/flatpickr.min.js',
  'flatpickr_weekSelect' => 'js/libs/flatpickr_weekSelect.js',
  'flatpickr_monthSelect' => 'js/libs/flatpickr_monthSelect.js',
];
?>

<!-- 共通 JS -->
<script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['jquery'] ?>"></script>
<script>
  var userId = <?= (int)$user_id ?>;
</script>
<script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['socket.io2'] ?>"></script>
<?php if (ENVIRONMENT === 'production') : ?>
  <script>
    const sysId = <?= '"'.$system_id->value.'"' ?>;
  </script>
<?php else: ?>
  <script>
    const sysId = "demo";
  </script>
<?php endif; ?>
<script>
  const socket = io.connect('https://dakoku.work:3000/v2/notice/get_data', {'force new connection' : true});
</script>
<script>
const socket2 = io.connect('https://dakoku.work:3000/v2/notice/get_text', {'force new connection' : true});
</script>
<!-- <script>
  const socket3 = io.connect('https://dakoku.work:3000/v2/notice/get_text', {'force new connection' : true});
</script> -->


<!-- ダッシュボード　dashboard JS -->
<?php if ($page_id === 'mypage_dashboard') : ?>
  <script>
    const gateway_mail_flag = <?= (int)$gateway_mail_flag->value ?>;
    const gps_flag = <?= (int)$gps_flag->value ?>;
    const agent = '<?= $agent ?>';
    const defaultInHour = <?= (int)substr($edit_in_time->value, 0, 2) ?>;
    const defaultInMinute = <?= (int)substr($edit_in_time->value, 3, 2) ?>;
    const defaultOutHour = <?= (int)substr($edit_out_time->value, 0, 2) ?>;
    const defaultOutMinute = <?= (int)substr($edit_out_time->value, 3, 2) ?>;
    const defaultMinuteIncrement = <?= (int)$edit_min->value ?>;
    const mypage_shift_alert = <?= $mypage_shift_alert->value ?>;
    var shift_closing_day = <?= (int)$shift_closing_day->value ?>;
  </script>
  <?php if ($low_user === 1): ?>
    <script>
      const socket3 = io.connect('https://dakoku.work:3000/v2/nowusers', {'force new connection' : true});
      const low_user = 1;
      var low_users_list = <?= $low_users_list ?>;
    </script>
  <?php else: ?>
    <script>
      const low_user = 0;
    </script>
  <?php endif; ?>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
<?php endif; ?>

<!-- マイ勤務状況 mystate JS -->
<?php if ($page_id === 'mypage_mystate') : ?>
  <script>
    const mypage_end_day = <?= (int)$mypage_end_day->value ?>;
    const over_day = <?= (int)$over_day->value ?>;
    const defaultInHour = <?= (int)substr($edit_in_time->value, 0, 2) ?>;
    const defaultInMinute = <?= (int)substr($edit_in_time->value, 3, 2) ?>;
    const defaultOutHour = <?= (int)substr($edit_out_time->value, 0, 2) ?>;
    const defaultOutMinute = <?= (int)substr($edit_out_time->value, 3, 2) ?>;
    const defaultMinuteIncrement = <?= (int)$edit_min->value ?>;
    const mypage_self_edit_flag = <?= (int)$mypage_self_edit_flag->value ?>;
    const mypage_self = <?= $mypage_self ?>;
  </script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr_monthSelect'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
<?php endif; ?>

<!-- 従業員 勤務状況（日別） state JS -->
<?php if ($page_id === 'mypage_state') : ?>
  <script>
    var edit_flag = <?= (int)$mypage_user_edit_flag->value ?>;
  </script>
  <script>
    const over_day = <?= (int)$over_day->value ?>;
    const defaultInHour = <?= (int)substr($edit_in_time->value, 0, 2) ?>;
    const defaultInMinute = <?= (int)substr($edit_in_time->value, 3, 2) ?>;
    const defaultOutHour = <?= (int)substr($edit_out_time->value, 0, 2) ?>;
    const defaultOutMinute = <?= (int)substr($edit_out_time->value, 3, 2) ?>;
    const defaultMinuteIncrement = <?= (int)$edit_min->value ?>;
  </script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
<?php endif; ?>

<!-- 従業員 勤務状況（集計） statelist JS -->
<?php if ($page_id === 'mypage_statelist') : ?>
  <script>
    const mypage_end_day = <?= (int)$mypage_end_day->value ?>;
  </script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr_monthSelect'] ?>"></script>
<?php endif; ?>

<!-- 従業員 勤務状況（集計） list JS -->
<?php if ($page_id === 'mypage_list') : ?>
  <script>
    var edit_flag = <?= (int)$mypage_user_edit_flag->value ?>;
  </script>
  <script>
    const mypage_end_day = <?= (int)$mypage_end_day->value ?>;
    const over_day = <?= (int)$over_day->value ?>;
    const defaultInHour = <?= (int)substr($edit_in_time->value, 0, 2) ?>;
    const defaultInMinute = <?= (int)substr($edit_in_time->value, 3, 2) ?>;
    const defaultOutHour = <?= (int)substr($edit_out_time->value, 0, 2) ?>;
    const defaultOutMinute = <?= (int)substr($edit_out_time->value, 3, 2) ?>;
    const defaultMinuteIncrement = <?= (int)$edit_min->value ?>;
  </script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr_monthSelect'] ?>"></script>
<?php endif; ?>

<!-- 申告 apply JS -->
<?php if ($page_id === 'mypage_apply') : ?>
  <script>
    var edit_min = <?= (int)$edit_min->value ?>;
  </script>
  <script>
    var user_name = "<?= $user_name ?>";
  </script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/timepicki.js"></script>
<?php endif; ?>

<!-- 通知　notice JS -->
<?php if ($page_id === 'mypage_notice') : ?>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
<?php endif; ?>

<!-- シフト　shift JS -->
<?php if ($page_id === 'mypage_shift') : ?>
  <script>
    const mypage_end_day = <?= (int)$mypage_end_day->value ?>;
    const shift_first_hour = <?= (int)$shift_first_hour->value ?>;
    const shift_end_hour = <?= (int)$shift_end_hour->value ?>;
    const shift_input_hour = <?= (int)$shift_input_hour->value ?>;
  </script>
  <script>
    var shift_cal_first_day = <?= (int)$shift_cal_first_day->value ?>;
  </script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/fullcalendar/core/main.min.js"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/fullcalendar/core/ja.js"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/fullcalendar/daygrid/main.min.js"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/fullcalendar/interaction/main.min.js"></script>
  <script type="text/javascript" src="https://uicdn.toast.com/tui.code-snippet/v1.5.0/tui-code-snippet.min.js"></script>
  <script type="text/javascript" src="https://uicdn.toast.com/tui.dom/v3.0.0/tui-dom.js"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/tui-time-picker.min.js"></script>
<?php endif; ?>

<!-- プロフィール profile JS -->
<?php if ($page_id === 'mypage_profile') : ?>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
  <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
<?php endif; ?>

<!-- 各ページ JS -->
<script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/<?= $page_id ?>.min.js?<?= time() ?>"></script>
