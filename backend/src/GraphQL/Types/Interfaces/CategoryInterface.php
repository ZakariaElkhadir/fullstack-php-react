<?php

namespace App\GraphQL\Types\Interfaces;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CategoryInterface
{
    private static ?InterfaceType $interface = null;

    public static function getType(): InterfaceType
    {
        if (self::$interface === null) {
            self::$interface = new InterfaceType([
                "name" => "Category",
                "description" => "Common interface for all category types",
                "fields" => [
                    "id" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Unique identifier for the category",
                    ],
                    "name" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Category name",
                    ],
                    "displayName" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" =>
                            "Formatted display name for the category",
                    ],
                    "description" => [
                        "type" => Type::string(),
                        "description" => "Category description",
                    ],
                    "isActive" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" => "Whether the category is active",
                    ],
                    "metadata" => [
                        "type" => Type::listOf(Type::string()),
                        "description" => "Category-specific metadata",
                    ],
                ],
                "resolveType" => function ($value) {
                    if ($value instanceof \App\Models\ClothesCategory) {
                        return \App\GraphQL\Types\Categories\ClothesCategoryType::getType();
                    }
                    if ($value instanceof \App\Models\TechCategory) {
                        return \App\GraphQL\Types\Categories\TechCategoryType::getType();
                    }

                    throw new \Exception("Unknown category type");
                },
            ]);
        }

        return self::$interface;
    }
}
