<?php
require_once 'cors.php';
include 'db_config.php';

if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi Database Gagal"]));
}

$sql = "SELECT * FROM experience ORDER BY id ASC"; 
$result = $conn->query($sql);

$exp = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $exp[] = $row;
    }
}

echo json_encode($exp);
$conn->close();
?>