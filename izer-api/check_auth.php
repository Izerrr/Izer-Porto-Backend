<?php
// Mengambil token yang dikirim Next.js lewat Header baju 'X-Admin-Token'
$headers = function_exists('getallheaders') ? getallheaders() : [];
$token = $headers['X-Admin-Token'] ?? $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? '';

if (empty($token)) {
    echo json_encode(["status" => "error", "message" => "Akses ditolak: Token tidak ditemukan!"]);
    exit;
}

$token = $conn->real_escape_string($token);

// Cek apakah token ini valid ada di database
$auth_query = $conn->query("SELECT id FROM admin_users WHERE token='$token'");

if (!$auth_query || $auth_query->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Akses ditolak: Token palsu atau sudah kedaluwarsa!"]);
    exit;
}
// Jika lolos, file PHP yang meng-include ini akan lanjut berjalan normal...
?>