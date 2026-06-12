<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include 'config.php'; // Pakai file config biar rapi

$sql = "SELECT image_url FROM about_images";
$result = $conn->query($sql);
$images = [];
while($row = $result->fetch_assoc()) { $images[] = $row['image_url']; }
echo json_encode($images);
?>