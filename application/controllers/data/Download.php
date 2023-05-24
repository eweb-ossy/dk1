<?php 
defined('BASEPATH') or exit('No direct script access alllowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Download extends CI_Controller
{
    public function excel()
    {
        $title = $this->input->post('title');
        $columns = json_decode($this->input->post('columns'), true);
        $data = json_decode($this->input->post('data'), true);
        $column_title_top = [];
        $column_title = [];
        $column_field = [];
        foreach ($columns as $key => $value) {
            $groups = isset($value['columns']) ? count($value['columns']) : 0;
            if ($groups > 0) {
                foreach ($value['columns'] as $i => $val) {
                    $column_title_top[] = $i === 0 ? $value['title'] : '';
                    $column_title[] = $val['title'];
                    $column_field[] = $val['field'];
                }
            } else {
                $column_title_top[] = '';
                $column_title[] = $value['title'];
                $column_field[] = $value['field'];
            }
        }
        // $column_title = array_column($columns, 'title');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($column_title_top, NULL, 'A1'); // コラム描画
        $sheet->fromArray($column_title, NULL, 'A2'); // コラム描画
        $views = [];
        // $column_field = array_column($columns, 'field');
        foreach ($data as $row => $value) {
            foreach ($column_field as $i => $field) {
                foreach ($value as $key => $item) {
                    if ($key === $field) {
                        $views[$row][$i] = $item ?: '';
                    }
                }
            }
        }
        $sheet->fromArray($views, NULL, 'A3'); // データ描画
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function pdf()
    {
        $title = $this->input->post('title');
        $file_name = mb_convert_encoding( $title.'.pdf', 'SJIS-WIN', 'UTF-8' );
        $columns = json_decode($this->input->post('columns'), true);
        $data = json_decode($this->input->post('data'), true);
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();
        $column_num = count($columns); // コラム数
        $column_field = array_column($columns, 'field');
        $num = 1;
        $w = 277 / $column_num; // A4サイズ横幅297mm - margin20mm
        $pdf->SetFont('kozgopromedium', '', 7);

        function viewColumn($columns, $pdf, $w) { // コラム描画
            $pdf->SetFillColor(230, 230, 230); // 背景色
            foreach ($columns as $value) {
                $rn = $value === end($columns) ? 1 : 0;
                $pdf->MultiCell($w, 5, $value['title'], 1, 'L', true, $rn, '', '', true, 0, false, true, 0, 'M', true);
            }
            unset($value);
        }
        
        viewColumn($columns, $pdf, $w);
        
        foreach ($data as $value) { // データ描画
            if ($num % 2 === 0) {
                $pdf->SetFillColor(245, 250, 255); // 背景色
            } else {
                $pdf->SetFillColor(255, 255, 255); // 背景色
            }
            $views = [];
            foreach ($column_field as $i => $field) {
                foreach ($value as $key => $item) {
                    if ($key === $field) {
                        $views[$i] = $item ?: '';
                    }
                }
            }
            foreach ($views as $key => $view) {
                $rn = $key === $column_num-1 ? 1 : 0;
                $pdf->MultiCell($w, 5, $view, 1, 'L', true, $rn, '', '', true, 0, false, true, 0, 'M', true);
            }
            $num++;
            if ($num === 37) { // 改ページ
                $pdf->AddPage();
                viewColumn($columns, $pdf, $w); // -> コラム描画
                $num = 1;
            }
        }
        
        $pdf_output = $pdf->Output('file.pdf', 'S');
        header( "Pragma: public" );
        header( "Expires: 0 ");
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Content-Transfer-Encoding: binary" );
        header( "Content-Type: application/octet-streams" );
        header( "Content-Disposition: attachment; filename=\"{$file_name}\"" );
        print $pdf_output;
        exit;
    }

    public function pdf_pay()
    {
        $columns = json_decode($this->input->post('columns'), true);
        $data = json_decode($this->input->post('data'), true);

        $this->load->database(); // 給与明細ダウンロード日時　保存
        $this->db->where('id', $data['id']);
        $now = new DateTime();
        $this->db->update('payment_data', ['download'=> $now->format('Y-m-d H:i:s')]);

        $name = $data['name'];
        $year = (int)$data['year'];
        $month = $data['month'];
        if ($year > 2019) {
            $year = $year - 2019 + 1;
            $wareki = "令和{$year}";
        } elseif ($year > 1989) {
            $year = $year - 1989 + 1;
            $wareki = "平成{$year}";
        }
        $view_title = $columns['title']['title']; // タイトル
        $view_title = str_replace('{name}', $name, $view_title);
        $view_title = str_replace('{wareki}', $wareki, $view_title);
        $view_title = str_replace('{month}', $month, $view_title);

        $file_name = mb_convert_encoding( $view_title.'.pdf', 'SJIS-WIN', 'UTF-8' );

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        
        $pdf->SetFont('kozgopromedium', 'B', 12); // タイトル
        $pdf->text(10, 13, $view_title);

        // 勤怠
        $pdf->SetFont('kozgopromedium', '', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(41, 88, 114);
        $pdf->SetDrawColor(41, 88, 144);
        $pdf->MultiCell(8, 28, $columns['work']['title'], 1, 'C', true, 1, 10, 20, true, 0, false, true, 0, 'M', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(186, 208, 235);
        $pdf->MultiCell(25, 7, $data['work1'] ? $columns['work1']['title'] : '', 1, 'L', true, 1, 18, 20, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work2'] ? $columns['work2']['title'] : '', 1, 'L', true, 1, 43, 20, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work3'] ? $columns['work3']['title'] : '', 1, 'L', true, 1, 68, 20, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work4'] ? $columns['work4']['title'] : '', 1, 'L', true, 1, 93, 20, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work5'] ? $columns['work5']['title'] : '', 1, 'L', true, 1, 118, 20, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work6'] ? $columns['work6']['title'] : '', 1, 'L', true, 1, 143, 20, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work7'] ? $columns['work7']['title'] : '', 1, 'L', true, 1, 168, 20, true, 0, false, true, 0, 'M', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(25, 7, $data['work1'] ?: ' ', 1, 'R', true, 1, 18, 27, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work2'] ?: ' ', 1, 'R', true, 1, 43, 27, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work3'] ?: ' ', 1, 'R', true, 1, 68, 27, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work4'] ?: ' ', 1, 'R', true, 1, 93, 27, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work5'] ?: ' ', 1, 'R', true, 1, 118, 27, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work6'] ?: ' ', 1, 'R', true, 1, 143, 27, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work7'] ?: ' ', 1, 'R', true, 1, 168, 27, true, 0, false, true, 0, 'M', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(186, 208, 235);
        $pdf->MultiCell(25, 7, $data['work8'] ? $columns['work8']['title'] : '', 1, 'L', true, 1, 18, 34, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work9'] ? $columns['work9']['title'] : '', 1, 'L', true, 1, 43, 34, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work10'] ? $columns['work10']['title'] : '', 1, 'L', true, 1, 68, 34, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work11'] ? $columns['work11']['title'] : '', 1, 'L', true, 1, 93, 34, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work12'] ? $columns['work12']['title'] : '', 1, 'L', true, 1, 118, 34, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work13'] ? $columns['work13']['title'] : '', 1, 'L', true, 1, 143, 34, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work14'] ? $columns['work14']['title'] : '', 1, 'L', true, 1, 168, 34, true, 0, false, true, 0, 'M', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(25, 7, $data['work8'] ?: ' ', 1, 'R', true, 1, 18, 41, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work9'] ?: ' ', 1, 'R', true, 1, 43, 41, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work10'] ?: ' ', 1, 'R', true, 1, 68, 41, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work11'] ?: ' ', 1, 'R', true, 1, 93, 41, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work12'] ?: ' ', 1, 'R', true, 1, 118, 41, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work13'] ?: ' ', 1, 'R', true, 1, 143, 41, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['work14'] ?: ' ', 1, 'R', true, 1, 168, 41, true, 0, false, true, 0, 'M', true);

        // 支給
        $pdf->SetFont('kozgopromedium', '', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(41, 88, 114);
        $pdf->SetDrawColor(41, 88, 144);
        $pdf->MultiCell(8, 28, $columns['pay']['title'], 1, 'C', true, 1, 10, 50, true, 0, false, true, 0, 'M', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(186, 208, 235);
        $pdf->MultiCell(25, 7, $data['pay1'] ? $columns['pay1']['title'] : '', 1, 'L', true, 1, 18, 50, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay2'] ? $columns['pay2']['title'] : '', 1, 'L', true, 1, 43, 50, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay3'] ? $columns['pay3']['title'] : '', 1, 'L', true, 1, 68, 50, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay4'] ? $columns['pay4']['title'] : '', 1, 'L', true, 1, 93, 50, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay5'] ? $columns['pay5']['title'] : '', 1, 'L', true, 1, 118, 50, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay6'] ? $columns['pay6']['title'] : '', 1, 'L', true, 1, 143, 50, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay7'] ? $columns['pay7']['title'] : '', 1, 'L', true, 1, 168, 50, true, 0, false, true, 0, 'M', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(25, 7, $data['pay1'] ? number_format($data['pay1']) : ' ', 1, 'R', true, 1, 18, 57, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay2'] ? number_format($data['pay2']) : ' ', 1, 'R', true, 1, 43, 57, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay3'] ? number_format($data['pay3']) : ' ', 1, 'R', true, 1, 68, 57, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay4'] ? number_format($data['pay4']) : ' ', 1, 'R', true, 1, 93, 57, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay5'] ? number_format($data['pay5']) : ' ', 1, 'R', true, 1, 118, 57, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay6'] ? number_format($data['pay6']) : ' ', 1, 'R', true, 1, 143, 57, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay7'] ? number_format($data['pay7']) : ' ', 1, 'R', true, 1, 168, 57, true, 0, false, true, 0, 'M', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(186, 208, 235);
        $pdf->MultiCell(25, 7, $data['pay8'] ? $columns['pay8']['title'] : '', 1, 'L', true, 1, 18, 64, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay9'] ? $columns['pay9']['title'] : '', 1, 'L', true, 1, 43, 64, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay10'] ? $columns['pay10']['title'] : '', 1, 'L', true, 1, 68, 64, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay11'] ? $columns['pay11']['title'] : '', 1, 'L', true, 1, 93, 64, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay12'] ? $columns['pay12']['title'] : '', 1, 'L', true, 1, 118, 64, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay13'] ? $columns['pay13']['title'] : '', 1, 'L', true, 1, 143, 64, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay14'] ? $columns['pay14']['title'] : '', 1, 'L', true, 1, 168, 64, true, 0, false, true, 0, 'M', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(25, 7, $data['pay8'] ? number_format($data['pay8']) : ' ', 1, 'R', true, 1, 18, 71, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay9'] ? number_format($data['pay9']) : ' ', 1, 'R', true, 1, 43, 71, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay10'] ? number_format($data['pay10']) : ' ', 1, 'R', true, 1, 68, 71, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay11'] ? number_format($data['pay11']) : ' ', 1, 'R', true, 1, 93, 71, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay12'] ? number_format($data['pay12']) : ' ', 1, 'R', true, 1, 118, 71, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay13'] ? number_format($data['pay13']) : ' ', 1, 'R', true, 1, 143, 71, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['pay14'] ? number_format($data['pay14']) : ' ', 1, 'R', true, 1, 168, 71, true, 0, false, true, 0, 'M', true);

        // 控除
        $pdf->SetFont('kozgopromedium', '', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(41, 88, 114);
        $pdf->SetDrawColor(41, 88, 144);
        $pdf->MultiCell(8, 28, $columns['deduct']['title'], 1, 'C', true, 1, 10, 80, true, 0, false, true, 0, 'M', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(186, 208, 235);
        $pdf->MultiCell(25, 7, $data['deduct1'] ? $columns['deduct1']['title'] : '', 1, 'L', true, 1, 18, 80, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct2'] ? $columns['deduct2']['title'] : '', 1, 'L', true, 1, 43, 80, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct3'] ? $columns['deduct3']['title'] : '', 1, 'L', true, 1, 68, 80, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct4'] ? $columns['deduct4']['title'] : '', 1, 'L', true, 1, 93, 80, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct5'] ? $columns['deduct5']['title'] : '', 1, 'L', true, 1, 118, 80, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct6'] ? $columns['deduct6']['title'] : '', 1, 'L', true, 1, 143, 80, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct7'] ? $columns['deduct7']['title'] : '', 1, 'L', true, 1, 168, 80, true, 0, false, true, 0, 'M', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(25, 7, $data['deduct1'] ? number_format($data['deduct1']) : ' ', 1, 'R', true, 1, 18, 87, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct2'] ? number_format($data['deduct2']) : ' ', 1, 'R', true, 1, 43, 87, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct3'] ? number_format($data['deduct3']) : ' ', 1, 'R', true, 1, 68, 87, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct4'] ? number_format($data['deduct4']) : ' ', 1, 'R', true, 1, 93, 87, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct5'] ? number_format($data['deduct5']) : ' ', 1, 'R', true, 1, 118, 87, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct6'] ? number_format($data['deduct6']) : ' ', 1, 'R', true, 1, 143, 87, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct7'] ? number_format($data['deduct7']) : ' ', 1, 'R', true, 1, 168, 87, true, 0, false, true, 0, 'M', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(186, 208, 235);
        $pdf->MultiCell(25, 7, $data['deduct8'] ? $columns['deduct8']['title'] : '', 1, 'L', true, 1, 18, 94, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct9'] ? $columns['deduct9']['title'] : '', 1, 'L', true, 1, 43, 94, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct10'] ? $columns['deduct10']['title'] : '', 1, 'L', true, 1, 68, 94, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct11'] ? $columns['deduct11']['title'] : '', 1, 'L', true, 1, 93, 94, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct12'] ? $columns['deduct12']['title'] : '', 1, 'L', true, 1, 118, 94, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct13'] ? $columns['deduct13']['title'] : '', 1, 'L', true, 1, 143, 94, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct14'] ? $columns['deduct14']['title'] : '', 1, 'L', true, 1, 168, 94, true, 0, false, true, 0, 'M', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(25, 7, $data['deduct8'] ? number_format($data['deduct8']) : ' ', 1, 'R', true, 1, 18, 101, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct9'] ? number_format($data['deduct9']) : ' ', 1, 'R', true, 1, 43, 101, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct10'] ? number_format($data['deduct10']) : ' ', 1, 'R', true, 1, 68, 101, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct11'] ? number_format($data['deduct11']) : ' ', 1, 'R', true, 1, 93, 101, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct12'] ? number_format($data['deduct12']) : ' ', 1, 'R', true, 1, 118, 101, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct13'] ? number_format($data['deduct13']) : ' ', 1, 'R', true, 1, 143, 101, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['deduct14'] ? number_format($data['deduct14']) : ' ', 1, 'R', true, 1, 168, 101, true, 0, false, true, 0, 'M', true);

        // 合計
        $pdf->SetFont('kozgopromedium', '', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(41, 88, 114);
        $pdf->SetDrawColor(41, 88, 144);
        $pdf->MultiCell(8, 14, $columns['total']['title'], 1, 'C', true, 1, 10, 110, true, 0, false, true, 0, 'M', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(186, 208, 235);
        $pdf->MultiCell(25, 7, $data['total1'] ? $columns['total1']['title'] : '', 1, 'L', true, 1, 18, 110, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total2'] ? $columns['total2']['title'] : '', 1, 'L', true, 1, 43, 110, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total3'] ? $columns['total3']['title'] : '', 1, 'L', true, 1, 68, 110, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total4'] ? $columns['total4']['title'] : '', 1, 'L', true, 1, 93, 110, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total5'] ? $columns['total5']['title'] : '', 1, 'L', true, 1, 118, 110, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total6'] ? $columns['total6']['title'] : '', 1, 'L', true, 1, 143, 110, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total7'] ? $columns['total7']['title'] : '', 1, 'L', true, 1, 168, 110, true, 0, false, true, 0, 'M', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(25, 7, $data['total1'] ? number_format($data['total1']) : ' ', 1, 'R', true, 1, 18, 117, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total2'] ? number_format($data['total2']) : ' ', 1, 'R', true, 1, 43, 117, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total3'] ? number_format($data['total3']) : ' ', 1, 'R', true, 1, 68, 117, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total4'] ? number_format($data['total4']) : ' ', 1, 'R', true, 1, 93, 117, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total5'] ? number_format($data['total5']) : ' ', 1, 'R', true, 1, 118, 117, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total6'] ? number_format($data['total6']) : ' ', 1, 'R', true, 1, 143, 117, true, 0, false, true, 0, 'M', true);
        $pdf->MultiCell(25, 7, $data['total7'] ? number_format($data['total7']) : ' ', 1, 'R', true, 1, 168, 117, true, 0, false, true, 0, 'M', true);

        // メモ
        $pdf->MultiCell(183, 0, $data['memo'] ?: '',  1, 'L', true, 1, 10, 126, true, 0, false, true, 0, 'M', true);

        $pdf_output = $pdf->Output('file.pdf', 'S');
        header( "Pragma: public" );
        header( "Expires: 0 ");
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Content-Transfer-Encoding: binary" );
        header( "Content-Type: application/octet-streams" );
        header( "Content-Disposition: attachment; filename=\"{$file_name}\"" );
        print $pdf_output;
        exit;
    }
}