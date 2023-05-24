<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- 使用 JS URL -->
<?php
$js_url = [
    'jquery' => 'js/libs/jquery.min.js',
    'js-cookie' => 'js/libs/js.cookie.js',
    'socket.io' => 'https://dakoku.work:3000/socket.io/socket.io.js',
    'socket.io2' => 'js/libs/socket.io.slim.js',
    'hotkeys' => 'js/libs/hotkeys.min.js',
    'izimodal' => 'js/libs/iziModal.min.js',
    'flatpickr' => 'js/libs/flatpickr.min.js',
    'flatpickr_weekSelect' => 'js/libs/flatpickr_weekSelect.js',
    'flatpickr_monthSelect' => 'js/libs/flatpickr_monthSelect.js',
    'siiimple-toast' => 'js/libs/siiimple-toast.min.js',
    'tabulator' => 'js/libs/tabulator.min.js',
    'jquery-ui' => 'js/libs/jquery-ui.min.js',
    'ajaxzip3' => 'https://ajaxzip3.github.io/ajaxzip3.js',
    'alertify' => 'js/libs/alertify.min.js',
    'axios' => 'js/libs/axios.min.js',
    'vue' => 'js/libs/vue.min.js'
];
?>

<!-- 共通　JS -->
<script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['jquery'] ?>"></script>

<!-- ログインページ　JS -->
<?php if ($page_id === 'login') : ?>
    <script>
        const gps_flag = <?= (int)$gps_flag->value ?>
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
<?php endif; ?>

<!-- 出退勤入力ページ　JS -->
<?php if ($page_id === 'gateway') : ?>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['socket.io2'] ?>"></script>
    <?php if (ENVIRONMENT === 'production') : ?>
    <script>
        const sysId = '<?= $system_id->value ?>';
    </script>
    <?php else: ?>
    <script>
        const sysId = "demo";
    </script>
    <?php endif; ?>
    <script>
        const socket = io.connect('https://dakoku.work:3000/v2/nowusers', {'force new connection' : true});
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['hotkeys'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['alertify'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['axios'] ?>"></script>
<?php endif; ?>

<!-- admin list_dayページ　JS -->
<?php if ($page_id === 'admin_list_day') : ?>
    <script>
        const authority = <?= (int)$this->session->authority ?>;
        const over_day = <?= (int)$over_day->value ?>;
        const defaultInHour = <?= (int)substr($edit_in_time->value, 0, 2) ?>;
        const defaultInMinute = <?= (int)substr($edit_in_time->value, 3, 2) ?>;
        const defaultOutHour = <?= (int)substr($edit_out_time->value, 0, 2) ?>;
        const defaultOutMinute = <?= (int)substr($edit_out_time->value, 3, 2) ?>;
        const defaultMinuteIncrement = <?= (int)$edit_min->value ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
<?php endif; ?>

<!-- admin list_monthページ　JS -->
<?php if ($page_id === 'admin_list_month') : ?>
    <script>
        const authority = <?= (int)$this->session->authority ?>;
        const end_day = <?= (int)$end_day->value ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr_monthSelect'] ?>"></script>
<?php endif; ?>

<!-- admin list_userページ　JS -->
<?php if ($page_id === 'admin_list_user') : ?>
    <script>
        const authority = <?= (int)$this->session->authority ?>;
        const end_day = <?= (int)$end_day->value ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr_monthSelect'] ?>"></script>
<?php endif; ?>

<!-- admin listsページ　JS -->
<?php if ($page_id === 'admin_lists') : ?>
    <script>
        const authority = <?= (int)$this->session->authority ?>;
        const over_day = <?= (int)$over_day->value ?>;
        var end_day = <?= (int)$end_day->value ?>;
    </script>
    <script>
        const defaultInHour = <?= (int)substr($edit_in_time->value, 0, 2) ?>;
        const defaultInMinute = <?= (int)substr($edit_in_time->value, 3, 2) ?>;
        const defaultOutHour = <?= (int)substr($edit_out_time->value, 0, 2) ?>;
        const defaultOutMinute = <?= (int)substr($edit_out_time->value, 3, 2) ?>;
        const defaultMinuteIncrement = <?= (int)$edit_min->value ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr_monthSelect'] ?>"></script>
<?php endif; ?>

<!-- admin usersページ　JS -->
<?php if ($page_id === 'admin_users') : ?>
    <script>
        const authority = <?= (int)$this->session->authority ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
<?php endif; ?>

<!-- admin user detailページ　JS -->
<?php if ($page_id === 'admin_user_detail') : ?>
    <script>
        const mypage_flag = <?= (int)$mypage_flag->value ?>;
        const id_size = <?= (int)$id_size->value ?>;
        const user_id_define = <?= (int)$user_id_define->value ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
    <!-- <script src="<?= $js_url['popper'] ?>"></script>
    <script src="<?= $js_url['tippy'] ?>"></script> -->
    <script src="<?= $js_url['ajaxzip3'] ?>"></script>
<?php endif; ?>

<!-- admin shiftページ　JS -->
<?php if ($page_id === 'admin_shift') : ?>
    <script>
        const authority = <?= (int)$this->session->authority ?>;
    </script>
    <script>
        var shift_view_flag = <?= (int)$shift_view_flag->value ?>;
        var shift_cal_first_day = <?= (int)$shift_cal_first_day->value ?>;
    </script>
    <script>
        const over_day = <?= (int)$over_day->value ?>;
    </script>
    <script>
        const defaultInHour = <?= (int)substr($edit_in_time->value, 0, 2) ?>;
        const defaultInMinute = <?= (int)substr($edit_in_time->value, 3, 2) ?>;
        const defaultOutHour = <?= (int)substr($edit_out_time->value, 0, 2) ?>;
        const defaultOutMinute = <?= (int)substr($edit_out_time->value, 3, 2) ?>;
        const defaultMinuteIncrement = <?= (int)$edit_min->value ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/fullcalendar/core/main.min.js"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/fullcalendar/core/ja.js"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/fullcalendar/daygrid/main.min.js"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/fullcalendar/interaction/main.min.js"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr_monthSelect'] ?>"></script>
<?php endif; ?>

<!-- admin payページ　JS -->
<?php if ($page_id === 'admin_pay') : ?>
    <script>
        const end_day = <?= (int)$end_day->value ?>;
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
<?php endif; ?>

<!-- admin confページ　JS -->
<?php if ($page_id === 'admin_conf') : ?>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['izimodal'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <!-- <script src="<?= $js_url['popper'] ?>"></script>
    <script src="<?= $js_url['tippy'] ?>"></script> -->
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['siiimple-toast'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['jquery-ui'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/libs/timepicki.js"></script>
<?php endif; ?>

<!-- admin toページ JS -->
<?php if ($page_id === 'admin_to'): ?>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
<?php endif; ?>

<!-- admin list_weeklyページ　JS -->
<?php if ($page_id === 'admin_list_weekly') : ?>
    <script>
        const firstDayOfWeek = <?= (int)$company_data['company_week_start']->value ?>
    </script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['js-cookie'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['tabulator'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr'] ?>"></script>
    <script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?><?= $js_url['flatpickr_weekSelect'] ?>"></script>
<?php endif; ?>

<!-- 各ページ js -->
<script src="<?= ENVIRONMENT === 'production' ? BASE_URI.'dist/' : 'dev/' ?>js/<?= $page_id ?>.min.js?<?= time() ?>"></script>
