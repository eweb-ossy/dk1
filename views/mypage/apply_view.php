<?php defined('BASEPATH') OR exit('No direct script access allowed');
// mypage apply view
// メタ部　読み込み
$this->load->view('parts/_mypage_head_view');
?>

<body>
  <div class="wrapper ">
    <!-- サイドバー読込 -->
    <?php $this->load->view('parts/_mypage_sidebar_view'); ?>
    <div class="main-panel">
      <!-- 上部ナビ　読込 -->
        <?php $this->load->view('parts/_mypage_top_nav_view'); ?>
      <div class="content">
        <!-- 申請　エリア -->
        <div class="form-row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <h5 id="step01_title">申請を選択</h5>
                <div class="form-group row">
                  <?php foreach ($notice_status_data as $value): ?>
                  <?php if ($value->group == 1 && $value->status == 1): ?>
                    <button type="button" id="notice_flag_<?= sprintf('%02d', $value->notice_status_id) ?>" class="btn btn-success btn-sm notice_flag"><?= $value->notice_status_title ?></button>
                  <?php endif; ?>
                  <?php endforeach; ?>
                </div>
                <div class="form-group row">
                  <?php foreach ($notice_status_data as $value): ?>
                  <?php if ($value->group == 2 && $value->status == 1): ?>
                    <button type="button" id="notice_flag_<?= sprintf('%02d', $value->notice_status_id) ?>" class="btn btn-success btn-sm notice_flag"><?= $value->notice_status_title ?></button>
                  <?php endif; ?>
                  <?php endforeach; ?>
                </div>
                <div class="form-group row">
                  <?php foreach ($notice_status_data as $value): ?>
                  <?php if ($value->group == 3 && $value->status == 1): ?>
                    <button type="button" id="notice_flag_<?= sprintf('%02d', $value->notice_status_id) ?>" class="btn btn-success btn-sm notice_flag"><?= $value->notice_status_title ?></button>
                  <?php endif; ?>
                  <?php endforeach; ?>
                </div>
                <div id="notice_notice_flag" class="selected">未選択<span class="sub-text">上記より申請を選択してください</span></div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <!-- 日付選択　エリア -->
          <input type="hidden" name="before_in_time">
          <input type="hidden" name="before_out_time">
          <div id="step02_area" class="col-md-5 area-none">
            <div class="card">
              <div class="card-body" style="text-align:center;">
                <h5 id="step02_title">申請日を選択</h5>
                <!-- <div class="calender" data-toggle="datepicker"></div> -->
                <div id="datepicker1" class="calender"></div>
                <div id="step02_now_time" class="form-row area-none">
                  <div class="form-group col-md-6">
                    <label>出勤時刻</label>
                    <h5 class="now-in-time">09:00</h5>
                  </div>
                  <div class="form-group col-md-6">
                    <label>退勤時刻</label>
                    <h5 class="now-out-time">17:00</h5>
                  </div>
                </div>
                <div id="step02_return_area" class="form-group col-md-12" style="text-align:right;">
                  <div id="step02_return"><i class="fas fa-undo"></i> リセット</div>
                </div>
              </div>
            </div>
          </div>
          <!-- 時刻入力　エリア -->
          <div id="step03_area" class="col-md-4 area-none">
            <div class="card">
              <div class="card-body">
                <h5 id="step03_title">修正申請</h5>
                <div class="form-row">
                  <div id="input_in_time_area" class="form-group col-md-6">
                    <label for="now_in_time">出勤時刻</label>
                    <input type="text" class="form-control now-in-time" id="now_in_time" data-edit-in-time="<?= (int)$edit_in_time->value ?>">
                  </div>
                  <div id="input_out_time_area" class="form-group col-md-6">
                    <label for="now_out_time">退勤時刻</label>
                    <input type="text" class="form-control now-out-time" id="now_out_time" data-edit-out-time="<?= (int)$edit_out_time->value ?>">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- 削除依頼　エリア -->
          <div id="step03_2_area" class="col-md-4 area-none">
            <div class="card">
              <div class="card-body">
                <h5 id="step03_2_title">下記時刻を削除申請</h5>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label>出勤時刻</label>
                    <h5 class="now-in-time del-in-time">09:00</h5>
                  </div>
                  <div class="form-group col-md-6">
                    <label>退勤時刻</label>
                    <h5 class="now-out-time del-out-time">17:00</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <!-- コメント入力　エリア -->
          <div id="step04_area" class="col-md-12 area-none">
            <div class="card">
              <div class="card-body">
                <h5>申請内容を入力</h5>
                <div class="form-group">
                  <label for="textarea1">コメント</label>
                  <textarea class="form-control" id="textarea1" rows="3"></textarea>
                </div>
                <div class="form-group notice-submit-area" style="text-align: right;">
                  <div class="mail-err">入力エラー</div>
                  <button type="button" id="mail_submit" class="btn btn-info pull-right disabled">送信</button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- javascript読込 -->
    <?php $this->load->view('parts/_mypage_javascript_view'); ?>
</body>

</html>