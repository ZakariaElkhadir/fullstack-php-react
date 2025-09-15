<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

try {
    $db = new Database();
    $connection = $db->getConnection();
    
    echo "=== Importing Product-Specific Attributes ===\n";
    
    $connection->exec("DELETE FROM product_attributes");
    $connection->exec("DELETE FROM attribute_items");
    $connection->exec("DELETE FROM attribute_sets");
    
    $productAttributes = [
        'classic-denim-jacket' => [
            'Size' => [
                'type' => 'text',
                'items' => [
                    ['id' => 'xs', 'display_value' => 'XS', 'value' => 'Extra Small'],
                    ['id' => 's', 'display_value' => 'S', 'value' => 'Small'],
                    ['id' => 'm', 'display_value' => 'M', 'value' => 'Medium'],
                    ['id' => 'l', 'display_value' => 'L', 'value' => 'Large'],
                    ['id' => 'xl', 'display_value' => 'XL', 'value' => 'Extra Large'],
                    ['id' => 'xxl', 'display_value' => 'XXL', 'value' => 'Extra Extra Large'],
                ]
            ],
            'Color' => [
                'type' => 'swatch',
                'items' => [
                    ['id' => 'light-blue', 'display_value' => 'Light Blue', 'value' => '#87CEEB'],
                    ['id' => 'dark-blue', 'display_value' => 'Dark Blue', 'value' => '#003366'],
                    ['id' => 'black', 'display_value' => 'Black', 'value' => '#000000'],
                ]
            ]
        ],
        'cotton-white-tshirt' => [
            'Size' => [
                'type' => 'text',
                'items' => [
                    ['id' => 'xs', 'display_value' => 'XS', 'value' => 'Extra Small'],
                    ['id' => 's', 'display_value' => 'S', 'value' => 'Small'],
                    ['id' => 'm', 'display_value' => 'M', 'value' => 'Medium'],
                    ['id' => 'l', 'display_value' => 'L', 'value' => 'Large'],
                    ['id' => 'xl', 'display_value' => 'XL', 'value' => 'Extra Large'],
                ]
            ],
            'Color' => [
                'type' => 'swatch',
                'items' => [
                    ['id' => 'white', 'display_value' => 'White', 'value' => '#FFFFFF'],
                    ['id' => 'black', 'display_value' => 'Black', 'value' => '#000000'],
                    ['id' => 'gray', 'display_value' => 'Gray', 'value' => '#808080'],
                    ['id' => 'navy', 'display_value' => 'Navy', 'value' => '#000080'],
                ]
            ]
        ],
        'running-sneakers' => [
            'Size' => [
                'type' => 'text',
                'items' => [
                    ['id' => '7', 'display_value' => '7', 'value' => '7'],
                    ['id' => '8', 'display_value' => '8', 'value' => '8'],
                    ['id' => '9', 'display_value' => '9', 'value' => '9'],
                    ['id' => '10', 'display_value' => '10', 'value' => '10'],
                    ['id' => '11', 'display_value' => '11', 'value' => '11'],
                    ['id' => '12', 'display_value' => '12', 'value' => '12'],
                ]
            ],
            'Color' => [
                'type' => 'swatch',
                'items' => [
                    ['id' => 'blue', 'display_value' => 'Blue', 'value' => '#0066CC'],
                    ['id' => 'black', 'display_value' => 'Black', 'value' => '#000000'],
                    ['id' => 'white', 'display_value' => 'White', 'value' => '#FFFFFF'],
                    ['id' => 'red', 'display_value' => 'Red', 'value' => '#FF0000'],
                ]
            ]
        ],
        'wireless-headphones' => [
            'Color' => [
                'type' => 'swatch',
                'items' => [
                    ['id' => 'black', 'display_value' => 'Black', 'value' => '#000000'],
                    ['id' => 'white', 'display_value' => 'White', 'value' => '#FFFFFF'],
                    ['id' => 'silver', 'display_value' => 'Silver', 'value' => '#C0C0C0'],
                ]
            ],
            'Noise Cancellation' => [
                'type' => 'text',
                'items' => [
                    ['id' => 'active', 'display_value' => 'Active', 'value' => 'Active'],
                    ['id' => 'passive', 'display_value' => 'Passive', 'value' => 'Passive'],
                ]
            ]
        ],
        'gaming-laptop' => [
            'RAM' => [
                'type' => 'text',
                'items' => [
                    ['id' => '16gb', 'display_value' => '16GB', 'value' => '16GB'],
                    ['id' => '32gb', 'display_value' => '32GB', 'value' => '32GB'],
                ]
            ],
            'Storage' => [
                'type' => 'text',
                'items' => [
                    ['id' => '512gb', 'display_value' => '512GB SSD', 'value' => '512GB'],
                    ['id' => '1tb', 'display_value' => '1TB SSD', 'value' => '1TB'],
                ]
            ],
            'Color' => [
                'type' => 'swatch',
                'items' => [
                    ['id' => 'black', 'display_value' => 'Black', 'value' => '#000000'],
                    ['id' => 'rgb', 'display_value' => 'RGB', 'value' => '#FF00FF'],
                ]
            ]
        ],
        'smartphone-pro' => [
            'Storage' => [
                'type' => 'text',
                'items' => [
                    ['id' => '128gb', 'display_value' => '128GB', 'value' => '128GB'],
                    ['id' => '256gb', 'display_value' => '256GB', 'value' => '256GB'],
                    ['id' => '512gb', 'display_value' => '512GB', 'value' => '512GB'],
                ]
            ],
            'Color' => [
                'type' => 'swatch',
                'items' => [
                    ['id' => 'midnight-black', 'display_value' => 'Midnight Black', 'value' => '#191970'],
                    ['id' => 'pearl-white', 'display_value' => 'Pearl White', 'value' => '#F8F6F0'],
                    ['id' => 'ocean-blue', 'display_value' => 'Ocean Blue', 'value' => '#006994'],
                    ['id' => 'rose-gold', 'display_value' => 'Rose Gold', 'value' => '#E8B4B8'],
                ]
            ]
        ],
        'smart-watch' => [
            'Band Material' => [
                'type' => 'text',
                'items' => [
                    ['id' => 'sport', 'display_value' => 'Sport Band', 'value' => 'Sport'],
                    ['id' => 'leather', 'display_value' => 'Leather', 'value' => 'Leather'],
                    ['id' => 'metal', 'display_value' => 'Metal', 'value' => 'Metal'],
                ]
            ],
            'Size' => [
                'type' => 'text',
                'items' => [
                    ['id' => '40mm', 'display_value' => '40mm', 'value' => '40mm'],
                    ['id' => '44mm', 'display_value' => '44mm', 'value' => '44mm'],
                ]
            ]
        ],
        'wireless-earbuds' => [
            'Color' => [
                'type' => 'swatch',
                'items' => [
                    ['id' => 'white', 'display_value' => 'White', 'value' => '#FFFFFF'],
                    ['id' => 'black', 'display_value' => 'Black', 'value' => '#000000'],
                ]
            ]
        ]
    ];
    
    $setStmt = $connection->prepare("INSERT INTO attribute_sets (id, name, type) VALUES (?, ?, ?)");
    $createdSets = [];
    
    foreach ($productAttributes as $productId => $attributes) {
        foreach ($attributes as $attrName => $attrData) {
            $setId = $attrName;
            if (!isset($createdSets[$setId])) {
                $setStmt->execute([$setId, $attrName, $attrData['type']]);
                $createdSets[$setId] = true;
                echo "Created attribute set: $attrName\n";
            }
        }
    }
    
    $itemStmt = $connection->prepare("INSERT INTO attribute_items (id, attribute_set_id, display_value, value) VALUES (?, ?, ?, ?)");
    $createdItems = [];
    
    foreach ($productAttributes as $productId => $attributes) {
        foreach ($attributes as $attrName => $attrData) {
            foreach ($attrData['items'] as $item) {
                $itemKey = $attrName . '_' . $item['id'];
                if (!isset($createdItems[$itemKey])) {
                    $itemStmt->execute([$item['id'], $attrName, $item['display_value'], $item['value']]);
                    $createdItems[$itemKey] = true;
                    echo "Created item: {$item['display_value']} for $attrName\n";
                }
            }
        }
    }
    
    $productAttrStmt = $connection->prepare("INSERT INTO product_attributes (product_id, attribute_set_id) VALUES (?, ?)");
    
    foreach ($productAttributes as $productId => $attributes) {
        foreach ($attributes as $attrName => $attrData) {
            $productAttrStmt->execute([$productId, $attrName]);
            echo "Linked product $productId to attribute $attrName\n";
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