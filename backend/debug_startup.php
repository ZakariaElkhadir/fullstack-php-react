<?php
// Debug startup script to check directory structure and environment
echo "=== RAILWAY STARTUP DEBUG ===\n";
echo "Current working directory: " . getcwd() . "\n";
echo "Script directory: " . __DIR__ . "\n";
echo "PHP version: " . PHP_VERSION . "\n\n";

echo "=== DIRECTORY STRUCTURE ===\n";
echo "Contents of current directory:\n";
$files = scandir('.');
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $type = is_dir($file) ? '[DIR]' : '[FILE]';
        echo "  $type $file\n";
    }
}

echo "\n=== PUBLIC DIRECTORY ===\n";
if (is_dir('public')) {
    echo "Public directory exists\n";
    $publicFiles = scandir('public');
    foreach ($publicFiles as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  [FILE] public/$file\n";
        }
    }
} else {
    echo "Public directory NOT found!\n";
}

echo "\n=== VENDOR DIRECTORY ===\n";
if (is_dir('vendor')) {
    echo "Vendor directory exists\n";
    if (file_exists('vendor/autoload.php')) {
        echo "Autoloader found at vendor/autoload.php\n";
    } else {
        echo "Autoloader NOT found at vendor/autoload.php\n";
    }
} else {
    echo "Vendor directory NOT found!\n";
}

echo "\n=== ENVIRONMENT VARIABLES ===\n";
echo "PORT: " . ($_ENV['PORT'] ?? 'NOT SET') . "\n";
echo "DATABASE_URL: " . (isset($_ENV['DATABASE_URL']) ? 'SET (hidden)' : 'NOT SET') . "\n";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NOT SET') . "\n";
echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'NOT SET') . "\n";
echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NOT SET') . "\n";

echo "\n=== AUTOLOADER TEST ===\n";
$autoloadPaths = [
    __DIR__ . "/vendor/autoload.php",
    __DIR__ . "/../vendor/autoload.php",
    __DIR__ . "/../../vendor/autoload.php",
];

foreach ($autoloadPaths as $path) {
    echo "Checking: $path - " . (file_exists($path) ? "EXISTS" : "NOT FOUND") . "\n";
}

echo "\n=== END DEBUG ===\n";
?>
