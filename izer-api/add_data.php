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

if ($data) {
    $category = $data['category'] ?? '';
    $title    = $conn->real_escape_string($data['title'] ?? '');
    $desc     = $conn->real_escape_string($data['description'] ?? '');
    $date_range = $conn->real_escape_string($data['date_range'] ?? 'Present');

    // --- LOGIC MAPPING PER TABEL SAKTI ---
    
    if ($category === 'about') {
        // Struktur DB: id, content
        // Karena cuma ada kolom 'content', kita masukkan variabel $desc ke sana
        $sql = "INSERT INTO about_content (content) VALUES ('$desc')";
    } 
    elseif ($category === 'about_images') {
    $img = $conn->real_escape_string($data['image_url'] ?? '');
    $sql = "INSERT INTO about_images (image_url) VALUES ('$img')";
}
    elseif ($category === 'skills') { 
        // Struktur DB: id, skill_name, skill_number, description
        // Kita set default skill_number ke 80 dulu supaya jumlah kolom dan value-nya pas (3 kolom, 3 value)
        $sql = "INSERT INTO skills (skill_name, skill_number, description) VALUES ('$title', 80, '$desc')";
    }
    
    elseif ($category === 'tech_icons') {
    $img = $conn->real_escape_string($data['image_url'] ?? '');
    $sql = "INSERT INTO tech_icons (name, image_url) VALUES ('$title', '$img')";
    }
    
    elseif ($category === 'experience') {
        // Struktur DB: id, title, date_range, description
        // Karena dari form belum ngirim date_range, kita isi default 'Present' dulu biar gak error kosong

        $sql = "INSERT INTO experience (title, date_range, description) VALUES ('$title', '$date_range', '$desc')";
    } 
    
    elseif ($category === 'education') {
    $sql = "INSERT INTO education (title, date_range, description) VALUES ('$title', '$date_range', '$desc')";
}

    elseif ($category === 'works' || $category === 'projects') {
        // Struktur DB: id, title, category, image_url, project_url, video_url
        $img = $conn->real_escape_string($data['image_url'] ?? '');
        $prj = $conn->real_escape_string($data['project_url'] ?? '');
        $vid = $conn->real_escape_string($data['video_url'] ?? '');
        
        $sql = "INSERT INTO projects (title, category, image_url, project_url, video_url) 
                VALUES ('$title', '$category', '$img', '$prj', '$vid')";
    }

    // --- EKSEKUSI ---
    if (isset($sql)) {
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Data $category Berhasil Masuk!"]);
        } else {
            // Bakal nampilin error detail dari MySQL kalau strukturnya masih gak cocok
            echo json_encode(["status" => "error", "message" => "MySQL Error: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Kategori Gak Dikenal: $category"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data JSON kosong atau tidak valid"]);
}
?>