<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Line extends CI_Controller
{
  public function index()
  {
    // config data取得
    $this->load->model('model_config_values');
    $where = [];
    $result = $this->model_config_values->find('config_name, value', $where, '');
    $config_data = array_column($result, 'value', 'config_name');

    if ((int)$config_data['line_flag'] === 0) {
      exit;
    }
    if ($config_data['line_token'] === '' || $config_data['line_token'] === NULL) {
      exit;
    }
    // webhook
    $raw = file_get_contents('php://input');
    $receive = json_decode($raw, true);
    $event = $receive['events'][0];
    $reply_token  = $event['replyToken'];
    $line_id = $event['source']['userId'];
    $message_text = $event['message']['text'];
    $latirude = $event['message']['type'];
    $headers = array('Content-Type: application/json','Authorization: Bearer ' . $config_data['line_token']);
    // line profileを取得
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.line.me/v2/bot/profile/'.$line_id);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $user_profile = curl_exec($curl);
    $user = json_decode($user_profile, true);
    curl_close($curl);

    $line_name = $user['displayName']; // line表示名
    
    $user_name = '';
    $this->load->model('model_user_data');

    // 登録処理
    if (mb_strpos($message_text, '登録') !== false) {
      $user_id = mb_substr($message_text, 2); // user_idの取り出し
      if ($user_id !== '') {
        mb_convert_kana($user_id, 'n');
        $id = $this->model_user_data->find_row('id', ['user_id'=> (int)$user_id])->id;
        if ((int)$id > 0) {
          $data = [
            'line_id' => $line_id,
            'line_name' => $line_name
          ];
          $this->model_user_data->update($id, $data);
        }
      }
    }
    
    // 登録確認
    $select = 'name_sei, name_mei';
    $where = ['line_id'=>$line_id];
    $userdata = $this->model_user_data->find_row($select, $where);
    if ($userdata) {
      $user_name = $userdata->name_sei.' '.$userdata->name_mei;
    }

    // put message
    if ($user_name === '') {
      $message = [
        'type' => 'text',
        'text' => $line_name."さん\r打刻keeperメンバー登録がされてません。"
      ];
    } else {
      $message = [
        'type' => 'text',
        'text' => 'こんにちは。'.$user_name.'さん'
      ];
    }

    $body = json_encode(array('replyToken' => $reply_token, 'messages' => array($message)));
    $options = array(CURLOPT_URL            => 'https://api.line.me/v2/bot/message/reply',
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER     => $headers,
                    CURLOPT_POSTFIELDS     => $body);

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    curl_exec($curl);
    curl_close($curl);
  }
}
