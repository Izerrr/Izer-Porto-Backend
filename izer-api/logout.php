<?php
// CORS & Security Headers
require_once 'cors.php'; // Replaces inline headers
include_once 'db_config.php';

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