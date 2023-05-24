<?php
defined('BASEPATH') or exit('No direct script access allowed');
// Mypage 共通パーツ　サイドバー
?>
<div class="sidebar" data-color="dakoku" data-background-color="white">
    <!--
    Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"
    Tip 2: you can also add an image using data-image tag
    -->
    <div class="logo" style="text-align:center;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 146 36"><style>.st0{fill:#221815}.st1{fill:#fff}</style><path class="st0" d="M144 23.3c.2-3.9-1-7.7-3.4-10.9-2.7-3.5-6.6-5.8-11-6.4-5-.7-10 1-13.7 4.5-.2.2-.2.6 0 .8.2.2.6.2.8 0 3.4-3.3 8.1-4.8 12.8-4.2 4.1.5 7.8 2.7 10.3 5.9 2.2 2.9 3.3 6.5 3.2 10.1-.6.2-1.1.8-1.1 1.4 0 .8.7 1.5 1.5 1.5s1.5-.7 1.5-1.5c0-.5-.4-1-.9-1.2zM4.3 13.5c0-1 0-1.4-.1-2.1h3.3c-.1.6-.1 1-.1 2.1v1.4H9v-2.5c.5.1 1.4.1 2.6.1h6c1.1 0 2 0 2.5-.1v3.1c-.7-.1-1.6-.1-2.6-.1h-.1v11.9c0 1.2-.3 1.9-1 2.3-.5.3-1.6.5-3.2.5-.6 0-1.4 0-2.4-.1-.1-1.3-.2-2.1-.6-3.1 1.6.2 2.4.2 3 .2.8 0 1-.1 1-.5V15.3H9.9v2.4c-.5 0-1-.1-1.7-.1h-.8V20c1.2-.2 1.6-.3 2.6-.6v2.8c-1.3.3-1.8.4-2.6.6v4.8c0 1-.3 1.7-.9 2-.5.4-1.6.5-2.8.5-.4 0-1 0-1.6-.1-.1-1.3-.3-1.9-.7-3 .9.1 1.6.2 2.1.2s.7-.2.7-.6v-3.2c-1.6.3-2.2.4-2.5.4l-.4-3.1h.4c.5 0 1.3-.1 2.6-.2v-2.8h-1c-.7 0-1.2 0-1.7.1v-3c.5.1.9.1 1.5.1h1.2v-1.4zM31.6 18c-.1.1-.1.2-.3.4-1 1.3-2 2.5-3 3.5-1.8 1.7-3.3 2.8-5.4 3.8-.6-1.1-1-1.7-1.9-2.5 1.7-.7 2.4-1.1 3.6-1.9-.9-.9-1.7-1.6-3.2-2.6l1.9-2.2c.5.3.6.5 1 .7.4-.5.5-.8.8-1.3h-1.9c-.8 0-1.2 0-1.7.1v-3c.7.1 1.1.1 2.1.1h2.1v-.3c0-.6 0-1-.1-1.4h3.2c-.1.4-.1.8-.1 1.4v.3h1.1c1.1 0 1.6 0 2.3-.1v2.9c-.6-.1-1.1-.1-1.9-.1h-1.9c-.6 1.1-1.3 2.1-2 2.9.2.2.2.2.6.5.7-.7 1.5-1.6 1.8-2.2.1-.1.2-.4.4-.8l2.6 1.5-.1.3zm1.1 4.4c-.4.5-.4.5-1.3 1.5-.4.4-.7.8-1.3 1.4.9.9 1.9 1.6 3 2.2-.9.9-1.3 1.6-1.9 2.6-1.4-1-2.3-1.7-3.3-2.8-1.6 1.3-2.9 2.2-4.7 3.1-.7-1.1-1.1-1.7-2.1-2.5 3.9-1.6 6.9-3.9 9.3-7.4l2.2 1.6v-7c0-.9 0-1.4-.1-2h3c-.1.6-.1 1-.1 2v8.3c0 1.1 0 1.5.1 2h-3c.1-.5.1-1 .1-2v-1zm7-10.8c-.1.6-.1 1.1-.1 2.4v13.3c0 2.3-.7 2.9-3.5 2.9-.4 0-.8 0-2-.1-.1-1.1-.3-1.8-.8-2.9 1.1.1 1.7.2 2.4.2s.9-.1.9-.7V14.1c0-1.3 0-1.8-.1-2.5h3.2zm12-1c-.1 1-.2 2-.2 3.5v6.6l2.6-2.6c1.1-1.1 1.6-1.7 2-2.4h6.2c-.9.8-1.2 1.1-2.5 2.3l-3.4 3.3 3.9 5.7c1.4 2.1 1.5 2.2 2.2 3h-6c-.4-1-.9-2-1.5-3.1L53.2 24l-1.8 1.7v.9c0 1.9 0 2.5.2 3.4h-4.8c.1-1.1.2-2 .2-3.5V14.1c0-1.6-.1-2.7-.2-3.5h4.9z"/><path class="st0" d="M65.5 24.3c.2 1.9 1.3 2.9 3.2 2.9.9 0 1.8-.3 2.4-.9.4-.3.5-.6.7-1.2l4.1 1.2c-.5 1.2-.9 1.8-1.5 2.4-1.3 1.3-3.2 2-5.5 2s-4.1-.7-5.4-2c-1.4-1.4-2.1-3.4-2.1-5.7 0-4.6 3-7.7 7.5-7.7 3.7 0 6.2 2 7 5.5.2.8.3 1.7.4 3.1v.5H65.5zm6.1-3.3c-.3-1.5-1.3-2.3-3-2.3s-2.7.8-3.1 2.3h6.1zm9 3.3c.2 1.9 1.3 2.9 3.2 2.9.9 0 1.8-.3 2.4-.9.4-.3.5-.6.7-1.2l4.1 1.2c-.5 1.2-.9 1.8-1.5 2.4-1.3 1.3-3.2 2-5.5 2s-4.1-.7-5.4-2c-1.4-1.4-2.1-3.4-2.1-5.7 0-4.6 3-7.7 7.5-7.7 3.7 0 6.2 2 7 5.5.2.8.3 1.7.4 3.1v.5H80.6zm6.1-3.3c-.3-1.5-1.3-2.3-3-2.3s-2.7.8-3.1 2.3h6.1z"/><path class="st0" d="M91.3 34.4c.1-1 .2-2.2.2-3.4V19.5c0-1.5 0-2.5-.2-3.7h4.6V17c1.4-1.2 2.9-1.7 4.8-1.7 2.1 0 3.8.6 5 1.8 1.2 1.2 1.9 3.1 1.9 5.5s-.7 4.3-2 5.7c-1.2 1.2-2.9 1.9-4.8 1.9-1.1 0-2.3-.2-3.2-.6-.6-.3-1-.5-1.6-1.1V31c0 1.3 0 2.4.2 3.4h-4.9zm10.7-8.6c.7-.6 1-1.7 1-3.1 0-2.4-1.3-3.8-3.5-3.8-1.9 0-3.5 1.7-3.5 3.9s1.6 3.9 3.7 3.9c.9-.1 1.7-.4 2.3-.9zm10.3-1.5c.2 1.9 1.3 2.9 3.2 2.9.9 0 1.8-.3 2.4-.9.4-.3.5-.6.7-1.2l4.1 1.2c-.5 1.2-.9 1.8-1.5 2.4-1.3 1.3-3.2 2-5.5 2s-4.1-.7-5.4-2c-1.4-1.4-2.1-3.4-2.1-5.7 0-4.6 3-7.7 7.5-7.7 3.7 0 6.2 2 7 5.5.2.8.3 1.7.4 3.1v.5h-10.8zm6.1-3.3c-.3-1.5-1.3-2.3-3-2.3s-2.7.8-3.1 2.3h6.1zm6.1-1.9h6.8l-3.4 13.8z"/><path class="st1" d="M131 19.4l-1.5 6.2-1.5 6.2-1.5-6.2-1.5-6.2h6zm.7-.6h-7.4l.2.7 1.5 6.2 1.5 6.2.5 2.2.5-2.2 1.5-6.2 1.5-6.2.2-.7zM131 20z"/><path fill="#1b97a8" d="M125.1 19.4l19-17.2-15 20.8z"/><path class="st1" d="M143.2 3.2l-6.4 8.9-7.8 10.8-1.9-1.7-1.9-1.7 9.8-8.9 8.2-7.4zm1.8-2l-10.1 9.1-10.1 9.1 2.1 1.9 2.1 1.9 7.9-11 8.1-11z"/><path class="st1" d="M131.6 20.4c0 2-1.7 3.7-3.7 3.7s-3.7-1.7-3.7-3.7 1.7-3.7 3.7-3.7c2.1-.1 3.7 1.6 3.7 3.7"/><path class="st0" d="M127.9 24.6c-2.4 0-4.3-1.9-4.3-4.3s1.9-4.3 4.3-4.3 4.3 1.9 4.3 4.3-1.9 4.3-4.3 4.3zm0-7.4c-1.7 0-3.2 1.4-3.2 3.2 0 1.7 1.4 3.2 3.2 3.2 1.7 0 3.2-1.4 3.2-3.2 0-1.8-1.4-3.2-3.2-3.2z"/><path class="st0" d="M130.4 20.4c0 1.4-1.1 2.5-2.5 2.5s-2.5-1.1-2.5-2.5 1.1-2.5 2.5-2.5 2.5 1.1 2.5 2.5"/></svg>
        <div class="sideber-username"><i class="fas fa-user-circle"></i> <?= $user_name ?></div>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item<?= $page_id === 'mypage_dashboard' ? ' active' : '' ?>">
                <a class="nav-link" href="./mypage_dashboard">
                    <i class="fas fa-th-large"></i>
                    <p>ダッシュボード</p>
                </a>
            </li>
            <li id="mypage_menu_mystate" class="nav-item<?= $page_id === 'mypage_mystate' ? ' active' : '' ?>">
                <a class="nav-link" href="./mypage_mystate">
                    <i class="fas fa-user-clock"></i>
                    <p>マイ 勤務状況</p>
                </a>
            </li>
            <?php if ($low_user === 1): ?>
            <li class="nav-item<?= $page_id === 'mypage_state' ? ' active' : '' ?>">
                <a class="nav-link" href="./mypage_state">
                    <i class="fas fa-users"></i>
                    <p>従業員 勤務状況（日別）</p>
                </a>
            </li>
            <li class="nav-item<?= $page_id === 'mypage_statelist' || $page_id === 'mypage_list' ? ' active' : '' ?>">
                <a class="nav-link" href="./mypage_statelist">
                    <i class="fas fa-users-cog"></i>
                    <p>従業員 勤務状況（集計）</p>
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item<?= $page_id === 'mypage_notice' ? ' active' : '' ?>">
                <a class="nav-link" href="./mypage_notice">
                    <i class="fas fa-bell"></i>
                    <p>通知
                        <span id="notice_count_new" class="badge" style="background:#d1ecf1;border-color:#bee5eb;color:#0c5460">
                        </span>
                        <span id="notice_count_ng" class="badge" style="background:#f1d1d1;border-color:#f1d1d1;color:#b71c1c"></span>
                    </p>
                </a>
                <div class="badge-text1">申請中</div>
                <div class="badge-text2">NG</div>
            </li>
            <?php if ((int)$mypage_shift_flag->value === 1): ?>
            <li id="mypage_menu_shift" class="nav-item<?= $page_id === 'mypage_shift' ? ' active' : '' ?>">
                <a class="nav-link" href="./mypage_shift">
                    <i class="fas fa-calendar-alt"></i>
                    <p>シフト管理</p>
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item<?= $page_id === 'mypage_profile' ? ' active' : '' ?>">
                <a class="nav-link" href="./mypage_profile">
                    <i class="fas fa-user-circle"></i>
                    <p>プロフィール</p>
                </a>
            </li>
            <?php if ($pay_flag->value == 1): ?>
            <li class="nav-item<?= $page_id === 'mypage_pay' ? ' active' : '' ?>">
                <a href="./mypage_pay" class="nav-link">
                    <i class="fas fa-pager"></i>
                    <p>給与明細</p>
                </a>
            </li>
            <?php endif; ?>
            <hr>
            <li class="nav-item">
                <a class="nav-link" href="./logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <p>ログアウト</p>
                </a>
            </li>
        </ul>
    </div>
</div>
