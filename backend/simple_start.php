<?php
// Simple PHP server startup script
echo "=== RAILWAY PHP SERVER STARTUP ===\n";
echo "Current directory: " . getcwd() . "\n";
echo "PHP version: " . PHP_VERSION . "\n";

// Check environment variables
$port = $_ENV['PORT'] ?? '8000';
echo "Port from environment: $port\n";

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

echo "All checks passed. Starting server...\n";
echo "Binding to: 0.0.0.0:$port\n";
echo "Document root: public/\n";
echo "===============================\n";

// Start the server
$command = "php -S 0.0.0.0:$port -t public";
passthru($command);
?>
