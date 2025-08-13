<?php

namespace App\GraphQL\Types\Categories;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\Interfaces\CategoryInterface;

class ClothesCategoryType
{
    private static ?ObjectType $type = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                "name" => "ClothesCategory",
                "description" => "Category for clothing products",
                "interfaces" => [CategoryInterface::getType()],
                "fields" => [
                    // Interface fields
                    "id" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn($category) => $category->getId(),
                    ],
                    "name" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn($category) => $category->getName(),
                    ],
                    "displayName" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn(
                            $category,
                        ) => $category->getDisplayName(),
                    ],
                    "description" => [
                        "type" => Type::string(),
                        "resolve" => fn(
                            $category,
                        ) => $category->getDescription(),
                    ],
                    "isActive" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "resolve" => fn($category) => $category->isActive(),
                    ],
                    "metadata" => [
                        "type" => Type::listOf(Type::string()),
                        "resolve" => function ($category) {
                            $metadata = $category->getMetadata();
                            return array_map(
                                fn($key, $value) => "$key: $value",
                                array_keys($metadata),
                                $metadata,
                            );
                        },
                    ],

                    "hasVariants" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether clothes in this category have variants",
                        "resolve" => function ($category) {
                            $metadata = $category->getMetadata();
                            return $metadata["hasVariants"] ?? true;
                        },
                    ],
                    "requiresSize" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether clothes in this category require size selection",
                        "resolve" => function ($category) {
                            $metadata = $category->getMetadata();
                            return $metadata["requiresSize"] ?? true;
                        },
                    ],
                    "sizeChart" => [
                        "type" => Type::string(),
                        "description" =>
                            "Size chart information for this clothing category",
                        "resolve" => function ($category) {
                            return "Standard clothing size chart applies";
                        },
                    ],
                ],
            ]);
        }

        return self::$type;
    }
}
