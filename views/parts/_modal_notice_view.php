<?php
defined('BASEPATH') or exit('No direct script access allowed');
// 申請　モーダル
?>

<!-- モーダル１　個人勤務状況 -->
<div id="modal1" class="modal" data-iziModal-fullscreen="true" data-iziModal-title="" data-iziModal-subtitle="勤務状況" data-iziModal-icon="icon-status">
  <div class="modal-content">
    <div class="">
      <div id="tabs1">
        <ul class="tabs">
          <li class="tab active">今月<span id="month_0"></span></li>
          <li class="tab">先月<span id="month_1"></span></li>
          <li class="tab">先々月<span id="month_2"></span></li>
        </ul>
        <div class="tab-content show">
          <table>
            <thead>
              <tr>
                <th>日</th>
                <th>曜</th>
                <th>出勤時間</th>
                <th>退勤時間</th>
                <th>備考</th>
                <?php if ((int)$gateway_mail_flag->value === 1): ?>
                <th class="mail-sys">修正依頼</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody class="month_data" id="month_data_0"></tbody>
          </table>
        </div>
        <div class="tab-content">
          <table>
            <thead>
              <tr>
                <th>日</th>
                <th>曜</th>
                <th>出勤時間</th>
                <th>退勤時間</th>
                <th>備考</th>
                <?php if ((int)$gateway_mail_flag->value === 1): ?>
                <th class="mail-sys">修正依頼</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody class="month_data" id="month_data_1"></tbody>
          </table>
        </div>
        <div class="tab-content">
        <table>
          <thead>
            <tr>
              <th>日</th>
              <th>曜</th>
              <th>出勤時間</th>
              <th>退勤時間</th>
              <th>備考</th>
              <?php if ((int)$gateway_mail_flag->value === 1): ?>
              <th class="mail-sys">申請依頼</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody class="month_data" id="month_data_2">
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
  <div class="modal-footer">
    <a href="javascript:void(0)" class="btn-flat" data-izimodal-close="">閉じる</a>
  </div>
</div>

<!-- モーダル２　修正依頼 -->
<div id="modal2" class="modal" data-iziModal-title="" data-iziModal-subtitle="各種申請" data-iziModal-icon="icon-mail" data-izimodal-transitionin="bounceInDown">
  <div class="modal-content">
    <div class="inner">
      <div class="modal-row time_edit_area">
        <div id="modal_date" class="modal-date-area"></div>
        <div class="status-block">
          <div class="date-area">
            <div id="date-area-wareki"></div>
            <div id="calendar_date" class="date-bloc"></div>
            <div id="today_mark" class="today-mark"></div>
          </div>
          <div class="form-group">
            <?php foreach ($notice_status_data as $value): ?>
            <?php if ($value->group == 1 && $value->status == 1): ?>
              <button type="button" class="notice-btn<?= (int)$value->term === 0 ? ' past' : '' ?><?= (int)$value->term === 1 ? ' future' : '' ?>" data-id="<?= (int)$value->notice_status_id ?>" data-term="<?= (int)$value->term ?>"><?= $value->notice_status_title ?></button>
            <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <div class="form-group">
            <?php foreach ($notice_status_data as $value): ?>
            <?php if ($value->group == 2 && $value->status == 1): ?>
              <button type="button" class="notice-btn<?= (int)$value->term === 0 ? ' past' : '' ?><?= (int)$value->term === 1 ? ' future' : '' ?>" data-id="<?= (int)$value->notice_status_id ?>" data-term="<?= (int)$value->term ?>"><?= $value->notice_status_title ?></button>
            <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <div class="form-group">
            <?php foreach ($notice_status_data as $value): ?>
            <?php if ($value->group == 3 && $value->status == 1): ?>
              <button type="button" class="notice-btn<?= (int)$value->term === 0 ? ' past' : '' ?><?= (int)$value->term === 1 ? ' future' : '' ?>" data-id="<?= (int)$value->notice_status_id ?>" data-term="<?= (int)$value->term ?>"><?= $value->notice_status_title ?></button>
            <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <div class="time-bloc">
            <div class="time-area">
              <div class="bloc" style="margin-right:10px;">
                <span>シフト予定</span>
                <div id="shiftStatus" class="shift-status"></div>
              </div>
              <div class="bloc shift-time">
                <span>出勤予定</span>
                <div id="shift_in_time" class="time"></div>
              </div>
              <div class="bloc shift-time">
                <span>退勤予定</span>
                <div id="shift_out_time" class="time"></div>
              </div>
            </div>
            <div class="arrow-area select-notice"></div>
            <div id="selectNotice" class="notice-title select-notice"></div>
          </div>
          <div class="time-status"><i class="fas fa-walking"></i> <span id="timeStatus"></span></div>
          <div class="time-bloc">
            <div class="time-area">
              <div class="bloc">
                <span>出勤時刻</span>
                <div id="in_time" class="time">--</div>
              </div>
              <div class="bloc">
                <span>退勤時刻</span>
                <div id="out_time" class="time">--</div>
              </div>
            </div>
            <div class="arrow-area"></div>
            <div class="time-area time-edit-area">
              <div id="in_time_input_area" class="bloc-long">
                <div class="bloc">
                  <span>出勤時刻</span>
                  <input type="text" id="picker_in_time" class="time-input">
                </div>
                <div id="picker_del_in_time" class="picker-del-btn"><i class="fas fa-times-circle"></i></div>
              </div>
              <div id="out_time_input_area" class="bloc-long">
                <div class="bloc">
                  <span>退勤時刻</span>
                  <input type="text" id="picker_out_time" class="time-input">
                </div>
                <div id="picker_del_out_time" class="picker-del-btn"><i class="fas fa-times-circle"></i></div>
              </div>
            </div>
            <div class="time-area notice-comment-area">
              <div id="noticeComment" class="notice-comment"></div>
              <!-- <div id="paid_hour" class="notice-paid-select">
                <select name="paid_hour" id="">
                  <option value="1">全日</option>
                  <option value="0.5">半日</option>
                </select>
              </div> -->
            </div>
          </div>
        </div>
      </div>
      <div class="mail-memo">
        <textarea id="memo" class="time-val" rows="3" placeholder="コメント"></textarea>
      </div>
      <div class="mail-err">未記入もしくは記入ミスがあります</div>
      <div class="time-modal-footer">
        <div id="time_edit_submit" class="btn-basic disable">依頼送信</div>
      </div>
    </div>
  </div>
</div>

<!-- モーダル３　シフト -->
<div id="modal3" class="modal" data-iziModal-fullscreen="true" data-iziModal-title="" data-iziModal-subtitle="シフト" data-iziModal-icon="icon-shift">
  <div class="modal-content">
    <div class="">
      <div id="tabs2">
        <ul class="tabs">
          <li class="tab">来月<span id="shift_month_0"></span></li>
          <li class="tab active">今月<span id="shift_month_1"></span></li>
          <li class="tab">先月<span id="shift_month_2"></span></li>
        </ul>
        <div class="tab-content">
        <table>
          <thead>
            <tr>
              <th>日</th>
              <th>曜</th>
              <th>予定</th>
              <th>出勤時間</th>
              <th>退勤時間</th>
            </tr>
          </thead>
          <tbody class="month_data" id="shift_month_data_0">
          </tbody>
        </table>
      </div>
        <div class="tab-content show">
        <table>
          <thead>
            <tr>
              <th>日</th>
              <th>曜</th>
              <th>予定</th>
              <th>出勤時間</th>
              <th>退勤時間</th>
            </tr>
          </thead>
          <tbody class="month_data" id="shift_month_data_1">
          </tbody>
        </table>
      </div>
        <div class="tab-content">
        <table>
          <thead>
            <tr>
              <th>日</th>
              <th>曜</th>
              <th>予定</th>
              <th>出勤時間</th>
              <th>退勤時間</th>
            </tr>
          </thead>
          <tbody class="month_data" id="shift_month_data_2">
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
  <div class="modal-footer">
    <a href="javascript:void(0)" class="btn-flat" data-izimodal-close="">閉じる</a>
  </div>
</div>

<!-- モーダル4　出勤後メッセージ -->
<div id="modal4" class="modal" data-iziModal-fullscreen="true" data-iziModal-title="" data-iziModal-subtitle="メッセージ" data-iziModal-icon="icon-status">
  <div class="modal-content">
    <div class="inner">
      <?php $message_in_title = isset($message['in']['title']) ? $message['in']['title'] : ""; ?>
      <div class="title"><?= $message_in_title ?></div>
      <?php $message_in_detail = isset($message['in']['detail']) ? $message['in']['detail'] : ""; ?>
      <div class="text">
        <?= nl2br($message_in_detail) ?>
      </div>
      <div class="mail-memo">
        <textarea id="message_in" class="time-val" rows="3" placeholder=""></textarea>
      </div>
      <div class="time-modal-footer">
        <div id="message_in_submit" class="btn-basic">送信</div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="javascript:void(0)" class="btn-flat" data-izimodal-close="">閉じる</a>
  </div>
</div>

<!-- モーダル5　退勤後メッセージ -->
<div id="modal5" class="modal" data-iziModal-fullscreen="true" data-iziModal-title="" data-iziModal-subtitle="メッセージ" data-iziModal-icon="icon-status">
  <div class="modal-content">
    <div class="inner">
      <?php $message_out_title = isset($message['out']['title']) ? $message['out']['title'] : ""; ?>
      <div class="title"><?= $message_out_title ?></div>
      <?php $message_out_detail = isset($message['out']['detail']) ? $message['out']['detail'] : ""; ?>
      <div class="text">
        <?= nl2br($message_out_detail) ?>
      </div>
      <div class="mail-memo">
        <textarea id="message_out" class="time-val" rows="3" placeholder=""></textarea>
      </div>
      <div class="time-modal-footer">
        <div id="message_out_submit" class="btn-basic">送信</div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="javascript:void(0)" class="btn-flat" data-izimodal-close="">閉じる</a>
  </div>
</div>