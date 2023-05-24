<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Process_time_lib
{
  protected $CI;
  public function __construct()
  {
    $this->CI =& get_instance();
  }

  // 出勤時分析処理
  public function in_time($data)
  {
    // gateway or nonstop から
    if ($data['flag'] === 'gateway' || $data['flag'] === 'nonstop') {
      $data['in_work_time'] = $data['in_time'];
      // ルールの取得
      $this->CI->load->library('process_rules_lib'); // rules lib 読込
      $rules = $this->CI->process_rules_lib->get_rule($data['user_id']);
      // gateway ルール
      if ($rules && $data['flag'] === 'gateway') {
        $in_marume = '';
        $in_marume_flag = (int)$rules->in_marume_flag;
        $out_marume_flag = (int)$rules->out_marume_flag;
        // 時刻合わせ
        if ($in_marume_flag === 2 || $in_marume_flag === 3) {
          $in_marume_time = $rules->in_marume_time;
          if (strtotime($data['dk_date'].' '.$in_marume_time) >= strtotime($data['dk_date'].' '.$data['in_time'])) {
            $data['in_work_time'] = $in_marume_time;
            $in_marume = 'on';
          }
        }
        // シフト合わせ
        if ($in_marume_flag === 4 || $in_marume_flag === 5) {
          $this->CI->load->model('model_shift');
          $shift_data = $this->CI->model_shift->find_day_userid($data['dk_date'], $data['user_id']); //シフトデータ取得
          if ($shift_data) {
            if ((int)$shift_data->status === 0) {
              $shift_in_time = $shift_data->in_time;
            }
          } else {
            $this->CI->load->model('model_config');
            $auto_shift_flag = (int)$this->CI->model_config->get_data()->auto_shift_flag; // autoシフトフラグ
            if ($auto_shift_flag === 1) {
              if ($rules->basic_in_time && $rules->basic_rest_weekday) {
                $basic_rest_week = str_split($rules->basic_rest_weekday);
                $this->CI->load->helper('holiday_date');
                $datetime = new DateTimeImmutable($data['dk_date']);
                $holiday_datetime = new HolidayDateTime($data['dk_date']);
                $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
                if ($basic_rest_week[$w] == 0) {
                  $shift_in_time = $rules->basic_in_time;
                }
              }
            }
          }
          if ($shift_in_time && strtotime($data['dk_date'].' '.$shift_in_time) >= strtotime($data['dk_date'].' '.$data['in_time'])) {
            $data['in_work_time'] = $shift_in_time;
            $in_marume = 'on';
          }
        }
        // まるめ
        if ($in_marume_flag === 1 || $in_marume_flag === 3 || $in_marume_flag === 5) {
          if ($in_marume === '') {
            $in_time_h = substr($data['in_time'], 0, 2);
            $in_time_m = substr($data['in_time'], 3, 2);
            $in_marume_hour = (int)$rules->in_marume_hour;
            if ($in_marume_hour === 15) { // 15分まるめ
              if ((int)$in_time_m < 15 && (int)$in_time_m != 0) {
                $data['in_work_time'] = $in_time_h.':'.'15:00';
              }
              if ((int)$in_time_m >= 15 && (int)$in_time_m < 30) {
                $data['in_work_time'] = $in_time_h.':30:00';
              }
              if ((int)$in_time_m >= 30 && (int)$in_time_m < 45) {
                $data['in_work_time'] = $in_time_h.':45:00';
              }
              if ((int)$in_time_m >= 45) {
                $data['in_work_time'] = ((int)$in_time_h + 1).':00:00';
              }
            }
            if ($in_marume_hour === 30) { // 30分まるめ
              if ((int)$in_time_m < 30 && (int)$in_time_m != 0) {
                $data['in_work_time'] = $in_time_h.':30:00';
              }
              if ((int)$in_time_m >= 30) {
                $data['in_work_time'] = ((int)$in_time_h + 1).':00:00';
              }
            }
          }
        }
        // 自動退勤 時刻設定
        if ($out_marume_flag === 6) {
          $data['out_work_time'] = $rules->out_marume_time;
          $data['out_flag'] = 1;
          $data['auto_out'] = '自動退勤 '.substr($data['out_work_time'], 0, 2).'時'.substr($data['out_work_time'], 3, 2).'分を処理';
        }
        // 自動退勤 シフトに準ずる
        if ($out_marume_flag === 7) {
          $this->CI->load->model('model_shift');
          $shift_data = $this->CI->model_shift->find_day_userid($data['dk_date'], $data['user_id']); //シフトデータ取得
          if ($shift_data) {
            if ((int)$shift_data->status === 0) {
              $shift_out_time = $shift_data->out_time;
            }
          } else {
            $this->CI->load->model('model_config');
            $auto_shift_flag = (int)$this->CI->model_config->get_data()->auto_shift_flag; // autoシフトフラグ
            if ($auto_shift_flag === 1) {
              if ($rules->basic_out_time && $rules->basic_rest_weekday) {
                $basic_rest_week = str_split($rules->basic_rest_weekday);
                $this->CI->load->helper('holiday_date');
                $datetime = new DateTimeImmutable($data['dk_date']);
                $holiday_datetime = new HolidayDateTime($data['dk_date']);
                $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
                if ($basic_rest_week[$w] == 0) {
                  $shift_out_time = $rules->basic_out_time;
                }
              }
            }
          }
          if ($shift_out_time) {
            $data['out_work_time'] = $shift_out_time;
            $data['out_flag'] = 1;
            $data['auto_out'] = '自動退勤 '.substr($data['out_work_time'], 0, 2).'時'.substr($data['out_work_time'], 3, 2).'分を処理';
          }
        }
      }
      // nonstop ルール
      if ($rules && $data['flag'] === 'nonstop') {
        if ($rules->basic_in_time) {
          $data['in_work_time'] = $rules->basic_in_time;
        }
      }
      // ルールなし
      if (!$rules) {
        $data['in_work_time'] = $data['in_time'];
      }
    }

    $this->CI->load->model('model_shift');
    $shift_data = $this->CI->model_shift->find_day_userid($data['dk_date'], $data['user_id']); //シフトデータ取得
    if ($shift_data) {
      if ((int)$shift_data->status === 0 && $shift_data->in_time) {
        $shift_in_time = $shift_data->in_time;
      }
    } else {
      $this->CI->load->model('model_config');
      $auto_shift_flag = (int)$this->CI->model_config->get_data()->auto_shift_flag; // autoシフトフラグ
      if ($auto_shift_flag === 1) {
        $this->CI->load->library('process_rules_lib'); // rules lib 読込
        $rules = $this->CI->process_rules_lib->get_rule($data['user_id']);
        if ($rules) {
          if ($rules->basic_in_time && $rules->basic_rest_weekday) {
            $basic_rest_week = str_split($rules->basic_rest_weekday);
            $this->CI->load->helper('holiday_date');
            $datetime = new DateTimeImmutable($data['dk_date']);
            $holiday_datetime = new HolidayDateTime($data['dk_date']);
            $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
            if ($basic_rest_week[$w] == 0) {
              $shift_in_time = $rules->basic_in_time;
            }
          }
        }
      }
    }
    if (isset($shift_in_time)) {
      // シフトとの比較
      if (strtotime($data['dk_date'].' '.$data['in_work_time']) > strtotime($data['dk_date'].' '.$shift_in_time)) {
        $data['late_hour'] = (strtotime($data['dk_date'].' '.$data['in_work_time']) - strtotime($data['dk_date'].' '.$shift_in_time)) / 60; // 遅刻時間
      } else {
        $data['late_hour'] = 0;
      }
    }
    return $data;
  }

  // 退勤時分析処理
  public function out_time($data)
  {
    // gateway or nonstop から
    if ($data['flag'] === 'gateway' || $data['flag'] === 'nonstop') {
      $data['out_work_time'] = $data['out_time'];
      // ルールの取得
      $this->CI->load->library('process_rules_lib'); // rules lib 読込
      $rules = $this->CI->process_rules_lib->get_rule($data['user_id']);
      // gateway ルール 退勤
      if ($rules && $data['flag'] === 'gateway') {
        
        $out_marume = '';
        $out_marume_flag = (int)$rules->out_marume_flag;
        // 時刻合わせ
        if ($out_marume_flag === 2 || $out_marume_flag === 3) {
          $out_marume_time = $rules->out_marume_time;
          if (strtotime($data['dk_date'].' '.$out_marume_time) <= strtotime($data['dk_date'].' '.$data['out_time'])) {
            $data['out_work_time'] = $out_marume_time;
            $out_marume = 'on';
          }
        }
        // シフト合わせ
        if ($out_marume_flag === 4 || $out_marume_flag === 5) {
          $this->CI->load->model('model_shift');
          $shift_data = $this->CI->model_shift->find_day_userid($data['dk_date'], $data['user_id']); //シフトデータ取得
          if ($shift_data) {
            if ((int)$shift_data->status === 0) {
              $shift_out_time = $shift_data->out_time;
            }
          } else {
            $this->CI->load->model('model_config');
            $auto_shift_flag = (int)$this->CI->model_config->get_data()->auto_shift_flag; // autoシフトフラグ
            if ($auto_shift_flag === 1) {
              if ($rules->basic_out_time && $rules->basic_rest_weekday) {
                $basic_rest_week = str_split($rules->basic_rest_weekday);
                $this->CI->load->helper('holiday_date');
                $datetime = new DateTimeImmutable($data['dk_date']);
                $holiday_datetime = new HolidayDateTime($data['dk_date']);
                $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
                if ($basic_rest_week[$w] == 0) {
                  $shift_out_time = $rules->basic_out_time;
                }
              }
            }
          }
          if ($shift_out_time && strtotime($data['dk_date'].' '.$shift_out_time) <= strtotime($data['dk_date'].' '.$data['out_time'])) {
            $data['out_work_time'] = $shift_out_time;
            $out_marume = 'on';
          }
        }
        // まるめ
        if ($out_marume_flag === 1 || $out_marume_flag === 3 || $out_marume_flag === 5) {
          if ($out_marume === '') {
            $out_time_h = substr($data['out_time'], 0, 2);
            $out_time_m = substr($data['out_time'], 3, 2);
            $out_marume_hour = (int)$rules->out_marume_hour;
            if ($out_marume_hour === 15) { // 15分まるめ
              if ((int)$out_time_m < 15 && (int)$out_time_m != 0) {
                $data['out_work_time'] = $out_time_h.':'.'00:00';
              }
              if ((int)$out_time_m >= 15 && (int)$out_time_m < 30) {
                $data['out_work_time'] = $out_time_h.':15:00';
              }
              if ((int)$out_time_m >= 30 && (int)$out_time_m < 45) {
                $data['out_work_time'] = $out_time_h.':30:00';
              }
              if ((int)$out_time_m >= 45) {
                $data['out_work_time'] = $out_time_h.':45:00';
              }
            }
            if ($out_marume_hour === 30) { // 30分まるめ
              if ((int)$out_time_m < 30 && (int)$out_time_m != 0) {
                $data['out_work_time'] = $out_time_h.':00:00';
              }
              if ((int)$out_time_m >= 30) {
                $data['out_work_time'] = $out_time_h.':30:00';
              }
            }
          }
        }
      }
      // nonstop ルール
      if ($rules && $data['flag'] === 'nonstop') {
        if ($rules->basic_out_time) {
          $data['out_work_time'] = $rules->basic_out_time;
        }
      }
      // ルールなし
      if (!$rules) {
        $data['out_work_time'] = $data['out_time'];
      }
    }

    // 修正以外
    $this->CI->load->model('model_time'); // model time
    $time_data = $this->CI->model_time->get_day_userid($data['dk_date'], $data['user_id']);
    if ($data['flag'] === 'gateway' || $data['flag'] === 'nonstop') {
      // 実 - 労働時間　計算
      $data['in_work_time'] = $time_data->in_work_time;
      $data['in_time'] = $time_data->in_time;
      $in_time = $data['in_time'];
      $out_time = $data['out_time'];
      $in_time_h = (int)substr($data['in_time'], 0, 2);
      if ($in_time_h > 23) {
        $in_time_m = (int)substr($data['in_time'], 3, 2);
        $in_time = $in_time_h - 24 .':00:00';
        $in_min = ($in_time_h - 24) * 60 + $in_time_m;
      } else {
        $in_min = 0;
      }
      $out_time_h = (int)substr($data['out_time'], 0, 2);
      if ($out_time_h > 23) {
        $out_time_m = (int)substr($data['out_time'], 3, 2);
        $out_time = '24:00:00';
        $out_min = ($out_time_h - 24) * 60 + $out_time_m;
      } else {
        $out_min = 0;
      }
      $data['fact_hour'] = (strtotime($data['dk_date'].' '.$out_time) - strtotime($data['dk_date'].' '.$in_time)) / 60;
      $data['fact_hour'] = $data['fact_hour'] - $in_min + $out_min;
    }
    
    // 修正後 - 労働時間　計算
    $in_work_time = $data['in_work_time'];
    $out_work_time = $data['out_work_time'];
    $in_work_time_h = (int)substr($data['in_work_time'], 0, 2);
    if ($in_work_time_h > 23) {
      $in_work_time_m = (int)substr($data['in_work_time'], 3, 2);
      $in_work_time = $in_work_time_h - 24 .':00:00';
      $in_work_min = ($in_work_time_h - 24) * 60 + $in_work_time_m;
    } else {
      $in_work_min = 0;
    }
    $out_work_time_h = (int)substr($data['out_work_time'], 0, 2);
    if ($out_work_time_h > 23) {
      $out_work_time_m = (int)substr($data['out_work_time'], 3, 2);
      $out_work_time = '24:00:00';
      $out_work_min = ($out_work_time_h - 24) * 60 + $out_work_time_m;
    } else {
      $out_work_min = 0;
    }
    $data['fact_work_hour'] = (strtotime($data['dk_date'].' '.$out_work_time) - strtotime($data['dk_date'].' '.$in_work_time)) / 60;
    
    // 休憩処理
    $rest = 0;
    if (isset($data['rest'])) {
      $rest = $data['rest'];
    } else {
      $this->CI->load->model('model_config'); // model config
      $rest_input_flag = (int)$this->CI->model_config->get_data()->rest_input_flag; // 休憩入力フラグ
      if ($rest_input_flag === 0) { // 休憩入力設定がなければ
        $this->CI->load->library('process_rest_lib'); // 休憩　処理用 lib 読込
        $data = $this->CI->process_rest_lib->rest($data); // 休憩　処理lib
        $rest = $data['rest'];
      }
      if ($rest_input_flag === 1) { // 休憩入力設定の場合は、現状の休憩時間を取得する
        $rest = $time_data->rest;
      }
    }
    
    $data['fact_work_hour'] = $data['fact_work_hour'] - $in_work_min + $out_work_min - $rest;
    if ($data['fact_work_hour'] < 0) {
      $data['fact_work_hour'] = 0;
    }
    
    // rules lib 読込
    $this->CI->load->library('process_rules_lib');
    $rules = $this->CI->process_rules_lib->get_rule($data['user_id']);

    // 残業　計算
    $over_limit_hour = 0;
    if ($rules->over_limit_hour) {
      $over_limit_hour = (int)$rules->over_limit_hour;
    }
    if ($over_limit_hour > 0 && $data['fact_work_hour'] > $over_limit_hour) {
      $data['over_hour'] = $data['fact_work_hour'] - $over_limit_hour;
    } else {
      $data['over_hour'] = 0;
    }
    
    // 深夜　計算
    if ((int)substr($out_work_time, 0, 2) >= 22) {
      $data['night_hour'] = (strtotime($data['dk_date'].' '.$out_work_time) - strtotime($data['dk_date'].' 22:00:00')) / 60;
      $data['night_hour'] = $data['night_hour'] + $out_work_min;
    }
    
    // シフトとの比較
    $data['late_hour'] = 0; // 遅刻時間
    $data['left_hour'] = 0; // 早退時間
    $this->CI->load->model('model_shift');
    $shift_data = $this->CI->model_shift->find_day_userid($data['dk_date'], $data['user_id']); //シフトデータ取得
    if ($shift_data) {
      if ((int)$shift_data->status === 0 && $shift_data->in_time) {
        $shift_in_time = $shift_data->in_time;
      }
      if ((int)$shift_data->status === 0 && $shift_data->out_time) {
        $shift_out_time = $shift_data->out_time;
      }
    } else {
      $this->CI->load->model('model_config');
      $auto_shift_flag = (int)$this->CI->model_config->get_data()->auto_shift_flag; // autoシフトフラグ
      if ($auto_shift_flag === 1) {
        if ($rules) {
          if ($rules->basic_in_time && $rules->basic_out_time && $rules->basic_rest_weekday) {
            $basic_rest_week = str_split($rules->basic_rest_weekday);
            $this->CI->load->helper('holiday_date');
            $datetime = new DateTimeImmutable($data['dk_date']);
            $holiday_datetime = new HolidayDateTime($data['dk_date']);
            $holiday_datetime->holiday() ? $w = 7 : $w = $datetime->format('w');
            if ($basic_rest_week[$w] == 0) {
              $shift_in_time = $rules->basic_in_time;
              $shift_out_time = $rules->basic_out_time;
            }
          }
        }
      }
    }
    if (isset($shift_in_time)) {
      if (strtotime($data['dk_date'].' '.$data['in_work_time']) > strtotime($data['dk_date'].' '.$shift_in_time)) {
        $data['late_hour'] = (strtotime($data['dk_date'].' '.$data['in_work_time']) - strtotime($data['dk_date'].' '.$shift_in_time)) / 60; // 遅刻時間
      }
    }
    if (isset($shift_out_time)) {
      if (strtotime($data['dk_date'].' '.$data['out_work_time']) < strtotime($data['dk_date'].' '.$shift_out_time)) {
        $data['left_hour'] = (strtotime($data['dk_date'].' '.$shift_out_time) - strtotime($data['dk_date'].' '.$data['out_work_time'])) / 60; // 早退時間
      }
    }

    // resq
    $this->CI->load->model('model_config'); // model config
    $resq_flag = $this->CI->model_config->get_data()->resq_flag;
    if ((int)$resq_flag === 1) {
      $post = [
        'company_code' => $this->CI->model_config->get_data()->resq_company_code,
        'employee_code' => $data['user_id'],
        'target_date' => $data['dk_date'],
        'working_hours' => $data['fact_work_hour'] / 60
      ];
      $this->CI->load->library('process_resq_lib'); // resQ lib 読込
      $this->CI->process_resq_lib->resq_send($post);
    }
    return $data;
  }
}
