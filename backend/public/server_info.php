<?php
// Server information endpoint
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$info = [
    "status" => "success",
    "message" => "Server is responding",
    "timestamp" => date("Y-m-d H:i:s"),
    "server_info" => [
        "php_version" => PHP_VERSION,
        "server_software" => $_SERVER["SERVER_SOFTWARE"] ?? "Unknown",
        "server_name" => $_SERVER["SERVER_NAME"] ?? "Unknown",
        "server_port" => $_SERVER["SERVER_PORT"] ?? "Unknown",
        "request_uri" => $_SERVER["REQUEST_URI"] ?? "Unknown",
        "request_method" => $_SERVER["REQUEST_METHOD"] ?? "Unknown",
        "remote_addr" => $_SERVER["REMOTE_ADDR"] ?? "Unknown",
        "http_host" => $_SERVER["HTTP_HOST"] ?? "Unknown",
    ],
    "environment" => [
        "port" => $_ENV["PORT"] ?? "not_set",
        "railway_environment" => $_ENV["RAILWAY_ENVIRONMENT"] ?? "not_set",
    ]
];

echo json_encode($info, JSON_PRETTY_PRINT);
?>
