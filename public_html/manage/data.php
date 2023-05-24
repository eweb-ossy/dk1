<?php 

$data = [];
if ($_POST['id']) {
    $db = 'eweb_dakoku';
    // require(dirname(__FILE__) . '/db.php'); // ローカル
    require('/home/eweb/dk-keeper.com/db.php'); // 本番
    $sql = 'SELECT system_id, company_fullname, db_name, bill_type, bill_price, bill_no, bill_note FROM global_data WHERE id = '.$_POST['id'];
    // $sql = 'SELECT db_name FROM global_data WHERE id = 1';
    $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    $db = $row['db_name'];
    $data['company'] = [
        'company_fullname' => $row['company_fullname'],
        'bill_type' => $row['bill_type'],
        'bill_price' => $row['bill_price'],
        'bill_no' => $row['bill_no'],
        'system_id' => $row['system_id'],
        'bill_note' => $row['bill_note'] ?: '',
        'db_name' => $db,
        'date' => $_POST['date']
    ];
    if ($db) {
        $date = $_POST['date'];
        $row = null;
        $pdo = null;
        // $db = 'dk_demo'; // ローカル
        // require(dirname(__FILE__) . '/db.php'); // ローカル
        require('/home/eweb/dk-keeper.com/db.php'); // 本番
        $sql = 'SELECT user_id, dk_date, in_work_time, out_work_time, fact_work_hour, status FROM time_data WHERE DATE_FORMAT(dk_date, "%Y%m") = "'.$date.'" AND fact_work_hour > 0';
        $result = $pdo->query($sql)->fetchALL(PDO::FETCH_ASSOC);
        foreach ($result as $value) {
            $data['data'][$value['user_id']][] = $value;
        }
        if (is_array($data['data'])) {
            $data['data_num'] = count($data['data']);
        } else {
            $data['data_num'] = 0;
        }
    }
}
// echo count($data);
// echo "<pre>";
// print_r($data);
// echo "</pre>";

//jsonとして出力
header('Content-type: application/json');
echo json_encode($data);