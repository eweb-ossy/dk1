<?php 

ini_set("display_errors", "On");
error_reporting(-1);

function getUserData() {
    global $year, $month;
    // $url = 'https://'.$host.'/api/aporan';
    $url = 'http://localhost:8000/api/esnapay';
    $data = [
        'year' => $year,
        'month' => $month
    ];
    $content = http_build_query($data);
    $headers = [
        'Content-Type: application/x-www-form-urlencoded'
    ];
    $options = ['http' => [
        'method' => 'POST',
        'content' => $content,
        'header' => implode("\r\n", $headers),
    ]];
    $options['ssl']['verify_peer']=false;
    $options['ssl']['verify_peer_name']=false;
    $contents = file_get_contents($url, false, stream_context_create($options));
    return json_decode($contents, true);
}


$year = '2022';
$month = '01';
// $usersData = [];
$usersData = getUserData();

print_r($usersData);