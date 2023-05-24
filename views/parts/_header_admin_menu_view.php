<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// admin menu view
?>
<div class="header-menu-area">
    <?php if ($system_id->value === 'esna' || $system_id->value === 'it-service'): ?>
    <div class="menu-bloc">
        <a class="admin-btn-12<?= $page_id === 'admin_info' ? ' active' : '' ?>" href="admin_info">状況</a>
    </div>
    <?php endif; ?>
    <div class="menu-bloc">
        <a class="admin-btn-01<?= $page_id === 'admin_list_day' ? ' active' : '' ?>" href="admin_list_day">日別集計</a>
    </div>
    <!-- <div class="menu-bloc">
        <a class="admin-btn-10<?= $page_id === 'admin_list_weekly' ? ' active' : '' ?>" href="admin_list_weekly">週別集計</a>
    </div> -->
    <div class="menu-bloc">
        <a class="admin-btn-02<?= $page_id === 'admin_list_month' ? ' active' : '' ?>" href="admin_list_month">月別集計</a>
    </div>
    <!-- <div class="menu-bloc">
        <a class="admin-btn-11" href="">年別集計</a>
    </div> -->
    <div class="menu-bloc">
        <a class="admin-btn-03<?= $page_id === 'admin_list_user' || $page_id === 'admin_lists' ? ' active' : '' ?>" href="admin_list_user">従業員別集計</a>
    </div>
    <div class="menu-bloc">
        <a class="admin-btn-04<?= $page_id === 'admin_shift' ? ' active' : '' ?>" href="admin_shift">シフト管理</a>
    </div>
    <div class="menu-bloc">
        <a class="admin-btn-05<?= $page_id === 'admin_users' || $page_id === 'admin_user_detail' ? ' active' : '' ?>" href="admin_users">従業員管理</a>
    </div>
    <?php if ($pay_flag->value == 1 && (int)$this->session->authority === 4): ?>
    <div class="menu-bloc">
        <a class="admin-btn-08<?= $page_id === 'admin_pay' ? ' active' : '' ?>" href="admin_pay">給与管理</a>
    </div>
    <?php endif; ?>

    <?php if (((int)$this->session->authority === 3 || (int)$this->session->authority === 4) && ((int)$advance_pay_flag->value === 1 || (int)$aporan_flag->value === 1)): ?>
        <div class="menu-bloc">
            <a class="admin-btn-09<?= $page_id === 'admin_to' ? ' active' : '' ?>" href="admin_to">配信管理</a>
        </div>
    <?php endif; ?>
    
    <?php if ((int)$this->session->authority === 4): ?>
        <div class="menu-bloc">
            <a class="admin-btn-06<?= $page_id === 'admin_conf' ? ' active' : '' ?>" href="admin_conf">設定</a>
        </div>
    <?php endif; ?>
</div>
