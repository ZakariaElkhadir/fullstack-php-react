<?php
// Simple test to check if autoloader is working
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

try {
    // Try multiple possible autoloader locations for different deployment scenarios
    $autoloadPaths = [
        __DIR__ . "/../../vendor/autoload.php", 
        __DIR__ . "/../vendor/autoload.php",    
        __DIR__ . "/../../../../vendor/autoload.php", 
    ];
    
    $autoloaderFound = false;
    $foundPath = null;
    
    foreach ($autoloadPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $autoloaderFound = true;
            $foundPath = $path;
            break;
        }
    }
    
    if (!$autoloaderFound) {
        throw new Exception("Autoloader not found. Checked paths: " . implode(", ", $autoloadPaths));
    }
    
    // Test if we can load a class
    $testClass = new App\Config\Database();
    
    echo json_encode([
        "status" => "success",
        "autoloader" => "found",
        "autoloader_path" => $foundPath,
        "database_class" => "loaded",
        "timestamp" => date("Y-m-d H:i:s")
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "error" => $e->getMessage(),
        "timestamp" => date("Y-m-d H:i:s")
    ], JSON_PRETTY_PRINT);
}
?>
