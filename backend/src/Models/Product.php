<?php

namespace App\Models;

use Exception;

class Product extends AbstractProduct
{
    /**
     * Find product by ID and load all its data
     */
    public static function findById(string $productId): ?Product
    {
        try {
            $product = new self();

            if ($product->loadBasicData($productId)) {
                $product->loadGallery();
                $product->loadPrices();
                $product->loadAttributes();

                return $product;
            }

            return null;
        } catch (Exception $e) {
            throw new Exception("Error finding product by ID: " . $e->getMessage());
        }
    }

    /**
     * Get all products with their complete data
     */
    public static function findAll(?string $category = null): array
    {
        try {
            $database = new \App\Config\Database();
            $connection = $database->getConnection();

            $sql = "SELECT id FROM products";
            $params = [];
            $types = "";

            if ($category && $category !== 'all') {
                $sql .= " WHERE category_name = ?";
                $params[] = $category;
                $types = "s";
            }

            $sql .= " ORDER BY created_at DESC";

            $stmt = $connection->prepare($sql);

            if (!$stmt) {
                throw new Exception("Failed to prepare products statement: " . $connection->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $products = [];
            while ($row = $result->fetch_assoc()) {
                $product = self::findById($row['id']);
                if ($product) {
                    $products[] = $product;
                }
            }

            $stmt->close();
            return $products;
        } catch (Exception $e) {
            throw new Exception("Error finding all products: " . $e->getMessage());
        }
    }


    public static function findByCategory(string $category): array
    {
        return self::findAll($category);
    }




    protected function processAttributes(array $rawAttributes): array
    {

        return $rawAttributes;
    }

    /**
     * Convert products array to array representation
     */
    public static function toArrayCollection(array $products): array
    {
        return array_map(function ($product) {
            return $product->toArray();
        }, $products);
    }
}
