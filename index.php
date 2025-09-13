<?php

error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", 1);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

try {
    $rootDir = __DIR__;

    $_SERVER["DOCUMENT_ROOT"] = $rootDir . "/backend/public";

    $backendIndexPath = $rootDir . "/backend/public/index.php";
    $autoloadPath = $rootDir . "/vendor/autoload.php";

    if (!file_exists($backendIndexPath)) {
        throw new Exception("Backend entry point not found: $backendIndexPath");
    }

    if (!file_exists($autoloadPath)) {
        throw new Exception("Composer autoload not found: $autoloadPath");
    }

    chdir($rootDir . "/backend/public");

    require_once $backendIndexPath;
} catch (Throwable $e) {
    error_log("Application Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Application failed to start",
        "error" => $e->getMessage(),
        "timestamp" => date("Y-m-d H:i:s"),
    ]);
    exit();
}
