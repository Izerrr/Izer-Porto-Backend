<?php
// CORS & preflight OPTIONS
require_once 'cors.php';      
require_once 'auth_check.php'; 
include_once 'db_config.php';  

// Allow OPTIONS requests for preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); 
}

// Seek connection + Security Authentication
include 'db_config.php'; 
include 'check_auth.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data && isset($data['id'])) {
    $id = (int)$data['id'];
    $category = $data['category'] ?? 'works'; 

    // Table mapping based on category
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