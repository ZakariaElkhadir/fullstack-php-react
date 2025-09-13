<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$health = [
    "status" => "healthy",
    "timestamp" => date("Y-m-d H:i:s"),
    "php_version" => PHP_VERSION,
    "memory_usage" => memory_get_usage(true),
    "server_software" => $_SERVER["SERVER_SOFTWARE"] ?? "Unknown",
];

try {
    if (file_exists(__DIR__ . "/vendor/autoload.php")) {
        require_once __DIR__ . "/vendor/autoload.php";
        $health["autoloader"] = "loaded";
    } else {
        $health["autoloader"] = "missing";
    }
} catch (Exception $e) {
    $health["autoloader"] = "error: " . $e->getMessage();
}

try {
    if (class_exists("App\Config\Database")) {
        $db = new App\Config\Database();
        if ($db->isConnected()) {
            $health["database"] = "connected";
        } else {
            $health["database"] = "not connected";
        }
    } else {
        $health["database"] = "database class not found";
    }
} catch (Exception $e) {
    $health["database"] = "error: " . $e->getMessage();
}

echo json_encode($health, JSON_PRETTY_PRINT);
?>
