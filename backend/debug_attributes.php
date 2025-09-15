<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

try {
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "=== Checking Attribute Tables ===\n";
    
    $tables = ['attribute_sets', 'attribute_items', 'product_attributes'];
    foreach ($tables as $table) {
        $stmt = $connection->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        echo "Table '$table': " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
        
        if ($exists) {
            $stmt = $connection->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "  Records: $count\n";
        }
    }
    echo "\n";
    
    echo "=== Attribute Sets ===\n";
    $stmt = $connection->query("SELECT * FROM attribute_sets LIMIT 10");
    $sets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($sets as $set) {
        echo "ID: {$set['id']}, Name: {$set['name']}, Type: {$set['type']}\n";
    }
    echo "\n";
    
    echo "=== Attribute Items ===\n";
    $stmt = $connection->query("SELECT * FROM attribute_items LIMIT 10");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as $item) {
        echo "ID: {$item['id']}, Set: {$item['attribute_set_id']}, Display: {$item['display_value']}, Value: {$item['value']}\n";
    }
    echo "\n";
    
    echo "=== Product Attributes ===\n";
    $stmt = $connection->query("SELECT * FROM product_attributes LIMIT 10");
    $productAttrs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($productAttrs as $attr) {
        echo "Product: {$attr['product_id']}, Attribute: {$attr['attribute_set_id']}\n";
    }
    echo "\n";
    
    echo "=== Testing Attribute Loading for 'classic-denim-jacket' ===\n";
    $productId = 'classic-denim-jacket';
    
    $sql = "SELECT ats.id, ats.name, ats.type,
                   ati.id as item_id, ati.display_value, ati.value
            FROM product_attributes pa
            JOIN attribute_sets ats ON pa.attribute_set_id = ats.id
            LEFT JOIN attribute_items ati ON ats.id = ati.attribute_set_id
            WHERE pa.product_id = ?
            ORDER BY ats.name, ati.display_value";

    $stmt = $connection->prepare($sql);
    $stmt->execute([$productId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Raw results count: " . count($results) . "\n";
    foreach ($results as $row) {
        echo "Set: {$row['id']} ({$row['name']}), Item: " . ($row['item_id'] ?? 'NULL') . " ({$row['display_value']})\n";
    }
    
    $db->close();
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>