<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class ClothesCategory extends AbstractCategory
{
    protected function processMetadata(array $rawMetadata): array
    {
        $processed = $rawMetadata;
        $processed["hasVariants"] = true;
        $processed["requiresSize"] = true;

        return $processed;
    }
    public function getDisplayName(): string
    {
        return $this->getName();
    }
    public static function findAll(): array
    {
        $db = new Database();
        $connection = $db->getConnection();

        $sampleData = [
            [
                "id" => "clothes",
                "name" => "clothes",
                "description" => "Clothing items",
                "is_active" => true,
            ],
        ];

        $categories = [];
        foreach ($sampleData as $row) {
            $categories[] = static::createFromArray($row, $connection);
        }

        $db->close();
        return $categories;
    }
}
