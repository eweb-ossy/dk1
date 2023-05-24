<?php
/**
*   ダウンロードデータを取得しダウンロード用ファイルを生成
*
*   @copyright  e-web,Inc.
*   @author     oshizawa
*/

defined('BASEPATH') or exit('No direct script access alllowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Admin_download extends CI_Controller
{
    /**
     *   EXCEL用ダウンロードファイルを生成
    *
    *   @param $column 
    *   @param $dl_data
    *   @param $data_date
    *   @param $type 
    */
    public function xlsx()
    {
        // // config data取得
        // $this->load->model('model_config_values');
        // $where = [];
        // $result = $this->model_config_values->find('id, config_name, value', $where, '');
        // $config_data = array_column($result, 'value', 'config_name');

        $column = json_decode($this->input->post('column'), true);
        $dl_data = json_decode($this->input->post('dl_data'), true);
        $data_date = $this->input->post('data_date');
        $type = $this->input->post('type');
        $all_data = json_decode($this->input->post('all_data'), true);

        $this->load->helper('file');
        delete_files('./files/');

        $arrayData = [];

        if ($type === 'day' || $type === 'user_detail') { // コラムグループ対策
            foreach ($column as $value) {
                if (isset($value['title'])) {
                    if (isset($value['columns'])) {
                        foreach ($value['columns'] as $val) {
                            $columnData[] = $val['title'];
                        }
                    } elseif ($value['title']) {
                        $columnData[] = $value['title'];
                    }
                }
            }
        } else { // 通常時
            $columnData = array_column($column, 'title');
            $keys = array_keys($column);
        }

        array_push($arrayData, $columnData);

        foreach ($dl_data as $key => $value) {
            $data = [];
            for ($i = 0; $i < count($columnData); $i++) {
                if (current(array_slice($value, $i, 1, true))) {
                    $data[] = current(array_slice($value, $i, 1, true));
                } else {
                    $data[] = "";
                }
            }
            array_push($arrayData, $data);
        }

        if ($all_data) {
            $all_data = array_intersect_key($all_data, $dl_data[0]);
            array_push($arrayData, array_values($all_data));
        }

        // excelファイル生成
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        switch ($type) {
            case 'day':
                $type_name = '日別集計';
                break;
            case 'month':
                $type_name = '月別集計';
                break;
            case 'list_user':
                $type_name = '従業員別集計';
                break;
            case 'user_detail':
                $type_name = '従業員別集計（個人）';
                break;
            case 'users':
                $type_name = '従業員一覧';
                $data_date = '';
                break;
            default:
                $type_name = '';
                break;
        }
        $sheet->setCellValue('A1', $type_name.' '.$data_date); // シート内タイトル
        $file_name = $type_name.$data_date; // 出力ファイル名

        $sheet->fromArray($arrayData, null, 'A3'); // 内容生成

        $writer = new Xlsx($spreadsheet);

        $writer->save('./files/'.$file_name.'.xlsx');

        $this->load->helper('download');
        force_download('./files/'.$file_name.'.xlsx', null);
    }

    /**
     *   PDF用ダウンロードファイルを生成
    *
    *   @param $column 
    *   @param $dl_data
    *   @param $data_date
    *   @param $type 
    */
    public function pdf()
    {
        // // config data取得
        // $this->load->model('model_config_values');
        // $where = [];
        // $result = $this->model_config_values->find('id, config_name, value', $where, '');
        // $config_data = array_column($result, 'value', 'config_name');

        $column = json_decode($this->input->post('column'), true); // タイトルデータ
        $dl_data = json_decode($this->input->post('dl_data'), true); // データ
        $data_date = $this->input->post('data_date');
        $type = $this->input->post('type');
        $all_data = json_decode($this->input->post('all_data'), true);

        $arrayData = [];

        if ($type === 'day' || $type === 'user_detail') { // コラムグループ対策
            foreach ($column as $value) {
                if (isset($value['title'])) {
                    if (isset($value['columns'])) {
                        foreach ($value['columns'] as $val) {
                            $columnData[] = $val['title'];
                        }
                    } elseif ($value['title']) {
                        $columnData[] = $value['title'];
                    }
                }
            }
        } else { // 通常時
            $columnData = array_column($column, 'title');
        }

        array_push($arrayData, $columnData);

        foreach ($dl_data as $key => $value) {
            $data = [];
            for ($i = 0; $i < count($columnData); $i++) {
                if (current(array_slice($value, $i, 1, true))) {
                    $data[] = current(array_slice($value, $i, 1, true));
                } else {
                    $data[] = "";
                }
            }
            array_push($arrayData, $data);
        }

        if ($all_data) {
            $all_data = array_intersect_key($all_data, $dl_data[0]);
            array_push($arrayData, array_values($all_data));
        }

        // pdfファイル生成
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        // ページヘッダー・フッダー出力の有無
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        $pdf->AddPage();

        switch ($type) {
            case 'day':
                $type_name = '日別集計';
                $file_name = 'list_day'.$data_date;
                break;
            case 'month':
                $type_name = '月別集計';
                $file_name = 'list_month'.$data_date;
                break;
            case 'list_user':
                $type_name = '従業員別集計';
                $file_name = 'list_users'.$data_date;
                break;
            case 'user_detail':
                $type_name = '従業員別集計（個人）';
                $file_name = 'list_user'.$data_date;
                break;
            case 'users':
                $type_name = '従業員一覧';
                $data_date = '';
                $file_name = 'users';
                break;
            default:
                $type_name = '';
                break;
        }

        $pdf->SetFont('kozgopromedium', 'B', 7);
        $pdf->MultiCell(0, 4, $type_name.' '.$data_date, 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'M', false); // 内容タイトル

        $w = 277 / count($columnData); // A4サイズ横幅297mm - margin20mm
        $pdf->SetFont('kozgopromedium', '', 7);
        foreach ($arrayData as $value) {
            for ($i = 0; $i < count($columnData); $i++) {
                if (isset($value[$i])) {
                    $val = $value[$i];
                } else {
                    $val = '';
                }
                if ($i === count($columnData)-1) {
                    $pdf->MultiCell($w, 5, $val, 1, 'L', false, 1, '', '', true, 0, false, true, 0, 'M', true);
                } else {
                    $pdf->MultiCell($w, 5, $val, 1, 'L', false, 0, '', '', true, 0, false, true, 0, 'M', true);
                }
            }
        }

        $pdf->Output($file_name.'.pdf', 'D');
        exit;

        // ALL　合計表示用　作成
        if (isset($all_shift_hour)) {
            $shift_all_w = '予定出勤数：'.count($all_shift_hour).'日　予定出勤時間：'.sprintf('%d:%02d', floor(array_sum($all_shift_hour)/60), array_sum($all_shift_hour)%60).'　';
        } else {
            $shift_all_w = '';
        }
        if (isset($all_work_hour)) {
            $work_all_w = '労働日数：'.count($all_work_hour).'日　総労働時間：'.sprintf('%d:%02d', floor(array_sum($all_work_hour)/60), array_sum($all_work_hour)%60).'　';
        } else {
            $work_all_w = '';
        }
        if (isset($all_rest_hour)) {
            $rest_all_w = '休憩日数：'.count($all_rest_hour).'日　総休憩時間：'.sprintf('%d:%02d', floor(array_sum($all_rest_hour)/60), array_sum($all_rest_hour)%60).'　';
        } else {
            $rest_all_w = '';
        }
        if (isset($all_over_hour)) {
            $over_all_w = '残業日数：'.count($all_over_hour).'日　総残業時間：'.sprintf('%d:%02d', floor(array_sum($all_over_hour)/60), array_sum($all_over_hour)%60).'　';
        } else {
            $over_all_w = '';
        }
        if (isset($all_night_hour)) {
            $night_all_w = '深夜日数：'.count($all_night_hour).'日　総深夜時間：'.sprintf('%d:%02d', floor(array_sum($all_night_hour)/60), array_sum($all_night_hour)%60).'　';
        } else {
            $night_all_w = '';
        }
        if (isset($all_late_hour)) {
            $late_all_w = '遅刻日数：'.count($all_late_hour).'日　総遅刻時間：'.sprintf('%d:%02d', floor(array_sum($all_late_hour)/60), array_sum($all_late_hour)%60).'　';
        } else {
            $late_all_w = '';
        }
        if (isset($all_left_hour)) {
            $left_all_w = '早退日数：'.count($all_left_hour).'日　総早退時間：'.sprintf('%d:%02d', floor(array_sum($all_left_hour)/60), array_sum($all_left_hour)%60).'　';
        } else {
            $left_all_w = '';
        }

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        // ページヘッダー・フッダー出力の有無
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        $pdf->SetFont('kozgopromedium', '', 6);
        $pdf->SetLineWidth(0.05);

        // void Cell(float $w, [float $h = 0], [string $txt = ''], [mixed $border = 0], [int $ln = 0], [string $align = ''], [int $fill = 0], [mixed $link = ''], [int $stretch = 0], [boolean $ignore_min_height = FALSE])
        // $w 幅
        // $h 高さ
        // $text テキスト
        // $border 線　0=なし　1=あり
        // $ln 移動　0=右　1=改行　2=下
        // $align テキスト位置　L=左揃え　C=中央揃え　R=右揃え　j=両端揃え
        // $fill 塗りつぶし　0=透明　1=塗りつぶす
        // $link リンク　リンク先URL
        // $strech テキストの伸縮モード　0=なし　1=必要に応じて水平伸縮　2=水平伸縮　3=必要に応じてスペース埋め　4=スペース埋め
        // $ignore_min_height trueにすると短形領域の高さの最小値調整をしない
        $h = 4;
        if ($type === 'day') {
            $w = 15;
            $file_name = 'list_day'.$data_date;
            $pdf->Cell($w, $h, '日別集計 '.$data_date, 0, 1, 'L', 0, '', 0, false);
        }
        if ($type === 'month') {
            $w = 7;
            $file_name = 'list_month'.$data_date;
            $pdf->Cell($w, $h, '月別集計 '.$data_date, 0, 1, 'L', 0, '', 0, false);
        }
        if ($type === 'user_detail') {
            $w = 13;
            $file_name = 'list_user'.$data_date;
            $pdf->Cell($w, $h, $data_date, 0, 1, 'L', 0, '', 0, false);
        }
        if ($type === 'list_user') {
            $w = 15;
            $file_name = 'list_users'.$data_date;
            $pdf->Cell($w, $h, '従業員別集計 '.$data_date, 0, 1, 'L', 0, '', 0, false);
        }
        $border = 1;
        $ln = 0;
        $align = 'L';
        $fill = 0;
        $link =  '';
        $strech = 1;
        foreach ($arrayData as $value) {
            for ($i = 0; $i < count($column); $i++) {
                if (isset($value[$i])) {
                    $val = $value[$i];
                } else {
                    $val = '';
                }
                if ($i === count($column)-1) {
                    $pdf->Cell($w, $h, $val, 1, 1, $align, $fill, $link, $strech, false);
                } else {
                    $pdf->Cell($w, $h, $val, 1, $ln, $align, $fill, $link, $strech, false);
                }
            }
        }
        if ($type === 'user_detail') { // 従業員　月一覧
            $pdf->Cell(300, 4, $shift_all_w.$work_all_w.$rest_all_w.$over_all_w.$night_all_w.$late_all_w.$left_all_w, 0, 1, $align, $fill, $link, $strech, false); // ALL 合計表示　出力
            if (isset($area_data)) { // エリアデータがある場合は、エリア別出力
                foreach ($area_data as $key => $value) {
                    $pdf->Cell(300, 4, $key, 0, 1, $align, $fill, $link, $strech, false); // エリア名出力
                    for ($i = 0; $i < count($columnData); $i++) { // コラム出力
                        if ($i === count($columnData)-1) {
                            $pdf->Cell($w, $h, $columnData[$i], 1, 1, $align, $fill, $link, $strech, false);
                        } else {
                            $pdf->Cell($w, $h, $columnData[$i], 1, $ln, $align, $fill, $link, $strech, false);
                        }
                    }
                    foreach ($value as $data) { // エリアデータ出力
                        for ($i = 0; $i < count($columnData); $i++) {
                            if ($i === count($columnData)-1) {
                                $pdf->Cell($w, $h, $data[$i], 1, 1, $align, $fill, $link, $strech, false);
                            } else {
                                $pdf->Cell($w, $h, $data[$i], 1, $ln, $align, $fill, $link, $strech, false);
                            }
                        }
                    }
                    if (isset($area_shift_hour[$key])) { // エリア別　合計表示
                        $area_shift_all_w = '予定出勤数：'.count($area_shift_hour[$key]).'日　予定出勤時間：'.sprintf('%d:%02d', floor(array_sum($area_shift_hour[$key])/60), array_sum($area_shift_hour[$key])%60).'　';
                    } else {
                        $area_shift_all_w = '';
                    }
                    if (isset($area_work_hour[$key])) {
                        $area_work_all_w = '労働日数：'.count($area_work_hour[$key]).'日　総労働時間：'.sprintf('%d:%02d', floor(array_sum($area_work_hour[$key])/60), array_sum($area_work_hour[$key])%60).'　';
                    } else {
                        $area_work_all_w = '';
                    }
                    if (isset($area_rest_hour[$key])) {
                        $area_rest_all_w = '休憩日数：'.count($area_rest_hour[$key]).'日　総休憩時間：'.sprintf('%d:%02d', floor(array_sum($area_rest_hour[$key])/60), array_sum($area_rest_hour[$key])%60).'　';
                    } else {
                        $area_rest_all_w = '';
                    }
                    if (isset($area_over_hour[$key])) {
                        $area_over_all_w = '残業日数：'.count($area_over_hour[$key]).'日　総残業時間：'.sprintf('%d:%02d', floor(array_sum($area_over_hour[$key])/60), array_sum($area_over_hour[$key])%60).'　';
                    } else {
                        $area_over_all_w = '';
                    }
                    if (isset($area_night_hour[$key])) {
                        $area_night_all_w = '深夜日数：'.count($area_night_hour[$key]).'日　総深夜時間：'.sprintf('%d:%02d', floor(array_sum($area_night_hour[$key])/60), array_sum($area_night_hour[$key])%60).'　';
                    } else {
                        $area_night_all_w = '';
                    }
                    if (isset($area_late_hour[$key])) {
                        $area_late_all_w = '遅刻日数：'.count($area_late_hour[$key]).'日　総遅刻時間：'.sprintf('%d:%02d', floor(array_sum($area_late_hour[$key])/60), array_sum($area_late_hour[$key])%60).'　';
                    } else {
                        $area_late_all_w = '';
                    }
                    if (isset($area_left_hour[$key])) {
                        $area_left_all_w = '早退日数：'.count($area_left_hour[$key]).'日　総早退時間：'.sprintf('%d:%02d', floor(array_sum($area_left_hour[$key])/60), array_sum($area_left_hour[$key])%60).'　';
                    } else {
                        $area_left_all_w = '';
                    }
                    $pdf->Cell(300, 4, $area_shift_all_w.$area_work_all_w.$area_rest_all_w.$area_over_all_w.$area_night_all_w.$area_late_all_w.$area_left_all_w, 0, 1, $align, $fill, $link, $strech, false); // エリア別　合計表示　出力
                }
            }
        }
        $pdf->Output($file_name.'.pdf', 'D');
    }
}
