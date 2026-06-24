<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "izer_portofolio"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi Database Gagal"]));
}

$sql = "SELECT * FROM experience ORDER BY id ASC"; // Ambil yang terbaru dulu
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