<?php

namespace App\GraphQL\Types\Categories;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\Interfaces\CategoryInterface;

class TechCategoryType
{
    private static ?ObjectType $type = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                "name" => "TechCategory",
                "description" => "Category for technology products",
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

                    // TECH-SPECIFIC FIELDS
                    "hasVariants" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether tech products in this category have variants",
                        "resolve" => function ($category) {
                            $metadata = $category->getMetadata();
                            return $metadata["hasVariants"] ?? false;
                        },
                    ],
                    "requiresCompatibility" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether products require compatibility checking",
                        "resolve" => function ($category) {
                            $metadata = $category->getMetadata();
                            return $metadata["requiresCompatibility"] ?? true;
                        },
                    ],
                    "warrantyRequired" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether warranty information is required",
                        "resolve" => function ($category) {
                            $metadata = $category->getMetadata();
                            return $metadata["warrantyRequired"] ?? true;
                        },
                    ],
                    "technicalSupport" => [
                        "type" => Type::string(),
                        "description" => "Technical support information",
                        "resolve" => function ($category) {
                            return "Technical support available 24/7 for all tech products";
                        },
                    ],
                ],
            ]);
        }

        return self::$type;
    }
}
