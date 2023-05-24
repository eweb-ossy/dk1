<?php
defined('BASEPATH') or exit('No direct script access allowed');
// 時間修正用モーダル
?>

<div id="modal2" class="modal" data-iziModal-fullscreen="false" data-iziModal-title="" data-iziModal-subtitle="" data-iziModal-icon="icon-status">
  <div class="modal-content">
    <div class="inner">
      <?php if ($page_id !== 'admin_shift'): ?>
      <div id="shift_view_btn" class="shift-view-btn"><i class="fas fa-exchange-alt"></i> シフト編集</div>
      <?php endif; ?>
      <!-- シフト用 -->
      <div id="shift_btn_area" class="modal-row<?= $page_id === 'admin_shift' ? ' shift' : '' ?>">
        <div id="status_name"></div>
        <?php if ((int)$auto_shift_flag->value === 0): ?>
        <div id="state_none" class="btn gray shift-btn">未登録</div>
        <?php endif; ?>
        <div id="state_work" class="btn blue shift-btn">出勤</div>
        <div id="state_rest" class="btn red shift-btn">公休</div>
        <div id="state_raid" class="btn green shift-btn">有給</div>
        <div id="shift_time_edit_area">
          <div class="shift-time-edit">
            <div id="shift_date" class="modal-date-area"></div>
            <div class="time-area">
              <div class="bloc">
                <span>出勤予定</span>
                <div id="shift_in_time" class="time">--</div>
              </div>
              <div class="bloc">
                <span>退勤予定</span>
                <div id="shift_out_time" class="time">--</div>
              </div>
            </div>
            <div class="arrow-area"></div>
            <div class="time-area time-edit-area">
              <div class="bloc-long">
                <div class="bloc">
                  <span>出勤予定</span>
                  <input type="text" id="picker_shift_in_time" class="time-input">
                </div>
                <div id="picker_shift_del_in_time" class="picker-del-btn"><i class="fas fa-times-circle"></i></div>
              </div>
              <div class="bloc-long">
                <div class="bloc">
                  <span>退勤予定</span>
                  <input type="text" id="picker_shift_out_time" class="time-input">
                </div>
                <div id="picker_shift_del_out_time" class="picker-del-btn"><i class="fas fa-times-circle"></i></div>
              </div>
              <div class="bloc-long" style="flex-direction: row-reverse;">
                <div class="work-value">
                  予定<span id="shift_value"></span>
                  <div id="shift_value2" class="work-value-hour"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="shift-rest-edit">
            <div class="rest-time-area">
              <div class="rest-title">予定休憩時間</div>
              <div class="rest-bar">
                <input type="range" min="0" max="180" class="time-val slider" id="shift_rest_range">
                <div class="shift-rest-value">
                  休憩<span id="shift_rest_value"></span>
                  <div id="shift_rest_value2" class="shift-rest-value-hour"></div>
                </div>
                <div class="shift-memori-area">
                  <div id="shift_memori_0" class="rs_btn">|</div>
                  <div id="shift_memori_15" class="rs_btn">|</div>
                  <div id="shift_memori_30" class="rs_btn">|</div>
                  <div id="shift_memori_45" class="rs_btn">|</div>
                  <div id="shift_memori_60" class="rs_btn">|</div>
                  <div id="shift_memori_75" class="rs_btn">|</div>
                  <div id="shift_memori_90" class="rs_btn">|</div>
                  <div id="shift_memori_105" class="rs_btn">|</div>
                  <div id="shift_memori_120" class="rs_btn">|</div>
                  <div id="shift_memori_135" class="rs_btn">|</div>
                  <div id="shift_memori_150" class="rs_btn">|</div>
                  <div id="shift_memori_165" class="rs_btn">|</div>
                  <div id="shift_memori_180" class="rs_btn">|</div>
                </div>
              </div>
              <div class="rest-btn-area">
                <div class="rest-btn">
                  <div id="shift_rest_0" data-time="0" class="rs_btn">0</div>
                  <div id="shift_rest_15" data-time="15" class="rs_btn">15</div>
                  <div id="shift_rest_30" data-time="30" class="rs_btn">30</div>
                  <div id="shift_rest_45" data-time="45" class="rs_btn">45</div>
                  <div id="shift_rest_60" data-time="60" class="rs_btn">60</div>
                  <div id="shift_rest_75" data-time="75" class="rs_btn">75</div>
                  <div id="shift_rest_90" data-time="90" class="rs_btn">90</div>
                  <div id="shift_rest_105" data-time="105" class="rs_btn">105</div>
                  <div id="shift_rest_120" data-time="120" class="rs_btn">120</div>
                  <div id="shift_rest_135" data-time="135" class="rs_btn">135</div>
                  <div id="shift_rest_150" data-time="150" class="rs_btn">150</div>
                  <div id="shift_rest_165" data-time="165" class="rs_btn">165</div>
                  <div id="shift_rest_180" data-time="180" class="rs_btn">180分</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <?php if ($page_id !== 'admin_shift'): ?>
      <div class="modal-row time_edit_area">
        <div id="modal_date" class="modal-date-area"></div>
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
          <div class="bloc-long">
            <div class="bloc">
              <span>出勤時刻</span>
              <input type="text" id="picker_in_time" class="time-input">
            </div>
            <div id="picker_del_in_time" class="picker-del-btn"><i class="fas fa-times-circle"></i></div>
          </div>
          <div class="bloc-long">
            <div class="bloc">
              <span>退勤時刻</span>
              <input type="text" id="picker_out_time" class="time-input">
            </div>
            <div id="picker_del_out_time" class="picker-del-btn"><i class="fas fa-times-circle"></i></div>
          </div>
          <div class="bloc-long" style="flex-direction: row-reverse;">
            <div class="work-value">
              勤務<span id="work_value"></span>
              <div id="work_value2" class="work-value-hour"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="rest-time-area">
        <div class="rest-title">休憩時間</div>
        <div class="rest-bar">
          <input type="range" min="0" max="180" class="time-val slider" id="rest_range">
          <div class="rest-value">
            休憩<span id="rest_value"></span>
            <div id="rest_value2" class="rest-value-hour"></div>
          </div>
          <div class="memori-area">
            <div id="memori_0" class="r_btn">|</div>
            <div id="memori_15" class="r_btn">|</div>
            <div id="memori_30" class="r_btn">|</div>
            <div id="memori_45" class="r_btn">|</div>
            <div id="memori_60" class="r_btn">|</div>
            <div id="memori_75" class="r_btn">|</div>
            <div id="memori_90" class="r_btn">|</div>
            <div id="memori_105" class="r_btn">|</div>
            <div id="memori_120" class="r_btn">|</div>
            <div id="memori_135" class="r_btn">|</div>
            <div id="memori_150" class="r_btn">|</div>
            <div id="memori_165" class="r_btn">|</div>
            <div id="memori_180" class="r_btn">|</div>
          </div>
        </div>
        <div class="rest-btn-area">
          <div class="rest-btn">
            <div id="rest_0" data-time="0" class="r_btn">0</div>
            <div id="rest_15" data-time="15" class="r_btn">15</div>
            <div id="rest_30" data-time="30" class="r_btn">30</div>
            <div id="rest_45" data-time="45" class="r_btn">45</div>
            <div id="rest_60" data-time="60" class="r_btn">60</div>
            <div id="rest_75" data-time="75" class="r_btn">75</div>
            <div id="rest_90" data-time="90" class="r_btn">90</div>
            <div id="rest_105" data-time="105" class="r_btn">105</div>
            <div id="rest_120" data-time="120" class="r_btn">120</div>
            <div id="rest_135" data-time="135" class="r_btn">135</div>
            <div id="rest_150" data-time="150" class="r_btn">150</div>
            <div id="rest_165" data-time="165" class="r_btn">165</div>
            <div id="rest_180" data-time="180" class="r_btn">180分</div>
          </div>
        </div>
      </div>
      <?php if ((int)$area_flag->value === 1): ?>
      <div class="select-place-area">
        <span>エリア選択</span>
        <select name="place" id="select-place" class="time-val select-area">
          <option data-area-id="" value="">---</option>
          <?php foreach ($area_data as $val): ?>
          <option data-area-id="<?php echo $val->id; ?>" value="<?php echo $val->area_name; ?>">
            <?php echo $val->area_name; ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
      <div class="mail-memo">
        <textarea id="memo" class="time-val" rows="3" placeholder="メモ"></textarea>
      </div>
      <?php if ($gps_flag !== 0): ?>
      <div class="gps-area">
        <div class="gps-bloc">
          <div class="gps-title">出勤時 位置情報</div>
          <div id="map_in"></div>
        </div>
        <div class="gps-bloc">
          <div class="gps-title">退勤時 位置情報</div>
          <div id="map_out"></div>
        </div>
      </div>
      <?php endif; ?>
      <?php endif; ?>
      
      <div class="mail-err">未記入もしくは記入ミスがあります</div>
      <div class="time-modal-footer">
        <div id="time_edit_submit" class="btn-basic disable">修正</div>
      </div>
    </div>
  </div>
</div>