<?php
namespace App\Models;
use App\Config\Database;
use Exception;
use PDO;

class Products extends AbstractProduct
{
    /**
     * Process attributes specific to products (generic processing)
     */
    protected function processAttributes(array $rawAttributes): array
    {
        $processed = [];

        foreach ($rawAttributes as $attribute) {
            if (
                isset($attribute["id"], $attribute["name"], $attribute["type"])
            ) {
                $processed[] = [
                    "id" => $attribute["id"],
                    "name" => $attribute["name"],
                    "type" => $attribute["type"],
                    "items" => $attribute["items"] ?? [],
                ];
            }
        }

        return $processed;
    }

    /**
     * Find all products from database (no category filtering)
     */
    public static function findAll(): array
    {
        try {
            $db = new Database();
            $connection = $db->getConnection();

            $sql = "SELECT * FROM products";
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $products = [];
            foreach ($results as $row) {
                $products[] = static::createFromArray($row, $connection);
            }

            $db->close();
            return $products;
        } catch (\Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
}
