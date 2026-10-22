<?php
// CORS & preflight OPTIONS
require_once 'cors.php';      
require_once 'auth_check.php'; 
include_once 'db_config.php';  

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
        $stmt = $conn->prepare("DELETE FROM {$table} WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Data dari tabel {$table} berhasil dihapus!"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "MySQL Error: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Kategori tidak dikenal: {$category}"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
}
?>
