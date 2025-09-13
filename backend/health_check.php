<?php
// Simple health check script for Railway deployment
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

try {
    // Check if autoloader exists
    $autoloadPaths = [
        __DIR__ . "/../vendor/autoload.php",
        __DIR__ . "/../../vendor/autoload.php",
    ];
    
    $autoloaderFound = false;
    foreach ($autoloadPaths as $path) {
        if (file_exists($path)) {
            $autoloaderFound = true;
            break;
        }
    }
    
    if (!$autoloaderFound) {
        throw new Exception("Autoloader not found");
    }
    
    // Check database connection
    require_once __DIR__ . "/../vendor/autoload.php";
    $db = new App\Config\Database();
    
    $response = [
        "status" => "healthy",
        "timestamp" => date("Y-m-d H:i:s"),
        "service" => "GraphQL API",
        "database" => $db->isConnected() ? "connected" : "disconnected",
        "autoloader" => "found",
        "environment" => [
            "php_version" => PHP_VERSION,
            "port" => $_ENV["PORT"] ?? "not_set",
            "database_url_set" => isset($_ENV["DATABASE_URL"]) ? "yes" : "no",
        ]
    ];
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "unhealthy",
        "timestamp" => date("Y-m-d H:i:s"),
        "error" => $e->getMessage(),
        "service" => "GraphQL API"
    ], JSON_PRETTY_PRINT);
}
?>
