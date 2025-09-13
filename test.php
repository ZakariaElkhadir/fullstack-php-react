<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

$response = [
    "status" => "success",
    "message" => "PHP server is working on Railway!",
    "timestamp" => date("Y-m-d H:i:s"),
    "php_version" => PHP_VERSION,
    "server_info" => [
        "REQUEST_METHOD" => $_SERVER["REQUEST_METHOD"] ?? "Unknown",
        "REQUEST_URI" => $_SERVER["REQUEST_URI"] ?? "Unknown",
        "HTTP_HOST" => $_SERVER["HTTP_HOST"] ?? "Unknown",
        "SERVER_PORT" => $_SERVER["SERVER_PORT"] ?? "Unknown",
        "DOCUMENT_ROOT" => $_SERVER["DOCUMENT_ROOT"] ?? "Unknown",
    ],
    "environment" => [
        "PORT" => $_ENV["PORT"] ?? (getenv("PORT") ?? "Not set"),
        "RAILWAY_ENVIRONMENT" =>
            $_ENV["RAILWAY_ENVIRONMENT"] ??
            (getenv("RAILWAY_ENVIRONMENT") ?? "Not detected"),
        "DB_HOST_SET" =>
            isset($_ENV["DB_HOST"]) || getenv("DB_HOST") ? "Yes" : "No",
        "DATABASE_URL_SET" =>
            isset($_ENV["DATABASE_URL"]) || getenv("DATABASE_URL")
                ? "Yes"
                : "No",
    ],
    "file_checks" => [
        "vendor_autoload" => file_exists(__DIR__ . "/vendor/autoload.php")
            ? "Found"
            : "Missing",
        "backend_index" => file_exists(__DIR__ . "/backend/public/index.php")
            ? "Found"
            : "Missing",
        "composer_json" => file_exists(__DIR__ . "/composer.json")
            ? "Found"
            : "Missing",
    ],
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
