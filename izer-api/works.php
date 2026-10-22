<?php
// CORS & preflight OPTIONS
require_once 'cors.php';       
include_once 'db_config.php';

if (!isset($conn) || !$conn || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection unavailable"]);
    exit;
}

$result = $conn->query("SELECT * FROM projects ORDER BY order_index ASC, id ASC");
$data = [];
if ($result) {
    while($row = $result->fetch_assoc()) { 
        $data[] = $row; 
    }
}
echo json_encode($data);
?>
