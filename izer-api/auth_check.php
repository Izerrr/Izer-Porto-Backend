<?php
include_once 'db_config.php';

// Take the token from either the cookie or the custom header
$token = $_COOKIE['admin_token'] ?? $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? null;

if (!$token) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized access!"]);
    exit();
}

// Check if the token exists in the database
$stmt = $conn->prepare("SELECT id FROM admin_users WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Invalid or expired token!"]);
    exit();
}
?>