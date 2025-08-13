<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class TechCategory extends AbstractCategory
{
    protected function processMetadata(array $rawMetadata): array
    {
        $processed = $rawMetadata;

        $processed["hasVariants"] = false;
        $processed["requiresCompatibility"] = true;
        $processed["warrantyRequired"] = true;

        return $processed;
    }

    public function getDisplayName(): string
    {
        return "💻 " . $this->getName();
    }

    public static function findAll(): array
    {
        $db = new Database();
        $connection = $db->getConnection();

        $sampleData = [
            [
                "id" => "tech",
                "name" => "tech",
                "description" => "Technology items",
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
