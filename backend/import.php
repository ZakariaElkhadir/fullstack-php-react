<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$port = $_ENV['DB_PORT'] ?? 3306;

$dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASS']);
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    echo "Connection successful 😃 \n";

    $jsonFile = 'data.json';
    if (!file_exists($jsonFile)) {
        die("Error: data.json file not found!\n");
    }

    $jsonData = file_get_contents($jsonFile);
    if ($jsonData === false) {
        die("Error: Could not read data.json file!\n");
    }

    $data = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error: Invalid JSON data - " . json_last_error_msg() . "\n");
    }

    if (!isset($data['data']['products']) || !is_array($data['data']['products'])) {
        die("Error: Invalid data structure - 'data.products' not found or not an array\n");
    }

    echo "Found " . count($data['data']['products']) . " products to import\n";

    $requiredTables = ['products', 'categories', 'product_galleries', 'product_prices', 'currencies'];
    foreach ($requiredTables as $table) {
        $tableCheck = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($tableCheck->rowCount() === 0) {
            die("Error: '$table' table does not exist in the database!\n");
        }
    }

    $pdo->beginTransaction();

    try {
        echo "\n=== Importing Categories ===\n";
        $requiredCategories = array_unique(array_filter(array_column($data['data']['products'], 'category')));

        $existingCategoriesStmt = $pdo->query("SELECT name FROM categories");
        $existingCategories = $existingCategoriesStmt->fetchAll(PDO::FETCH_COLUMN);

        $categoryInsertStmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        $categoryCount = 0;

        foreach ($requiredCategories as $category) {
            if (!empty($category) && !in_array($category, $existingCategories)) {
                $categoryInsertStmt->execute([$category]);
                if ($categoryInsertStmt->rowCount() > 0) {
                    $categoryCount++;
                    echo "✓ Inserted category: $category\n";
                }
            }
        }
        echo "Categories imported: $categoryCount\n";

        echo "\n=== Importing Currencies ===\n";
        $currencyInsertStmt = $pdo->prepare("
            INSERT INTO currencies (code, label, symbol) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            label = VALUES(label), 
            symbol = VALUES(symbol)
        ");

        $processedCurrencies = [];
        $currencyCount = 0;

        foreach ($data['data']['products'] as $product) {
            if (!empty($product['prices']) && is_array($product['prices'])) {
                foreach ($product['prices'] as $price) {
                    if (isset($price['currency']['label']) && isset($price['currency']['symbol'])) {
                        $currencyCode = $price['currency']['label'];

                        if (!isset($processedCurrencies[$currencyCode])) {
                            $currencyInsertStmt->execute([
                                $currencyCode,
                                $price['currency']['label'],
                                $price['currency']['symbol']
                            ]);

                            if ($currencyInsertStmt->rowCount() > 0) {
                                $currencyCount++;
                                echo "✓ Imported currency: {$price['currency']['label']} ({$price['currency']['symbol']})\n";
                            }

                            $processedCurrencies[$currencyCode] = true;
                        }
                    }
                }
            }
        }
        echo "Currencies imported: $currencyCount\n";

        echo "\n=== Importing Products ===\n";
        $productStmt = $pdo->prepare("
            INSERT INTO products (id, name, description, brand, category_name, in_stock, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            description = VALUES(description),
            brand = VALUES(brand),
            category_name = VALUES(category_name),
            in_stock = VALUES(in_stock),
            updated_at = NOW()
        ");

        $productCount = 0;
        $productUpdatedCount = 0;

        $existingProductsStmt = $pdo->query("SELECT id FROM products");
        $existingProductIds = $existingProductsStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($data['data']['products'] as $index => $product) {
            if (empty($product['id']) || empty($product['name'])) {
                echo "⚠ Skipping product at index $index - missing ID or name\n";
                continue;
            }

            $isUpdate = in_array($product['id'], $existingProductIds);
            $cleanDescription = strip_tags($product['description'] ?? '');

            try {
                $productStmt->execute([
                    $product['id'],
                    $product['name'],
                    $cleanDescription,
                    $product['brand'] ?? '',
                    $product['category'] ?? '',
                    !empty($product['inStock']) ? 1 : 0
                ]);

                if ($productStmt->rowCount() > 0) {
                    if ($isUpdate) {
                        $productUpdatedCount++;
                        echo "↻ Updated product: {$product['name']} (ID: {$product['id']})\n";
                    } else {
                        $productCount++;
                        echo "✓ Inserted product: {$product['name']} (ID: {$product['id']})\n";
                    }
                }
            } catch (PDOException $e) {
                echo "✗ Error with product {$product['id']}: " . $e->getMessage() . "\n";
            }
        }

        echo "Products inserted: $productCount, updated: $productUpdatedCount\n";

        echo "\n=== Importing Product Galleries ===\n";

        $productIds = array_filter(array_column($data['data']['products'], 'id'));
        if (!empty($productIds)) {
            $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
            $deleteGalleryStmt = $pdo->prepare("DELETE FROM product_galleries WHERE product_id IN ($placeholders)");
            $deleteGalleryStmt->execute($productIds);
            echo "Cleaned existing gallery images for imported products\n";
        }

        $galleryStmt = $pdo->prepare("INSERT INTO product_galleries (product_id, image_url, sort_order) VALUES (?, ?, ?)");
        $galleryCount = 0;

        foreach ($data['data']['products'] as $product) {
            if (!empty($product['gallery']) && is_array($product['gallery']) && !empty($product['id'])) {
                foreach ($product['gallery'] as $index => $imageUrl) {
                    if (!empty($imageUrl)) {
                        try {
                            $galleryStmt->execute([
                                $product['id'],
                                $imageUrl,
                                $index
                            ]);
                            $galleryCount++;
                        } catch (PDOException $e) {
                            echo "⚠ Error inserting gallery image for product {$product['id']}: " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
        }
        echo "Gallery images imported: $galleryCount\n";

        echo "\n=== Importing Product Prices ===\n";

        if (!empty($productIds)) {
            $deletePricesStmt = $pdo->prepare("DELETE FROM product_prices WHERE product_id IN ($placeholders)");
            $deletePricesStmt->execute($productIds);
            echo "Cleaned existing prices for imported products\n";
        }

        $pricesStmt = $pdo->prepare("INSERT INTO product_prices (product_id, amount, currency_code, sort_order) VALUES (?, ?, ?, ?)");
        $pricesCount = 0;

        foreach ($data['data']['products'] as $product) {
            if (!empty($product['prices']) && is_array($product['prices']) && !empty($product['id'])) {
                foreach ($product['prices'] as $index => $price) {
                    if (isset($price['amount']) && isset($price['currency']['label'])) {
                        try {
                            $pricesStmt->execute([
                                $product['id'],
                                floatval($price['amount']),
                                $price['currency']['label'],
                                $index
                            ]);
                            $pricesCount++;
                        } catch (PDOException $e) {
                            echo "⚠ Error inserting price for product {$product['id']}: " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
        }
        echo "Prices imported: $pricesCount\n";

        $pdo->commit();

        echo "\n=== Import Summary ===\n";
        echo "✓ Categories imported: $categoryCount\n";
        echo "✓ Currencies imported: $currencyCount\n";
        echo "✓ Products inserted: $productCount\n";
        echo "✓ Products updated: $productUpdatedCount\n";
        echo "✓ Gallery images imported: $galleryCount\n";
        echo "✓ Prices imported: $pricesCount\n";

        $totalProductsStmt = $pdo->query("SELECT COUNT(*) FROM products");
        $totalProducts = $totalProductsStmt->fetchColumn();
        echo "✓ Total products in database: $totalProducts\n";

        echo "\nSample records:\n";
        $sampleStmt = $pdo->query("
            SELECT p.id, p.name, p.brand, p.category_name, 
                   COUNT(DISTINCT pg.id) as gallery_count,
                   COUNT(DISTINCT pp.id) as price_count
            FROM products p 
            LEFT JOIN product_galleries pg ON p.id = pg.product_id 
            LEFT JOIN product_prices pp ON p.id = pp.product_id 
            GROUP BY p.id 
            LIMIT 3
        ");

        while ($row = $sampleStmt->fetch()) {
            echo "- {$row['name']} (ID: {$row['id']}, Gallery: {$row['gallery_count']} images, Prices: {$row['price_count']})\n";
        }

        echo "\n🎉 Import completed successfully!\n";
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
} catch (PDOException $e) {
    die("😢 Database error: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("😢 General error: " . $e->getMessage() . "\n");
}
