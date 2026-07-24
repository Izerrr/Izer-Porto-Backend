<?php
// CORS & preflight OPTIONS
require_once 'cors.php';
include 'db_config.php'; 

$sql = "SELECT image_url FROM about_images";
$result = $conn->query($sql);
$images = [];
while($row = $result->fetch_assoc()) { $images[] = $row['image_url']; }
echo json_encode($images);
?>