<?php

$now = new DateTimeImmutable();
$yesterday = $now->modify( '-1 day' );
$begin_date = new DateTimeImmutable( $yesterday->format('Y-m-d') );

// $begin_date = new DateTimeImmutable('2020-11-01');

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin_date, $interval ,$now);

// $host = 'lifein24';
// foreach($daterange as $date){
//   $url = 'https://'.$host.'.dk-keeper.com/auto/analyze/status?dk_date='.$date->format("Y-m-d");
//   $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, $url);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//   $response =  curl_exec($ch);
//   echo $response.PHP_EOL;
//   curl_close($ch);
//   sleep(1);
// }
// exit;

try {
  $dsn = 'mysql:dbname=eweb_dakoku;host=183.181.99.201;charset=utf8mb4';
  $username = 'eweb_ossy';
  $password = '7Vr5EvUEgpFa';
  $driver_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ];
  $pdo = new PDO($dsn, $username, $password, $driver_options);
} catch (PDOException $e) {
  header('Content-Type: text/plain; charset=UTF-8', true, 500);
  exit($e->getMessage());
}

$stmt = $pdo->prepare('SELECT * FROM global_data WHERE status = :status');
$stmt->bindValue(':status', 1, PDO::PARAM_INT);
$stmt->execute();

while ($row = $stmt->fetch()) {
  echo '接続：'.$row['company_name'].PHP_EOL;
  $host = $row['system_id'];
  foreach($daterange as $date){
    $url = 'https://'.$host.'.dk-keeper.com/auto/analyze/status?dk_date='.$date->format("Y-m-d");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response =  curl_exec($ch);
    echo $response.PHP_EOL;
    curl_close($ch);
    sleep(5);
  }
}

$pdo = null;
echo '完了'.PHP_EOL;
