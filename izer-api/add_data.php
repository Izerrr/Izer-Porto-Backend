<?php
// CORS & preflight OPTIONS
require_once 'cors.php';       
require_once 'auth_check.php'; 
include_once 'db_config.php';  


// Allow OPTIONS requests for preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); 
}

// Check connection + Security Authentication
include 'db_config.php'; 
include 'check_auth.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data) {
    $category = $data['category'] ?? '';
    $title    = $conn->real_escape_string($data['title'] ?? '');
    $desc     = $conn->real_escape_string($data['description'] ?? '');
    $date_range = $conn->real_escape_string($data['date_range'] ?? 'Present');

    // MySQL Tables Mapping based on category
    
    if ($category === 'about') {
        // Structure DB: id, content
        $sql = "INSERT INTO about_content (content) VALUES ('$desc')";
    } 
    elseif ($category === 'about_images') {
    $img = $conn->real_escape_string($data['image_url'] ?? '');
    $sql = "INSERT INTO about_images (image_url) VALUES ('$img')";
}
    elseif ($category === 'skills') { 
        // Structure DB: id, skill_name, skill_number, description
        $sql = "INSERT INTO skills (skill_name, skill_number, description) VALUES ('$title', 80, '$desc')";
    }
    
    elseif ($category === 'tech_icons') {
    $img = $conn->real_escape_string($data['image_url'] ?? '');
    $sql = "INSERT INTO tech_icons (name, image_url) VALUES ('$title', '$img')";
    }
    
    elseif ($category === 'experience') {
        // Structure DB: id, title, date_range, description

        $sql = "INSERT INTO experience (title, date_range, description) VALUES ('$title', '$date_range', '$desc')";
    } 
    
    elseif ($category === 'education') {
    $sql = "INSERT INTO education (title, date_range, description) VALUES ('$title', '$date_range', '$desc')";
}

    elseif ($category === 'works' || $category === 'projects') {
        // Database Structure based on MySQL
        $img = $conn->real_escape_string($data['image_url'] ?? '');
        $prj = $conn->real_escape_string($data['project_url'] ?? '');
        $vid = $conn->real_escape_string($data['video_url'] ?? '');
        
        $sql = "INSERT INTO projects (title, category, image_url, project_url, video_url) 
                VALUES ('$title', '$category', '$img', '$prj', '$vid')";
    }

    // Execution
    if (isset($sql)) {
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Data $category Berhasil Masuk!"]);
        } else {
            // MySQL Error Handling
            echo json_encode(["status" => "error", "message" => "MySQL Error: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Kategori Gak Dikenal: $category"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data JSON kosong atau tidak valid"]);
}
?>