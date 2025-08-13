<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Products;

try {
    $products = Products::findAll();
} catch (Exception $e) {
    error_log('DB error :',$e->getMessage());
}
print_r($products);
