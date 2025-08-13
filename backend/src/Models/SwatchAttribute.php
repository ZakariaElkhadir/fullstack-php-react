<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class SwatchAttribute extends AbstractAttribute
{
    protected function processItems(array $rawItems): array
    {
        $processed = [];
        foreach ($rawItems as $item) {
            $processedItem = [
                "id" => $item["id"] ?? uniqid(),
                "display_value" => $item["display_value"] ?? $item,
                "value" => $item["value"] ?? $item,
                "isColor" => true,
            ];

            $processed[] = $processedItem;
        }

        return $processed;
    }

    protected function validateValue($value): bool
    {
        foreach ($this->items as $item) {
            if ($item["value"] === $value) {
                return true;
            }
        }
        return false;
    }

    public function getDisplayFormat(): string
    {
        return "color_swatch";
    }

    public static function findAll(): array
    {
        $db = new Database();
        $connection = $db->getConnection();

        // Sample swatch attributes
        $sampleData = [
            [
                "id" => "swatch-1",
                "name" => "Color",
                "type" => "swatch",
                "is_required" => false,
            ],
            [
                "id" => "swatch-2",
                "name" => "Material",
                "type" => "swatch",
                "is_required" => true,
            ],
        ];

        $attributes = [];
        foreach ($sampleData as $row) {
            $attributes[] = static::createFromArray($row, $connection);
        }

        $db->close();
        return $attributes;
    }
}
