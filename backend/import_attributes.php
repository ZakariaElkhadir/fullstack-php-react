<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

try {
    $db = new Database();
    $connection = $db->getConnection();
    
    $jsonData = file_get_contents(__DIR__ . '/data.json');
    $data = json_decode($jsonData, true);
    
    if (!$data || !isset($data['data']['products'])) {
        throw new Exception('Invalid JSON data');
    }
    
    echo "=== Importing Attributes from data.json ===\n";
    
    $allAttributes = [];
    $productAttributes = [];
    
    foreach ($data['data']['products'] as $product) {
        if (isset($product['attributes']) && is_array($product['attributes'])) {
            foreach ($product['attributes'] as $attribute) {
                $attrId = $attribute['id'];
                
                if (!isset($allAttributes[$attrId])) {
                    $allAttributes[$attrId] = [
                        'id' => $attrId,
                        'name' => $attribute['name'],
                        'type' => $attribute['type'],
                        'items' => []
                    ];
                }
                
                if (isset($attribute['items'])) {
                    foreach ($attribute['items'] as $item) {
                        $itemId = $item['id'];
                        if (!isset($allAttributes[$attrId]['items'][$itemId])) {
                            $allAttributes[$attrId]['items'][$itemId] = [
                                'id' => $itemId,
                                'display_value' => $item['displayValue'],
                                'value' => $item['value']
                            ];
                        }
                    }
                }
                
                if (!isset($productAttributes[$product['id']])) {
                    $productAttributes[$product['id']] = [];
                }
                $productAttributes[$product['id']][] = $attrId;
            }
        }
    }
    
    echo "Found " . count($allAttributes) . " unique attributes\n";
    echo "Found attributes for " . count($productAttributes) . " products\n\n";
    
    echo "Clearing existing attribute data...\n";
    $connection->exec("DELETE FROM product_attributes");
    $connection->exec("DELETE FROM attribute_items");
    $connection->exec("DELETE FROM attribute_sets");
    
    echo "Inserting attribute sets...\n";
    $setStmt = $connection->prepare("INSERT INTO attribute_sets (id, name, type) VALUES (?, ?, ?)");
    
    foreach ($allAttributes as $attr) {
        $setStmt->execute([$attr['id'], $attr['name'], $attr['type']]);
        echo "  Added set: {$attr['id']} ({$attr['name']})\n";
    }
    
    echo "Inserting attribute items...\n";
    $itemStmt = $connection->prepare("INSERT INTO attribute_items (id, attribute_set_id, display_value, value) VALUES (?, ?, ?, ?)");
    
    foreach ($allAttributes as $attr) {
        foreach ($attr['items'] as $item) {
            $itemStmt->execute([$item['id'], $attr['id'], $item['display_value'], $item['value']]);
            echo "  Added item: {$item['id']} ({$item['display_value']}) to {$attr['id']}\n";
        }
    }
    
    echo "Inserting product-attribute relationships...\n";
    $productAttrStmt = $connection->prepare("INSERT INTO product_attributes (product_id, attribute_set_id) VALUES (?, ?)");
    
    foreach ($productAttributes as $productId => $attributeIds) {
        foreach ($attributeIds as $attrId) {
            $productAttrStmt->execute([$productId, $attrId]);
            echo "  Linked product $productId to attribute $attrId\n";
        }
    }
    
    echo "\n=== Import Complete ===\n";
    
    $stmt = $connection->query("SELECT COUNT(*) as count FROM attribute_sets");
    $setCount = $stmt->fetch()['count'];
    
    $stmt = $connection->query("SELECT COUNT(*) as count FROM attribute_items");
    $itemCount = $stmt->fetch()['count'];
    
    $stmt = $connection->query("SELECT COUNT(*) as count FROM product_attributes");
    $productAttrCount = $stmt->fetch()['count'];
    
    echo "Imported: $setCount attribute sets, $itemCount items, $productAttrCount product-attribute links\n";
    
    $db->close();
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>