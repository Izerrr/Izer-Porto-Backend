<?php
// Centralized CORS & Auth Middleware
require_once 'cors.php';
require_once 'auth_check.php';
include_once 'db_config.php';

// Read raw JSON payload
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid or empty JSON payload!"]);
    exit;
}

// Input Extraction & Sanitization
$category    = trim($data['category'] ?? '');
$project_cat = strip_tags(trim($data['project_category'] ?? 'Web Development'));
$title       = strip_tags(trim($data['title'] ?? ''));
$desc        = strip_tags(trim($data['description'] ?? ''));
$date_range  = strip_tags(trim($data['date_range'] ?? 'Present'));
$img_url     = trim($data['image_url'] ?? '');
$proj_url    = trim($data['project_url'] ?? '');
$video_url   = trim($data['video_url'] ?? '');

$stmt = null;

// Prepared Statements according to category
if ($category === 'about') {
    $stmt = $conn->prepare("INSERT INTO about_content (content) VALUES (?)");
    $stmt->bind_param("s", $desc);
} 
elseif ($category === 'about_images') {
    $stmt = $conn->prepare("INSERT INTO about_images (image_url) VALUES (?)");
    $stmt->bind_param("s", $img_url);
} 
elseif ($category === 'skills') {
    $skill_number = 80;
    $stmt = $conn->prepare("INSERT INTO skills (skill_name, skill_number, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $title, $skill_number, $desc);
} 
elseif ($category === 'tech_icons') {
    $stmt = $conn->prepare("INSERT INTO tech_icons (name, image_url) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $img_url);
} 
elseif ($category === 'experience') {
    $stmt = $conn->prepare("INSERT INTO experience (title, date_range, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $date_range, $desc);
} 
elseif ($category === 'education') {
    $stmt = $conn->prepare("INSERT INTO education (title, date_range, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $date_range, $desc);
} 
elseif ($category === 'works' || $category === 'projects') {
    if ($project_cat === '' || strtolower($project_cat) === 'works') {
        $project_cat = 'Web Development';
    }
    $stmt = $conn->prepare("INSERT INTO projects (title, category, image_url, project_url, video_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $project_cat, $img_url, $proj_url, $video_url);
}

// Execution and Response Handling
if ($stmt) {
    if ($stmt->execute()) {
        $new_id = (int)$conn->insert_id;
        $tableMap = [
            'about_images' => 'about_images',
            'experience'   => 'experience',
            'education'    => 'education',
            'skills'       => 'skills',
            'tech_icons'   => 'tech_icons',
            'works'        => 'projects',
            'projects'     => 'projects',
        ];
        if (isset($tableMap[$category]) && $new_id > 0) {
            $tbl = $tableMap[$category];
            $conn->query("UPDATE {$tbl} SET order_index = {$new_id} WHERE id = {$new_id}");
        }
        echo json_encode(["status" => "success", "message" => "Data for category '{$category}' created successfully!"]);
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
