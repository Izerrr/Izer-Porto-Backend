<?php
require_once 'cors.php';
require_once 'auth_check.php';
include_once 'db_config.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$category = trim($data['category'] ?? '');
$order = $data['order'] ?? [];

if (!$category || !is_array($order) || empty($order)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid category or order list!"]);
    exit;
}

$validIds = [];
foreach ($order as $rawId) {
    $cid = filter_var($rawId, FILTER_VALIDATE_INT);
    if ($cid !== false) {
        $validIds[] = (int)$cid;
    }
}

if (empty($validIds)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "No valid IDs provided for reordering!"]);
    exit;
}

$tableMap = [
    'about_images' => 'about_images',
    'experience'   => 'experience',
    'education'    => 'education',
    'skills'       => 'skills',
    'tech_icons'   => 'tech_icons',
    'works'        => 'projects',
    'projects'     => 'projects',
];

if (!isset($tableMap[$category])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Unsupported category for reordering!"]);
    exit;
}

$table = $tableMap[$category];

$stmt = $conn->prepare("UPDATE {$table} SET order_index = ? WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to prepare SQL: " . $conn->error]);
    exit;
}

$conn->begin_transaction();
try {
    foreach ($validIds as $index => $cleanId) {
        $orderIndex = (int)$index;
        $stmt->bind_param("ii", $orderIndex, $cleanId);
        $stmt->execute();
    }
    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Order updated successfully!"]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to update order: " . $e->getMessage()]);
}
$stmt->close();
?>

