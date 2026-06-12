<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Admin-Token");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle preflight request dari Next.js
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'db_config.php'; // Sesuaikan dengan nama file koneksi database lo

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $username = $conn->real_escape_string($data['username'] ?? '');
    $password = $data['password'] ?? '';

    // Cari usernamenya di DB
    $sql = "SELECT * FROM admin_users WHERE username='$username'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        
        // Verifikasi password terenkripsi
        if (password_verify($password, $user['password'])) {
            // Jika sukses, buat token acak yang kuat sepanjang 64 karakter
            $token = bin2hex(random_bytes(32));
            
            // Simpan token ke DB agar bisa divalidasi nanti
            $conn->query("UPDATE admin_users SET token='$token' WHERE id=" . $user['id']);
            
            echo json_encode([
                "status" => "success", 
                "token" => $token, 
                "message" => "Selamat datang kembali, Kapten!"
            ]);
            exit;
        }
    }
    
    echo json_encode(["status" => "error", "message" => "Username atau Password salah!"]);
}
?>