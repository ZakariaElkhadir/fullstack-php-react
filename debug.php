<?php

// Debug script for Railway deployment
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Function to safely output debug info
function debugOutput($label, $data) {
    return [
        'label' => $label,
        'data' => $data,
        'type' => gettype($data)
    ];
}

$debug = [];

// 1. Check PHP version
$debug[] = debugOutput('PHP Version', PHP_VERSION);

// 2. Check if we're running on Railway
$debug[] = debugOutput('Railway Environment', isset($_ENV['RAILWAY_ENVIRONMENT']) ? $_ENV['RAILWAY_ENVIRONMENT'] : 'Not detected');

// 3. Check environment variables (without exposing sensitive data)
$envVars = [
    'DATABASE_URL' => isset($_ENV['DATABASE_URL']) ? 'Present' : 'Missing',
    'DB_HOST' => isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'Missing',
    'DB_NAME' => isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'Missing',
    'DB_USER' => isset($_ENV['DB_USER']) ? 'Present' : 'Missing',
    'DB_PASS' => isset($_ENV['DB_PASS']) ? 'Present' : 'Missing',
    'DB_PORT' => isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : 'Missing',
];

// Also check getenv()
foreach (['DATABASE_URL', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_PORT'] as $var) {
    $getenvValue = getenv($var);
    if ($getenvValue !== false) {
        $envVars[$var . '_getenv'] = $var === 'DB_PASS' ? 'Present' : $getenvValue;
    }
}

$debug[] = debugOutput('Environment Variables', $envVars);

// 4. Check if .env file exists
$envFile = __DIR__ . '/backend/.env';
$debug[] = debugOutput('.env file exists', file_exists($envFile));

// 5. Check composer autoload
$debug[] = debugOutput('Composer autoload exists', file_exists(__DIR__ . '/vendor/autoload.php'));

// 6. Check backend structure
$backendFiles = [
    'backend/public/index.php' => file_exists(__DIR__ . '/backend/public/index.php'),
    'backend/src/Controller/GraphQL.php' => file_exists(__DIR__ . '/backend/src/Controller/GraphQL.php'),
    'backend/src/Config/Database.php' => file_exists(__DIR__ . '/backend/src/Config/Database.php'),
];
$debug[] = debugOutput('Backend Files', $backendFiles);

// 7. Test database connection
try {
    require_once __DIR__ . '/vendor/autoload.php';

    // Try to create database instance
    $dbTest = [
        'connection_attempt' => 'Starting...',
        'error' => null
    ];

    try {
        $db = new App\Config\Database();
        $dbTest['connection_attempt'] = 'Database class instantiated';

        if ($db->isConnected()) {
            $dbTest['connection_status'] = 'Connected successfully';

            // Try a simple query
            $pdo = $db->getConnection();
            $stmt = $pdo->query('SELECT COUNT(*) as product_count FROM products');
            $result = $stmt->fetch();
            $dbTest['product_count'] = $result['product_count'];
        } else {
            $dbTest['connection_status'] = 'Not connected';
        }
    } catch (Exception $e) {
        $dbTest['error'] = $e->getMessage();
    }

    $debug[] = debugOutput('Database Connection Test', $dbTest);

} catch (Exception $e) {
    $debug[] = debugOutput('Database Test Error', $e->getMessage());
}

// 8. Check current working directory
$debug[] = debugOutput('Current Working Directory', getcwd());

// 9. Check $_SERVER variables (relevant ones)
$serverVars = [
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'Not set',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'Not set',
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'Not set',
    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'Not set',
];
$debug[] = debugOutput('Server Variables', $serverVars);

// 10. Memory usage
$debug[] = debugOutput('Memory Usage', [
    'current' => memory_get_usage(true),
    'peak' => memory_get_peak_usage(true)
]);

// Output the debug information
echo json_encode([
    'status' => 'debug',
    'timestamp' => date('Y-m-d H:i:s'),
    'debug_info' => $debug
], JSON_PRETTY_PRINT);
?>
