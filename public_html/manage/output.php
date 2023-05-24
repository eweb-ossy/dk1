<?php 

$no = $_GET['field_no'];
$output_date = $_GET['field_outputdate'];
$limit_date = $_GET['field_limitdate'];
$to = $_GET['field_to'];
$title = $_GET['field_title'];
$item1 = $_GET['field_item1'];
$item_detail1 = $_GET['field_item_detail1'];
$price1 = $_GET['field_price1'];
$num1 = $_GET['field_num1'];
$unit1 = $_GET['field_unit1'];
$item_price1 = $_GET['field_item_price1'];
$item2 = $_GET['field_item2'];
$item_detail2 = $_GET['field_item_detail2'];
$price2 = $_GET['field_price2'];
$num2 = $_GET['field_num2'];
$unit2 = $_GET['field_unit2'];
$item_price2 = $_GET['field_item_price2'];
$price = $_GET['field_price'];
$tax = $_GET['field_tax'];
$total_price = $_GET['field_total_price'];

require_once('vendor/autoload.php');

use setasign\Fpdi\Tcpdf\Fpdi;

// 用紙の設定
$pdf = new Fpdi('P', 'mm', 'A4');
$pdf->SetMargins(0, 0, 0);

$templateFile = dirname(__FILE__) . '/template/template.pdf'; // 領収書テンプレートPDF
$fontPath = dirname(__FILE__) . '/font/ipaexg.ttf'; // 日本語フォントの設定

// テンプレートの複製
$pdf->SetSourceFile($templateFile);
$p1 = $pdf->importPage(1);
$pdf->AddPage();
$pdf->useTemplate($p1, null, null, null, null, true);

// フォントの設定
$font = TCPDF_FONTS::addTTFfont($fontPath);
$pdf->SetFont($font, '', 12);
$pdf->SetTextColor(0, 0, 0);

// NO
$pdf->SetXY(32, 34);
$pdf->Write(0, $no);

// 発行日
$pdf->SetXY(275, 34);
$pdf->Write(0, $output_date);

// 宛名
$pdf->SetFont($font, 'B', 18);
$pdf->SetXY(20, 60);
$pdf->Write(0, $to);

// タイトル
$pdf->SetFont($font, 'I', 18);
$pdf->SetXY(20, 73);
$pdf->Write(0, $title);

// 項目1-1
$pdf->SetFont($font, '', 10);
$pdf->SetXY(21, 131.5);
$pdf->Write(0, $item1);
// 項目説明1-1
$pdf->SetFont($font, '', 10);
$pdf->SetXY(96, 131.5);
$pdf->Write(0, $item_detail1);
// 単価1-1
$pdf->SetFont($font, '', 10);
$pdf->SetXY(239, 131.5);
$pdf->Cell(1, 0, $price1, 0, 0, 'R');
// 数量1-1
$pdf->SetFont($font, '', 10);
$pdf->SetXY(256.5, 131.5);
$pdf->Cell(1, 0, $num1, 0, 0, 'R');
// 単位1-1
$pdf->SetFont($font, '', 10);
$pdf->SetXY(275, 131.5);
$pdf->Cell(1, 0, $unit1, 0, 0, 'R');
// 金額1-1
$pdf->SetFont($font, '', 10);
$pdf->SetXY(309, 131.5);
$pdf->Cell(1, 0, $item_price1, 0, 0, 'R');
// 項目1-2
$pdf->SetFont($font, '', 10);
$pdf->SetXY(21, 138.5);
$pdf->Write(0, $item2);
// 項目説明1-2
$pdf->SetFont($font, '', 10);
$pdf->SetXY(96, 138.5);
$pdf->Write(0, $item_detail2);
// 単価1-2
$pdf->SetFont($font, '', 10);
$pdf->SetXY(239, 138.5);
$pdf->Cell(1, 0, $price2, 0, 0, 'R');
// 数量1-2
$pdf->SetFont($font, '', 10);
$pdf->SetXY(256.5, 138.5);
$pdf->Cell(1, 0, $num2, 0, 0, 'R');
// 単位1-2
$pdf->SetFont($font, '', 10);
$pdf->SetXY(275, 138.5);
$pdf->Cell(1, 0, $unit2, 0, 0, 'R');
// 金額1-2
$pdf->SetFont($font, '', 10);
$pdf->SetXY(309, 138.5);
$pdf->Cell(1, 0, $item_price2, 0, 0, 'R');

// 小計1
$pdf->SetFont($font, 'B', 12);
$pdf->SetXY(310, 171);
$pdf->Cell(1, 0, $price, 0, 0, 'R');

// 小計1+2+3
$pdf->SetFont($font, 'B', 15);
$pdf->SetXY(310, 297.5);
$pdf->Cell(1, 0, $price, 0, 0, 'R');

// 小計1+2+3+4
$pdf->SetFont($font, 'B', 15);
$pdf->SetXY(310, 361);
$pdf->Cell(1, 0, $price, 0, 0, 'R');

// 消費税
$pdf->SetFont($font, 'B', 12);
$pdf->SetXY(310, 372);
$pdf->Cell(1, 0, $tax, 0, 0, 'R');

// ご請求金額（消費税込）
$pdf->SetFont($font, 'B', 22);
$pdf->SetXY(310, 388.5);
$pdf->Cell(1, 0, $total_price, 0, 0, 'R');

// お振込期限
$pdf->SetFont($font, 'B', 14);
$pdf->SetXY(47, 429);
$pdf->Write(0, $limit_date);

// 出力
$filename = date( 'Ymd' )."file.pdf";
$pdf->Output($filename);
exit;