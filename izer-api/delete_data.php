<?php
// 1. IZINKAN HEADER KUSTOM 'X-Admin-Token' MASUK
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Admin-Token"); 
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// 2. KUNCI UTAMA: Langsung loloskan request OPTIONS tanpa perlu cek token
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); 
}

// 3. BARU PANGGIL KONEKSI DAN SATPAM AUTH
include 'db_config.php'; // <-- Sesuaikan dengan nama file database lo (db.php / config.php)
include 'check_auth.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data && isset($data['id'])) {
    $id = (int)$data['id'];
    $category = $data['category'] ?? 'works'; 

    // --- Mapping Tabel Lengkap ---
    $table = "";
    if ($category === 'about') $table = "about_content";
    elseif ($category === 'about_images') $table = "about_images";
    elseif ($category === 'experience') $table = "experience";
    elseif ($category === 'education') $table = "education";
    elseif ($category === 'skills') $table = "skills";
    elseif ($category === 'tech_icons') $table = "tech_icons";
    elseif ($category === 'works' || $category === 'projects') $table = "projects";

    if ($table !== "") {
        $sql = "DELETE FROM $table WHERE id=$id";

        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Beres Zi, data dari tabel $table lenyap!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "MySQL Error: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Kategori Gak Dikenal: $category"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID Tidak Ditemukan"]);
}
?>