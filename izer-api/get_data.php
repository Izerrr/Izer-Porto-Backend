<?php
require_once 'cors.php';
include_once 'db_config.php';

$category = $_GET['category'] ?? 'works';

// Table mapping based on category
$sql = "";

if ($category === 'about') {
    // The 'about_content' table only contains 'id' and 'content'.
    // Alias 'content' to 'description' for the frontend textarea.
    // Generate a static title so the list item in React is not empty.
    $sql = "SELECT id, CONCAT('About Content #', id) AS title, content AS description FROM about_content ORDER BY id DESC";
} 
elseif ($category === 'about_images') {
    $sql = "SELECT id, image_url FROM about_images ORDER BY id DESC";
}
elseif ($category === 'experience') {
    // Structure: id, title, date_range, description
    // Retrieve all fields; Frontend will consume 'title' and 'description'.
    $sql = "SELECT id, title, date_range, description FROM experience ORDER BY id DESC";
} 
elseif ($category === 'education') {
    $sql = "SELECT id, title, date_range, description FROM education ORDER BY id DESC";
}
elseif ($category === 'skills') {
    // Structure: id, skill_name, skill_number, description
    // Alias 'skill_name' to 'title' for frontend consistency.
    // Fetch 'description' directly as the column already exists in the table.
    $sql = "SELECT id, skill_name AS title, skill_number, description FROM skills ORDER BY skill_number ASC";
} 
elseif ($category === 'tech_icons') {
    // Alias 'name' to 'title' to synchronize with the Next.js Admin sidebar list
    $sql = "SELECT id, name AS title, image_url FROM tech_icons ORDER BY id ASC";
}
elseif ($category === 'works') {
    // Structure: id, title, category, image_url, project_url, video_url
    // Explicit selection of required columns for query safety and performance.
    $sql = "SELECT id, title, category, image_url, project_url, video_url FROM projects ORDER BY id DESC";
}

// Execute the query and fetch results
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
    echo json_encode(["error" => "Invalid category requested!"]);
}
?>