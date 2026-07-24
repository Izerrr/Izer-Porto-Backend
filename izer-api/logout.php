<?php
// CORS & Security Headers
require_once 'cors.php'; 
include_once 'db_config.php';

// Expire the auth cookie
setcookie('admin_token', '', [
    'expires'  => time() - 3600, // Expire in the past
    'path'     => '/',
    'domain'   => '.izerworks.my.id',
    'httponly' => true,
    'samesite' => 'Lax'
]);

echo json_encode([
    "status" => "success",
    "message" => "Logged out successfully"
]);
?>