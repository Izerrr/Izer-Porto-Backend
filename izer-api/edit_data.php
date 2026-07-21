<?php

// 1. IZINKAN HEADER KUSTOM 'X-Admin-Token' MASUK
require_once 'cors.php';       // 1. Beresin CORS & preflight OPTIONS
require_once 'auth_check.php'; // 2. Kunci pintu pake HttpOnly Cookie / Token
include_once 'db_config.php';  // 3. Koneksi DB (sudah dipanggil juga di auth_check)

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
    $id          = (int)$data['id'];
    $category    = $data['category'] ?? '';
    $title       = $conn->real_escape_string($data['title'] ?? '');
    $description = $conn->real_escape_string($data['description'] ?? '');
    $date_range = $conn->real_escape_string($data['date_range'] ?? 'Present');
    $image_url   = $conn->real_escape_string($data['image_url'] ?? '');
    $project_url = $conn->real_escape_string($data['project_url'] ?? '');
    $video_url   = $conn->real_escape_string($data['video_url'] ?? '');

    // --- LOGIC UPDATE MAPPING PER TABEL ---
    if ($category === 'about') {
        // Tabel about_content: kolom teksnya adalah 'content'
        $sql = "UPDATE about_content SET content='$description' WHERE id=$id";
    } 
    elseif ($category === 'about_images') {
    $sql = "UPDATE about_images SET image_url='$image_url' WHERE id=$id";
    }
    elseif ($category === 'skills') {
        // Tabel skills: kolomnya skill_name dan description
        $sql = "UPDATE skills SET skill_name='$title', description='$description' WHERE id=$id";
    } 
    elseif ($category === 'tech_icons') {
    $sql = "UPDATE tech_icons SET name='$title', image_url='$image_url' WHERE id=$id";
    }
    elseif ($category === 'experience') {
        // Tabel experience: kolom title dan description
        $sql = "UPDATE experience SET title='$title', date_range='$date_range', description='$description' WHERE id=$id";
    } 
    elseif ($category === 'education') {
    $sql = "UPDATE education SET title='$title', date_range='$date_range', description='$description' WHERE id=$id";
}
    elseif ($category === 'works' || $category === 'projects') {
        // Tabel projects: update semua aset media proyek
        $sql = "UPDATE projects SET 
                title='$title', 
                image_url='$image_url', 
                project_url='$project_url', 
                video_url='$video_url' 
                WHERE id=$id";
    }

    // --- EKSEKUSI QUERY ---
    if (isset($sql)) {
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Data $category Berhasil Terupdate, Zi!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "MySQL Error: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Kategori Gak Dikenal: $category"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID atau Data Tidak Valid"]);
}
?>