<?php
require_once 'cors.php';
include 'db_config.php';


$category = $_GET['category'] ?? 'works';

// 1. MAPPING TABEL & QUERY SPESIFIK
$sql = "";

if ($category === 'about') {
    // Di tabel about_content cuma ada 'id' dan 'content'.
    // Kita alias-kan 'content' menjadi 'description' untuk textarea di frontend.
    // Kita buat judul statis (atau bisa ambil sedikit cuplikan konten) sebagai 'title' agar list di React tidak kosong.
    $sql = "SELECT id, CONCAT('About Content #', id) AS title, content AS description FROM about_content ORDER BY id DESC";
} 
elseif ($category === 'about_images') {
    $sql = "SELECT id, image_url FROM about_images ORDER BY id DESC";
}
elseif ($category === 'experience') {
    // Struktur: id, title, date_range, description
    // Sudah pas. Kita ambil semuanya. Frontend akan pakai 'title' dan 'description'.
    $sql = "SELECT id, title, date_range, description FROM experience ORDER BY id DESC";
} 
elseif ($category === 'education') {
    $sql = "SELECT id, title, date_range, description FROM education ORDER BY id DESC";
}
elseif ($category === 'skills') {
    // Struktur: id, skill_name, skill_number, description
    // Alias-kan 'skill_name' menjadi 'title'. 
    // Karena tabel sudah punya kolom 'description', ambil langsung tanpa perlu alias dari skill_number.
    $sql = "SELECT id, skill_name AS title, skill_number, description FROM skills ORDER BY skill_number ASC";
} 
elseif ($category === 'tech_icons') {
    // Kita alias-kan name jadi title biar sinkron dengan list kanan di Admin Next.js
    $sql = "SELECT id, name AS title, image_url FROM tech_icons ORDER BY id ASC";
}
elseif ($category === 'works') {
    // Struktur: id, title, category, image_url, project_url, video_url
    // Query ini sudah aman. Bisa pakai SELECT * atau dijabarkan seperti di bawah.
    $sql = "SELECT id, title, category, image_url, project_url, video_url FROM projects ORDER BY id DESC";
}

// 2. EKSEKUSI
if ($sql !== "") {
    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Kategori gak jelas, Zi!"]);
}
?>