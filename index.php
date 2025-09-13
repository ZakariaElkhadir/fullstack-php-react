<?php

// Railway-compatible PHP entry point
error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", 1);

// Set CORS headers for all responses
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle CORS preflight requests
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

try {
    // Root directory
    $rootDir = __DIR__;

    // Check if this is a direct file request (test.php, health.php, debug.php)
    $requestUri = $_SERVER["REQUEST_URI"] ?? "";
    $parsedUri = parse_url($requestUri, PHP_URL_PATH);

    // Handle direct file requests
    if ($parsedUri && preg_match('/\.(php)$/', $parsedUri)) {
        $fileName = basename($parsedUri);
        $filePath = $rootDir . "/" . $fileName;

        // Only allow specific files for security
        $allowedFiles = ["test.php", "health.php", "debug.php"];

        if (in_array($fileName, $allowedFiles) && file_exists($filePath)) {
            require_once $filePath;
            exit();
        }
    }

    // Verify critical files exist
    $backendIndexPath = $rootDir . "/backend/public/index.php";
    $autoloadPath = $rootDir . "/vendor/autoload.php";

    if (!file_exists($backendIndexPath)) {
        throw new Exception("Backend entry point not found: $backendIndexPath");
    }

    if (!file_exists($autoloadPath)) {
        throw new Exception("Composer autoload not found: $autoloadPath");
    }

    // Set document root and change directory
    $_SERVER["DOCUMENT_ROOT"] = $rootDir . "/backend/public";
    chdir($rootDir . "/backend/public");

    // Include the backend application
    require_once $backendIndexPath;
} catch (Throwable $e) {
    // Log the error
    error_log("Application Error: " . $e->getMessage());

    // Return proper JSON error response
    header("Content-Type: application/json");
    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" => "Application failed to start",
        "error" => $e->getMessage(),
        "timestamp" => date("Y-m-d H:i:s"),
        "php_version" => PHP_VERSION,
    ]);

    exit();
}
