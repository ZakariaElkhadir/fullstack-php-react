<?php
require_once __DIR__ . '/vendor/autoload.php';

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
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful 😃 \n";

    // Check if data.json file exists
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

    // Check if data structure is correct
    if (!isset($data['data']['products']) || !is_array($data['data']['products'])) {
        die("Error: Invalid data structure - 'data.products' not found or not an array\n");
    }

    echo "Found " . count($data['data']['products']) . " products to import\n";

    // Check if table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($tableCheck->rowCount() === 0) {
        die("Error: 'products' table does not exist in the database!\n");
    }

    // Check if categories table exists and insert missing categories
    $categoriesCheck = $pdo->query("SHOW TABLES LIKE 'categories'");
    if ($categoriesCheck->rowCount() > 0) {
        echo "Categories table found, checking for required categories...\n";

        // Get unique categories from the JSON data
        $requiredCategories = array_unique(array_column($data['data']['products'], 'category'));
        echo "Required categories: " . implode(', ', $requiredCategories) . "\n";

        // Check existing categories
        $existingCategoriesStmt = $pdo->query("SELECT name FROM categories");
        $existingCategories = $existingCategoriesStmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Existing categories: " . implode(', ', $existingCategories) . "\n";

        // Insert missing categories
        $categoryInsertStmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        foreach ($requiredCategories as $category) {
            if (!in_array($category, $existingCategories)) {
                $categoryInsertStmt->execute([$category]);
                echo "✓ Inserted missing category: $category\n";
            }
        }
    } else {
        echo "Warning: categories table not found, but foreign key constraint exists\n";
    }

    // Show table structure for debugging
    echo "Table structure:\n";
    $columns = $pdo->query("DESCRIBE products");
    while ($column = $columns->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }

    // Prepare the statement (using INSERT IGNORE to avoid duplicate errors)
    $stmt = $pdo->prepare("INSERT IGNORE INTO products (id, name, description, brand, category_name, in_stock) VALUES (?, ?, ?, ?, ?, ?)");
    $importedCount = 0;

    foreach ($data['data']['products'] as $index => $product) {
        // Debug: show what we're trying to insert
        echo "\nProduct " . ($index + 1) . ": {$product['name']}\n";
        echo "- ID: {$product['id']}\n";
        echo "- Brand: {$product['brand']}\n";
        echo "- Category: {$product['category']}\n";
        echo "- In Stock: " . ($product['inStock'] ? 'Yes' : 'No') . "\n";

        // Clean up description (remove HTML tags for database storage)
        $cleanDescription = strip_tags($product['description']);

        try {
            $result = $stmt->execute([
                $product['id'],
                $product['name'],
                $cleanDescription,
                $product['brand'],
                $product['category'],
                $product['inStock'] ? 1 : 0,
            ]);

            if ($result) {
                // Check if row was actually inserted (INSERT IGNORE might skip duplicates)
                $rowCount = $stmt->rowCount();
                if ($rowCount > 0) {
                    $importedCount++;
                    echo "✓ Product imported successfully (affected rows: $rowCount)\n";
                } else {
                    echo "⚠ Product skipped (likely duplicate ID: {$product['id']})\n";
                }
            } else {
                echo "✗ Failed to import product\n";
                $errorInfo = $stmt->errorInfo();
                echo "Error: " . $errorInfo[2] . "\n";
            }
        } catch (PDOException $e) {
            echo "✗ Database error importing product: " . $e->getMessage() . "\n";
            echo "  SQL State: " . $e->getCode() . "\n";
        }
    }

    echo "\n=== Import Summary ===\n";
    echo "Total products processed: " . count($data['data']['products']) . "\n";
    echo "Successfully imported: $importedCount\n";

    // Verify data was inserted
    $countStmt = $pdo->query("SELECT COUNT(*) FROM products");
    $totalInDb = $countStmt->fetchColumn();
    echo "Total products in database: $totalInDb\n";

    // Show a few sample records
    echo "\nSample records from database:\n";
    $sampleStmt = $pdo->query("SELECT id, name, brand, category_name FROM products LIMIT 3");
    while ($row = $sampleStmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['name']} (ID: {$row['id']}, Brand: {$row['brand']}, Category: {$row['category_name']})\n";
    }
    $galleryStmt = $pdo->prepare("INSERT IGNORE INTO product_galleries (product_id, image_url, sort_order) VALUES(?, ?, ?)");
    $galleryCount = 0;

    foreach ($data['data']['products'] as $product) {
        if (!empty($product['gallery']) && is_array($product['gallery'])) {
            foreach ($product['gallery'] as $index => $imageUrl) {
                $galleryStmt->execute([
                    $product['id'],
                    $imageUrl,
                    $index
                ]);
                $galleryCount += $galleryStmt->rowCount();
                // print_r($index);
                echo "Successfully imported $galleryCount gallery images\n";
            }
        }
    }

    $pricesStmt = $pdo->prepare("INSERT IGNORE INTO product_prices (product_id, amount, currency_code, sort_order) VALUES (?,?,?,?)");
    $pricesCount = 0;

    $currencyStmt = $pdo->prepare("INSERT IGNORE INTO currencies (code, label, symbol) VALUES (?,?,?)");


    foreach ($data['data']['products'] as $product) {
        if (!empty($product['prices']) && is_array($product['prices'])) {
            foreach ($product['prices'] as $index => $price) {
                try {
                    $pricesStmt->execute([
                        $product['id'],
                        $price['amount'],
                        $price['currency']['label'], 
                        $index 
                    ]);
                    $pricesCount += $pricesStmt->rowCount();
                } catch (PDOException $e) {
                    echo "ERROR inserting price: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    echo "Successfully imported $pricesCount prices\n";



    $currencySet = [];
    $currencyCount = 0;

    foreach ($data['data']['products'] as $product) {
        if (!empty($product['prices']) && is_array($product['prices'])) {
            foreach ($product['prices'] as $price) {
                $currency = $price['currency'];
                if (!empty($currency['label']) && !empty($currency['symbol'])) {
                    $key = $currency['label'] . '|' . $currency['symbol'];
                    if (!isset($currencySet[$key])) {
                        $currencyStmt->execute([
                            $currency['label'],
                            $currency['label'],
                            $currency['symbol']
                        ]);
                        $currencySet[$key] = true;
                        $currencyCount++;
                        echo "✓ Imported currency: {$currency['label']} ({$currency['symbol']})\n";
                    }
                }
            }
        }
    }
    echo "Successfully imported $currencyCount currencies\n";
} catch (PDOException $e) {
    die("😢 Database error: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("😢 General error: " . $e->getMessage() . "\n");
}
