<?php

require_once __DIR__ . "/../vendor/autoload.php";

use App\Models\ClothesProduct;

echo "=== Testing ClothesProduct Class ===\n\n";

// Test 1: Test processAttributes with Size (text values)
echo "1. Testing Size attribute with text values (XS, S, M, L):\n";
$rawAttributesText = [
    [
        "id" => "size",
        "name" => "Size",
        "type" => "text",
        "items" => [
            ["id" => "xs", "display_value" => "Extra Small", "value" => "XS"],
            ["id" => "m", "display_value" => "Medium", "value" => "M"],
            ["id" => "l", "display_value" => "Large", "value" => "L"],
        ],
    ],
];

$product = new ClothesProduct(
    "test",
    "Test Product",
    [],
    [],
    "clothes",
    true,
    "Test Brand",
    "Test Description",
    $rawAttributesText,
);
$attributes = $product->getAttributes();
echo "Processed attributes:\n";
print_r($attributes);
echo "\n";

// Test 2: Test processAttributes with Size (numeric values)
echo "2. Testing Size attribute with numeric values (7, 8, 9):\n";
$rawAttributesNumeric = [
    [
        "id" => "size",
        "name" => "Size",
        "type" => "text",
        "items" => [
            ["id" => "7", "display_value" => "Size 7", "value" => "7"],
            ["id" => "8", "display_value" => "Size 8", "value" => "8"],
            ["id" => "9", "display_value" => "Size 9", "value" => "9"],
        ],
    ],
];

$product2 = new ClothesProduct(
    "test2",
    "Test Product 2",
    [],
    [],
    "clothes",
    true,
    "Test Brand",
    "Test Description",
    $rawAttributesNumeric,
);
$attributes2 = $product2->getAttributes();
echo "Processed attributes:\n";
print_r($attributes2);
echo "\n";

// Test 3: Test swatch type (Color)
echo "3. Testing Color swatch attribute:\n";
$rawAttributesSwatch = [
    [
        "id" => "color",
        "name" => "Color",
        "type" => "swatch",
        "items" => [
            ["id" => "red", "display_value" => "Red", "value" => "#FF0000"],
            ["id" => "blue", "display_value" => "Blue", "value" => "#0000FF"],
        ],
    ],
];

$product3 = new ClothesProduct(
    "test3",
    "Test Product 3",
    [],
    [],
    "clothes",
    true,
    "Test Brand",
    "Test Description",
    $rawAttributesSwatch,
);
$attributes3 = $product3->getAttributes();
echo "Processed attributes:\n";
print_r($attributes3);
echo "\n";

// Test 4: Test multiple attributes together
echo "4. Testing multiple attributes together:\n";
$rawAttributesMixed = [
    [
        "id" => "size",
        "name" => "Size",
        "type" => "text",
        "items" => [
            ["id" => "xs", "display_value" => "Extra Small", "value" => "XS"],
            ["id" => "s", "display_value" => "Small", "value" => "S"],
        ],
    ],
    [
        "id" => "color",
        "name" => "Color",
        "type" => "swatch",
        "items" => [
            ["id" => "red", "display_value" => "Red", "value" => "#FF0000"],
        ],
    ],
];

$product4 = new ClothesProduct(
    "test4",
    "Test Product 4",
    [],
    [],
    "clothes",
    true,
    "Test Brand",
    "Test Description",
    $rawAttributesMixed,
);
$attributes4 = $product4->getAttributes();
echo "Processed attributes:\n";
print_r($attributes4);
echo "\n";

echo "=== Testing Complete ===\n";
