<?php
// CORS & Security Headers
$allowed_origins = [
    "http://localhost:3000",
    "https://izer.dev"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $origin);
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Expire the auth cookie
setcookie('admin_token', '', [
    'expires'  => time() - 3600, // Expire in the past
    'path'     => '/',
    'domain'   => '',
    'httponly' => true,
    'samesite' => 'Strict'
]);

echo json_encode([
    "status" => "success",
    "message" => "Logged out successfully"
]);
?>