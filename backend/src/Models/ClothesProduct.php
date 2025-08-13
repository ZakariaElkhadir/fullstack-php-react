<?php

namespace App\models;

use App\Config\Database;
use PDO;

class ClothesProduct extends AbstractProduct
{


    protected function processAttributes(array $rawAttributes): array
    {
        $processed = [];
        foreach ($rawAttributes as $attribute) {
            if (isset($attribute['id'], $attribute['name'], $attribute['type'])) {
                $processedAttribute[] = [
                    'id' => $attribute['id'],
                    'name' => $attribute['name'],
                    'type' => $attribute['type'],
                    'items' => $attribute['items'] ?? []
                ];
                if ($attribute['name'] === 'Size') {
                    $firstItem = $attribute['items'][0]['value'] ?? '';
                    if (is_numeric($firstItem)) {
                        $processedAttribute['displayOrder'] = ['7', '8', '9', '10', '11', '12'];
                    } else {
                        $processedAttribute['displayOrder'] = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                    }
                }
                if ($attribute['type'] === 'swatch') {
                    $processedAttribute['isColorSwatch'] = true;
                }
                $processed[] = $processedAttribute;
            }
        }
        return $processed;
    }

    /**
     * @throws \Exception
     */
    public static function findAll(): array
    {
        $db = new Database();

        $connection = $db->getConnection();
        $category = 'clothes';
        $stmt = $connection->prepare("SELECT * FROM products WHERE category_name = ?");
        $stmt->execute([$category]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = [];
        foreach ($results as $row) {
            $products[] = static::createFromArray($row, $connection);
        }
        $db->close();
        return $products;

    }

    /**
     * may add this futures
     *  Find clothes by size
     * public static function findBySize(string $size): array
     * Find clothes by color
     * public static function findByColor(string $color): array
     */

}