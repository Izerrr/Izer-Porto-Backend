<?php
include 'config.php';
$result = $conn->query("SELECT * FROM projects ORDER BY id ASC");
$data = [];
while($row = $result->fetch_assoc()) { $data[] = $row; }
echo json_encode($data);
?>