<?php
// Very simple test that doesn't require any classes
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

echo json_encode([
    "status" => "success",
    "message" => "PHP server is working",
    "php_version" => PHP_VERSION,
    "timestamp" => date("Y-m-d H:i:s"),
    "current_dir" => __DIR__,
    "server_info" => [
        "REQUEST_URI" => $_SERVER["REQUEST_URI"] ?? "not_set",
        "REQUEST_METHOD" => $_SERVER["REQUEST_METHOD"] ?? "not_set",
        "SERVER_NAME" => $_SERVER["SERVER_NAME"] ?? "not_set",
        "SERVER_PORT" => $_SERVER["SERVER_PORT"] ?? "not_set"
    ]
], JSON_PRETTY_PRINT);
?>
