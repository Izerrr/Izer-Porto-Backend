<?php
// CORS & preflight OPTIONS
require_once 'cors.php';
include_once 'db_config.php'; 

if (!isset($conn) || !$conn || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection unavailable"]);
    exit;
}

$sql = "SELECT image_url FROM about_images ORDER BY order_index ASC, id ASC";
$result = $conn->query($sql);
$images = [];
if ($result) {
    while($row = $result->fetch_assoc()) { 
        $images[] = $row['image_url']; 
    }
}
echo json_encode($images);
?>
