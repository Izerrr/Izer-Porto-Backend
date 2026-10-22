<?php
// Database Configuration Template
// Salin file ini menjadi 'db_config.php' dan sesuaikan kredensial MySQL lokal/VPS Anda.

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'izer_portfolio';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode([
        "status" => "error",
        "message" => "Koneksi Database Gagal: " . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");
?>

