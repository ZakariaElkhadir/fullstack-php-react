<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class TechProduct extends AbstractProduct
{
    protected function processAttributes(array $rawAttributes): array
    {
        $processed = [];
        foreach ($rawAttributes as $attribute) {
            if (
                isset($attribute["id"], $attribute["name"], $attribute["type"])
            ) {
                $processedAttribute = [
                    "id" => $attribute["id"],
                    "name" => $attribute["name"],
                    "type" => $attribute["type"],
                    "items" => $attribute["items"] ?? [],
                ];

                // Tech-specific attribute processing
                if ($attribute["name"] === "Capacity") {
                    // For storage capacity, we might want to sort numerically
                    $processedAttribute["displayOrder"] = [
                        "256GB",
                        "512GB",
                        "1TB",
                        "2TB",
                    ];
                }

                if ($attribute["type"] === "swatch") {
                    $processedAttribute["isColorSwatch"] = true;
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
        $category = "tech";

        $stmt = $connection->prepare(
            "SELECT * FROM products WHERE category_name = ?",
        );
        $stmt->execute([$category]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $products = [];
        foreach ($results as $row) {
            $products[] = static::createFromArray($row, $connection);
        }

        $db->close();
        return $products;
    }
}
