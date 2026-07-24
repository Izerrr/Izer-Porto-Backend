<?php
// Centralized CORS & Auth Middleware
require_once 'cors.php';
require_once 'auth_check.php';
include_once 'db_config.php';

// Read raw JSON payload
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);

if (!$data || !$id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid ID or payload format!"]);
    exit;
}

// Input Extraction & Sanitization
$category   = trim($data['category'] ?? '');
$title      = strip_tags(trim($data['title'] ?? ''));
$desc       = strip_tags(trim($data['description'] ?? ''));
$date_range = strip_tags(trim($data['date_range'] ?? 'Present'));
$img_url    = trim($data['image_url'] ?? '');
$proj_url   = trim($data['project_url'] ?? '');
$video_url  = trim($data['video_url'] ?? '');

$stmt = null;

// Prepared Statements according to category
if ($category === 'about') {
    $stmt = $conn->prepare("UPDATE about_content SET content = ? WHERE id = ?");
    $stmt->bind_param("si", $desc, $id);
} 
elseif ($category === 'about_images') {
    $stmt = $conn->prepare("UPDATE about_images SET image_url = ? WHERE id = ?");
    $stmt->bind_param("si", $img_url, $id);
} 
elseif ($category === 'skills') {
    $stmt = $conn->prepare("UPDATE skills SET skill_name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $desc, $id);
} 
elseif ($category === 'tech_icons') {
    $stmt = $conn->prepare("UPDATE tech_icons SET name = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $img_url, $id);
} 
elseif ($category === 'experience') {
    $stmt = $conn->prepare("UPDATE experience SET title = ?, date_range = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $date_range, $desc, $id);
} 
elseif ($category === 'education') {
    $stmt = $conn->prepare("UPDATE education SET title = ?, date_range = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $date_range, $desc, $id);
} 
elseif ($category === 'works' || $category === 'projects') {
    $stmt = $conn->prepare("UPDATE projects SET title = ?, image_url = ?, project_url = ?, video_url = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $img_url, $proj_url, $video_url, $id);
}

// Execution and Response Handling
if ($stmt) {
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Data for category '{$category}' updated successfully!"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database execution failed: " . $stmt->error]);
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Unknown or invalid category: '{$category}'"]);
}
?>