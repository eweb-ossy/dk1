<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// gateway view
?>

<!-- meta部　読み込み -->
<?php $this->load->view('parts/_head_view'); ?>
<body>
  <div class="container">

    <!-- header部 -->
    <div class="header">
      <!-- ヘッダー部上部　読み込み -->
      <?php $this->load->view('parts/_header_top_view'); ?>
      <div class="header-menu-area">
        <div class="menu-bloc">
          <div id="status_btn" class="header-btn public-btn-01 disable">勤務状況</div>
        </div>
        <div class="menu-bloc"<?= (int)$gateway_mail_flag->value === 0 ? ' style="display:none;"' : '' ?>>
          <div id="mail_btn" class="header-btn public-btn-02 disable">修正依頼</div>
        </div>
        <div class="menu-bloc">
          <div id="shift_btn" class="header-btn public-btn-03 disable">シフト</div>
        </div>
      </div>
    </div>

    <!-- main部 -->
    <div class="main">
      <!-- 時計表示エリア -->
      <div class="time-area">
        <div class="time">
          <div id="date_view" class="date-text"></div>
          <div class="time-text">
            <div class="time-background">
              <div class="time-back">88:88</div>
              <div class="second-back">88</div>
            </div>
            <div class="time-front">
              <div id="time_view"></div>
              <div id="second" class="second-text"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- パーソナル情報表示エリア -->
      <div class="user-display-area">
        <div class="top-area">
          <div class="name-area">
            <div id="user_name" class="user-name user_data">　</div>
            <div id="group1" class="user-group user_data">　</div>
            <div id="group2" class="user-group user_data">　</div>
            <div id="group3" class="user-group user_data">　</div>
          </div>
          <div class="num-area">
            <div class="work-num-area">
              <div class="title">出勤<br>日数</div>
              <div id="count" class="num user_data"></div>
            </div>
            <div class="work-time-area">
              <div class="title">勤務<br>時間</div>
              <div id="time" class="num user_data"></div>
            </div>
          </div>
        </div>
        <div class="bottom-area">
          <div class="id-area">
            <span id="user_id" data-num="<?= (int)$id_size->value ?>">　</span>
          </div>
        </div>
      </div>

      <div class="status-area">
        <div class="inner">
          <div class="in-list">
            <div class="title">出勤中一覧</div>
            <ul id="inUserData"></ul>
          </div>
          <div class="out-list">
            <div class="title">退勤一覧</div>
            <ul id="outUserData"></ul>
          </div>
        </div>
      </div>

      <div class="user-input-area">
        <div class="inner">
          <div class="num-btn-area">
            <div id="num_1" class="num-btn"><div>1</div></div>
            <div id="num_2" class="num-btn"><div>2</div></div>
            <div id="num_3" class="num-btn"><div>3</div></div>
            <div id="num_4" class="num-btn"><div>4</div></div>
            <div id="num_5" class="num-btn"><div>5</div></div>
            <div id="num_6" class="num-btn"><div>6</div></div>
            <div id="num_7" class="num-btn"><div>7</div></div>
            <div id="num_8" class="num-btn"><div>8</div></div>
            <div id="num_9" class="num-btn"><div>9</div></div>
            <div id="num_0" class="num-btn"><div>0</div></div>
          </div>
          <div class="etc-btn-area">
            <div id="num_clear"><div>訂正</div></div>
            <div id="submit_userid"><div>確定</div></div>
          </div>
        </div>
      </div>

      <!-- 出退勤インプットボタンエリア -->
      <?php $message_gateway_flag = isset($message['gateway']['flag']) ? $message['gateway']['flag'] : 0; ?>
      <div id="input_area" class="input-area<?= $message_gateway_flag == 1 ? ' display-no' : '' ?>" data-message="<?= $message_gateway_flag ?>" data-id="" data-area-id="">
        <div id="input_btn" class="bloc-l disable<?= (int)$rest_input_flag->value === 1 ? ' rest-input' : '' ?>">
          <div class="inner public-btn-05">
            <div class="input-text">出勤</div>
          </div>
        </div>
        <?php if ((int)$rest_input_flag->value === 1): ?>
        <div class="rest-area">
          <div id="rest_in_btn" class="rest-bloc disable">
            <div class="inner">
              <div class="input-text">休憩開始</div>
            </div>
          </div>
          <div id="rest_out_btn" class="rest-bloc disable">
            <div class="inner">
              <div class="input-text">休憩終了</div>
            </div>
          </div>
        </div>
        <?php endif; ?>
        <div id="output_btn" class="bloc-r disable<?= (int)$rest_input_flag->value === 1 ? ' rest-input' : '' ?>">
          <div class="inner public-btn-06">
            <div class="input-text">退勤</div>
          </div>
        </div>
      </div>

      <!-- メッセージ表示エリア -->
      <div id="message_area" class="message-area<?= $message_gateway_flag == 0 ? ' display-no' : '' ?>">
        <div class="title-area">
          <div class="img-area" style="width:60px">
            <svg version="1.1" id="レイヤー_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 53.8 44.8" style="enable-background:new 0 0 53.8 44.8;" xml:space="preserve">
            <style type="text/css">
            	.st0{fill:#1E8E9D;}
            	.st1{fill:#FFFFFF;}
            </style>
            <path class="st0" d="M48.8,0H5C2.2,0,0,2.2,0,5v26.9c0,2.8,2.2,5,5,5h28.1c4.8,2.6,9.7,5.2,14.5,7.9c-1-2.6-2.1-5.2-3.1-7.9h4.3 c2.8,0,5-2.2,5-5V5C53.8,2.2,51.5,0,48.8,0z"/>
            <g>
            	<path class="st1" d="M15.3,16.7c0.5,0,0.9,0.1,1.2,0.3l0.1,0.1l0.4-1.4l-0.1,0c-0.3-0.2-0.9-0.3-1.7-0.3c-2.3,0-3.8,1.6-3.8,3.9 c0,2.3,1.4,3.8,3.5,3.8c1,0,1.7-0.3,1.9-0.4l0.1,0l-0.3-1.4l-0.1,0.1c-0.3,0.1-0.7,0.3-1.3,0.3c-1.2,0-2-0.9-2-2.4 C13.3,17.9,13.9,16.7,15.3,16.7z"/><path class="st1" d="M21.8,15.3c-0.8,0-1.5,0.3-2,0.9v-4h-1.8v10.7h1.8v-4.5c0-1,0.7-1.6,1.4-1.6c1,0,1.2,1,1.2,1.8v4.3h1.8v-4.5 C24.3,16.1,23,15.3,21.8,15.3z"/><path class="st1" d="M28.8,15.3c-2,0-3.3,1.6-3.3,4c0,2.3,1.4,3.8,3.5,3.8c0.9,0,1.7-0.2,2.3-0.4l0.1,0l-0.3-1.3L31,21.2 c-0.5,0.2-1.1,0.3-1.8,0.3c-0.6,0-1.9-0.2-2-2h4.5l0-0.1c0-0.2,0-0.4,0-0.7C31.8,17.1,31,15.3,28.8,15.3z M27.2,18.3 c0.1-0.8,0.6-1.7,1.5-1.7c0.4,0,0.7,0.1,0.9,0.3c0.4,0.4,0.5,1,0.5,1.3H27.2z"/><path class="st1" d="M36.4,16.7c0.5,0,0.9,0.1,1.2,0.3l0.1,0.1l0.4-1.4l-0.1,0c-0.3-0.2-0.9-0.3-1.7-0.3c-2.3,0-3.8,1.6-3.8,3.9 c0,2.3,1.4,3.8,3.5,3.8c1,0,1.7-0.3,1.9-0.4l0.1,0l-0.3-1.4l-0.1,0.1c-0.3,0.1-0.7,0.3-1.3,0.3c-1.2,0-2-0.9-2-2.4 C34.3,17.9,34.9,16.7,36.4,16.7z"/><path class="st1" d="M42.5,18.5l2.6-3.1h-2.1l-1.8,2.4c-0.1,0.2-0.2,0.3-0.3,0.5v-6.2h-1.8v10.7h1.8v-2.7l0.4-0.5l2,3.1l0,0.1h2.1 L42.5,18.5z"/>
            </g>
            </svg>
          </div>
          <?php $message_gateway_title = isset($message['gateway']['title']) ? $message['gateway']['title'] : ""; ?>
          <div class="text-area"><?= $message_gateway_title ?></div>
        </div>
        <?php $message_gateway_detail = isset($message['gateway']['detail']) ? $message['gateway']['detail'] : ""; ?>
        <div class="content"><?= nl2br($message_gateway_detail) ?></div>
      </div>
    </div>
  </div>
  <!-- モーダル　読み込み -->
  <?php $this->load->view('parts/_modal_notice_view'); ?>

  <!-- javascript　読み込み -->
  <?php $this->load->view('parts/_javascript_view'); ?>
  <script>
    const gateway_status_view_flag = <?= (int)$gateway_status_view_flag->value ?>;
    <?php $message_in_flag = isset($message['in']['flag']) ? (int)$message['in']['flag'] : 0; ?>
    const message_in_flag = <?= $message_in_flag ?>;
    <?php $message_out_flag = isset($message['out']['flag']) ? (int)$message['out']['flag'] : 0; ?>
    const message_out_flag = <?= $message_out_flag ?>;
  </script>
</body>
</html>
