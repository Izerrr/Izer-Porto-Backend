<?php

$allowed_origins = [
    "http://localhost:3000",
    "http://127.0.0.1:3000",
    "https://izerworks.my.id",
    "https://www.izerworks.my.id"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Allow specific whitelist or local/LAN development origins
if (
    in_array($origin, $allowed_origins) ||
    preg_match('#^https?://(localhost|127\.0\.0\.1|192\.168\.\d+\.\d+)(:\d+)?$#', $origin)
) {
    header("Access-Control-Allow-Origin: " . $origin);
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, X-Admin-Token, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Intercept preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}
?>
