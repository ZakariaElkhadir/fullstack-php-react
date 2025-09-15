<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

try {
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "=== Database Connection Test ===\n";
    echo "Connection: " . ($db->isConnected() ? "SUCCESS" : "FAILED") . "\n\n";
    
    echo "=== Products Table Structure ===\n";
    $stmt = $connection->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "{$column['Field']} | {$column['Type']} | {$column['Null']} | {$column['Key']} | {$column['Default']}\n";
    }
    echo "\n";
    
    echo "=== Products Count ===\n";
    $stmt = $connection->query("SELECT COUNT(*) as total FROM products");
    $result = $stmt->fetch();
    echo "Total products: " . $result['total'] . "\n\n";
    
    echo "=== Products by Category ===\n";
    $stmt = $connection->query("SELECT category_name, COUNT(*) as count FROM products GROUP BY category_name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($categories as $category) {
        $catName = $category['category_name'] ?? 'NULL';
        echo "Category '{$catName}': {$category['count']} products\n";
    }
    echo "\n";
    
    echo "=== Sample Products ===\n";
    $stmt = $connection->query("SELECT id, name, category_name, brand, in_stock FROM products LIMIT 5");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as $product) {
        echo "ID: {$product['id']}\n";
        echo "Name: {$product['name']}\n";
        echo "Category: " . ($product['category_name'] ?? 'NULL') . "\n";
        echo "Brand: " . ($product['brand'] ?? 'NULL') . "\n";
        echo "In Stock: " . ($product['in_stock'] ? 'YES' : 'NO') . "\n";
        echo "---\n";
    }
    
    echo "\n=== Testing Category Queries ===\n";
    
    $stmt = $connection->prepare("SELECT COUNT(*) as count FROM products WHERE category_name = ?");
    $stmt->execute(['clothes']);
    $clothesCount = $stmt->fetch()['count'];
    echo "Clothes products: $clothesCount\n";
    
    $stmt->execute(['tech']);
    $techCount = $stmt->fetch()['count'];
    echo "Tech products: $techCount\n";
    
    $db->close();
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>