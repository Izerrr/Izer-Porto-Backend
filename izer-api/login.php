<?php
// CORS & Security Headers
require_once 'cors.php'; // Replaces inline headers
include_once 'db_config.php';

// Rate Limiting (5 attempts / 15 mins)
$client_ip = $_SERVER['REMOTE_ADDR'];
$limit_file = sys_get_temp_dir() . '/rate_limit_' . md5($client_ip) . '.json';
$max_attempts = 5;
$lockout_time = 15 * 60;

$rate_data = ['attempts' => 0, 'last_attempt' => time()];
if (file_exists($limit_file)) {
    $rate_data = json_decode(file_get_contents($limit_file), true);
}

// Reset limit window
if (time() - $rate_data['last_attempt'] > $lockout_time) {
    $rate_data['attempts'] = 0;
}

// Check lockout
if ($rate_data['attempts'] >= $max_attempts) {
    $remaining_minutes = ceil(($lockout_time - (time() - $rate_data['last_attempt'])) / 60);
    http_response_code(429);
    echo json_encode([
        "status" => "error",
        "message" => "Terlalu banyak percobaan login gagal! Coba lagi dalam {$remaining_minutes} menit."
    ]);
    exit;
}

// Process login
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Clear rate limit
            if (file_exists($limit_file)) {
                unlink($limit_file);
            }

            // Generate token
            $token = bin2hex(random_bytes(32));

            $update_stmt = $conn->prepare("UPDATE admin_users SET token = ? WHERE id = ?");
            $update_stmt->bind_param("si", $token, $user['id']);
            $update_stmt->execute();

            $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

            // Set HttpOnly auth cookie
            setcookie('admin_token', $token, [
                'expires'  => time() + 86400,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $is_https,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            echo json_encode([
                "status" => "success",
                "message" => "Selamat datang kembali, Kapten!"
            ]);
            exit;
        }
    }

    // Record failed attempt
    $rate_data['attempts'] += 1;
    $rate_data['last_attempt'] = time();
    file_put_contents($limit_file, json_encode($rate_data));

    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Username atau Password salah!"]);
    exit;
}
?>