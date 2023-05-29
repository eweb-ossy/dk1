<?php 

$filename = 'dakoku.sql';
$servername = "dakoku-db.cgdtnpptj1o2.ap-northeast-1.rds.amazonaws.com";
$username = "dakoku_db_root";
$password = "HyN8iK85rJW9";
$database = 'test';

$conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);

$query = file_get_contents($filename);

$stmt = $conn->prepare($query);

if ($stmt->execute()) {
    echo "test DB Success";
} else {
    echo "Fail";
}