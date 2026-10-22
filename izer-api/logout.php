<?php
// CORS & Security Headers
require_once 'cors.php'; 
include_once 'db_config.php';

$host = $_SERVER['HTTP_HOST'] ?? '';
$is_production = (strpos($host, 'izerworks.my.id') !== false);

$cookie_options = [
    'expires'  => time() - 3600, // Expire in the past
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax'
];

if ($is_production) {
    $cookie_options['domain'] = '.izerworks.my.id';
}

// Expire the auth cookie
setcookie('admin_token', '', $cookie_options);

// If token provided, invalidate in database as well
$token = $_COOKIE['admin_token'] ?? $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? null;
if ($token && isset($conn) && $conn && !$conn->connect_error) {
    $stmt = $conn->prepare("UPDATE admin_users SET token = NULL WHERE token = ?");
    if ($stmt) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();
    }
}

echo json_encode([
    "status" => "success",
    "message" => "Logged out successfully"
]);
?>
