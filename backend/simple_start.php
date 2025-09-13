<?php
// Simple PHP server startup script
echo "Starting PHP server...\n";
echo "Port: " . ($_ENV['PORT'] ?? '8000') . "\n";
echo "Current directory: " . getcwd() . "\n";

// Check if we're in the right directory
if (!is_dir('public')) {
    echo "ERROR: public directory not found!\n";
    echo "Current directory contents:\n";
    $files = scandir('.');
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - $file\n";
        }
    }
    exit(1);
}

if (!file_exists('vendor/autoload.php')) {
    echo "ERROR: vendor/autoload.php not found!\n";
    echo "Make sure composer install was run.\n";
    exit(1);
}

echo "Starting server...\n";
$port = $_ENV['PORT'] ?? '8000';
$command = "php -S 0.0.0.0:$port -t public";
echo "Command: $command\n";
passthru($command);
?>
