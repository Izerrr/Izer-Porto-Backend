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

if ($data && isset($data['id'])) {
    $id          = (int)$data['id'];
    $category    = $data['category'] ?? '';
    $title       = $conn->real_escape_string($data['title'] ?? '');
    $description = $conn->real_escape_string($data['description'] ?? '');
    $date_range = $conn->real_escape_string($data['date_range'] ?? 'Present');
    $image_url   = $conn->real_escape_string($data['image_url'] ?? '');
    $project_url = $conn->real_escape_string($data['project_url'] ?? '');
    $video_url   = $conn->real_escape_string($data['video_url'] ?? '');

    // Logic to determine which table to update based on the category
    if ($category === 'about') {
        // Columns in about_content: id, content
        $sql = "UPDATE about_content SET content='$description' WHERE id=$id";
    } 
    elseif ($category === 'about_images') {
    $sql = "UPDATE about_images SET image_url='$image_url' WHERE id=$id";
    }
    elseif ($category === 'skills') {
        // Columns in skills: id, skill_name, skill_number, description
        $sql = "UPDATE skills SET skill_name='$title', description='$description' WHERE id=$id";
    } 
    elseif ($category === 'tech_icons') {
    $sql = "UPDATE tech_icons SET name='$title', image_url='$image_url' WHERE id=$id";
    }
    elseif ($category === 'experience') {
        // Columns in experience: id, title, date_range, description
        $sql = "UPDATE experience SET title='$title', date_range='$date_range', description='$description' WHERE id=$id";
    } 
    elseif ($category === 'education') {
    $sql = "UPDATE education SET title='$title', date_range='$date_range', description='$description' WHERE id=$id";
}
    elseif ($category === 'works' || $category === 'projects') {
        // Columns in projects: id, title, category, image_url, project_url, video_url
        $sql = "UPDATE projects SET 
                title='$title', 
                image_url='$image_url', 
                project_url='$project_url', 
                video_url='$video_url' 
                WHERE id=$id";
    }

    // Query execution and response
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