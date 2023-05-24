<?php
defined('BASEPATH') or exit('No direct script access alllowed');

class Model_config extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 通常データ取得用
    public function get_data()
    {
        return $this->db->get('config')->row();
    }

    // 登録　update
    public function update_data($data)
    {
        if (isset($data['sound_flag'])) {
            $this->db->set('sound_flag', $data['sound_flag']);
        }
        if (isset($data['id_size'])) {
            $this->db->set('id_size', $data['id_size']);
        }
        if (isset($data['company_name'])) {
            $this->db->set('company_name', $data['company_name']);
        }
        if (isset($data['system_mail1'])) {
            $this->db->set('system_mail1', $data['system_mail1']);
        }
        if (isset($data['system_mail2'])) {
            $this->db->set('system_mail2', $data['system_mail2']);
        }
        if (isset($data['memo_open_flag'])) {
            $this->db->set('memo_open_flag', $data['memo_open_flag']);
        }
        if (isset($data['gateway_mail_flag'])) {
            $this->db->set('gateway_mail_flag', $data['gateway_mail_flag']);
        }
        if (isset($data['over_time_flag'])) {
            $this->db->set('over_time_flag', $data['over_time_flag']);
        }
        if (isset($data['night_time_flag'])) {
            $this->db->set('night_time_flag', $data['night_time_flag']);
        }
        if (isset($data['aporan_flag'])) {
            $this->db->set('aporan_flag', $data['aporan_flag']);
        }
        if (isset($data['download_filetype'])) {
            $this->db->set('download_filetype', $data['download_filetype']);
        }
        if (isset($data['revision_flag'])) {
            $this->db->set('revision_flag', $data['revision_flag']);
        }
        if (isset($data['system_id'])) {
            $this->db->set('system_id', $data['system_id']);
        }
        if (isset($data['line_flag'])) {
            $this->db->set('line_flag', $data['line_flag']);
        }
        if (isset($data['line_token'])) {
            $this->db->set('line_token', $data['line_token']);
        }
        if (isset($data['advance_pay_flag'])) {
            $this->db->set('advance_pay_flag', $data['advance_pay_flag']);
        }
        if (isset($data['notice_mail_flag'])) {
            $this->db->set('notice_mail_flag', $data['notice_mail_flag']);
        }
        if (isset($data['notice_mailaddress1'])) {
            $this->db->set('notice_mailaddress1', $data['notice_mailaddress1']);
        }
        if (isset($data['notice_mailaddress2'])) {
            $this->db->set('notice_mailaddress2', $data['notice_mailaddress2']);
        }
        if (isset($data['notice_mailaddress3'])) {
            $this->db->set('notice_mailaddress3', $data['notice_mailaddress3']);
        }
        if (isset($data['notice_mailaddress4'])) {
            $this->db->set('notice_mailaddress4', $data['notice_mailaddress4']);
        }
        if (isset($data['notice_mailaddress4'])) {
            $this->db->set('notice_mailaddress4', $data['notice_mailaddress4']);
        }
        if (isset($data['over_day'])) {
            $this->db->set('over_day', $data['over_day']);
        }
        if (isset($data['rest_input_flag'])) {
            $this->db->set('rest_input_flag', $data['rest_input_flag']);
        }
        if (isset($data['gps_flag'])) {
            $this->db->set('gps_flag', $data['gps_flag']);
        }
        if (isset($data['qrcode_flag'])) {
            $this->db->set('qrcode_flag', $data['qrcode_flag']);
        }
        if (isset($data['area_flag'])) {
            $this->db->set('area_flag', $data['area_flag']);
        }
        if (isset($data['end_day'])) {
            $this->db->set('end_day', $data['end_day']);
        }
        if (isset($data['resq_flag'])) {
            $this->db->set('resq_flag', $data['resq_flag']);
        }
        if (isset($data['resq_company_code'])) {
            $this->db->set('resq_company_code', $data['resq_company_code']);
        }
        if (isset($data['mypage_flag'])) {
            $this->db->set('mypage_flag', $data['mypage_flag']);
        }
        if (isset($data['mypage_input_flag'])) {
            $this->db->set('mypage_input_flag', $data['mypage_input_flag']);
        }
        if (isset($data['mypage_profile_edit_flag'])) {
            $this->db->set('mypage_profile_edit_flag', $data['mypage_profile_edit_flag']);
        }
        if (isset($data['mypage_password_edit_flag'])) {
            $this->db->set('mypage_password_edit_flag', $data['mypage_password_edit_flag']);
        }
        if (isset($data['mypage_end_day'])) {
            $this->db->set('mypage_end_day', $data['mypage_end_day']);
        }
        if (isset($data['mypage_user_edit_flag'])) {
            $this->db->set('mypage_user_edit_flag', $data['mypage_user_edit_flag']);
        }
        if (isset($data['minute_time_flag'])) {
            $this->db->set('minute_time_flag', $data['minute_time_flag']);
        }
        if (isset($data['normal_time_flag'])) {
            $this->db->set('normal_time_flag', $data['normal_time_flag']);
        }
        if (isset($data['mypage_my_inout_view_flag'])) {
            $this->db->set('mypage_my_inout_view_flag', $data['mypage_my_inout_view_flag']);
        }
        if (isset($data['mypage_status_inout_view_flag'])) {
            $this->db->set('mypage_status_inout_view_flag', $data['mypage_status_inout_view_flag']);
        }
        if (isset($data['mypage_status_view_flag'])) {
            $this->db->set('mypage_status_view_flag', $data['mypage_status_view_flag']);
        }
        if (isset($data['gateway_status_view_flag'])) {
            $this->db->set('gateway_status_view_flag', $data['gateway_status_view_flag']);
        }
        if (isset($data['shift_view_flag'])) {
            $this->db->set('shift_view_flag', $data['shift_view_flag']);
        }
        if (isset($data['shift_cal_first_day'])) {
            $this->db->set('shift_cal_first_day', $data['shift_cal_first_day']);
        }
        $this->db->where('id', 1);
        $ret = $this->db->update('config');
        if ($ret === false) {
            return false;
        }
        return true;
    }
}
