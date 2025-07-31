<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host  = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$port = $_ENV['DB_PORT'] ?? 3306;


$dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASS']);
$dsn = "mysql:host=$host;port=$port;dbname=$dbname";



try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful 😃" . "\n";

    $jsonData = file_get_contents('data.json');
    $data = json_decode($jsonData, true);
    // print_r($data);
$stmt = $pdo->prepare("INSERT IGNORE INTO products(id, name, description, brand, category_name, in_stock) VALUES(?, ?, ?, ?, ?, ?)");
foreach($data['data']['products'] as $product){
    $stmt->execute([
        $product['id'],
        $product['name'],
        $product['description'],
        $product['brand'],
        $product['category'],
        $product['inStock'] ? 1 : 0,
    ]);
    echo "product imported successfully" . "\n";

    }



} catch (PDOException $e) {
    die("😢Connection failed: " . $e->getMessage());
}
