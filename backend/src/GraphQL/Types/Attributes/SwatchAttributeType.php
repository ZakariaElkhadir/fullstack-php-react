<?php

namespace App\GraphQL\Types\Attributes;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\Interfaces\AttributeInterface;

class SwatchAttributeType
{
    private static ?ObjectType $type = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                "name" => "SwatchAttribute",
                "description" =>
                    "Color swatch attribute with visual representation",
                "interfaces" => [AttributeInterface::getType()],
                "fields" => [
                    // Interface fields
                    "id" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn($attribute) => $attribute->getId(),
                    ],
                    "name" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn($attribute) => $attribute->getName(),
                    ],
                    "type" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn($attribute) => $attribute->getType(),
                    ],
                    "displayFormat" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn(
                            $attribute,
                        ) => $attribute->getDisplayFormat(),
                    ],
                    "isRequired" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "resolve" => fn($attribute) => $attribute->isRequired(),
                    ],
                    "items" => [
                        "type" => Type::nonNull(
                            Type::listOf(
                                Type::nonNull(self::getSwatchItemType()),
                            ),
                        ),
                        "resolve" => fn($attribute) => $attribute->getItems(),
                    ],

                    // SWATCH-SPECIFIC FIELDS
                    "isColorAttribute" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether this is specifically a color attribute",
                        "resolve" => function ($attribute) {
                            return strtolower($attribute->getName()) ===
                                "color";
                        },
                    ],
                    "swatchStyle" => [
                        "type" => Type::string(),
                        "description" => "Visual style for displaying swatches",
                        "resolve" => function ($attribute) {
                            return "circular"; // or 'square', 'rectangular'
                        },
                    ],
                ],
            ]);
        }

        return self::$type;
    }

    private static function getSwatchItemType()
    {
        static $itemType = null;

        if ($itemType === null) {
            $itemType = new ObjectType([
                "name" => "SwatchItem",
                "description" => "Swatch attribute item with color information",
                "fields" => [
                    "id" => ["type" => Type::nonNull(Type::string())],
                    "displayValue" => ["type" => Type::nonNull(Type::string())],
                    "value" => ["type" => Type::nonNull(Type::string())],
                    "isColor" => ["type" => Type::boolean()],
                    "hexCode" => ["type" => Type::string()],
                    "sortOrder" => ["type" => Type::int()],
                ],
            ]);
        }

        return $itemType;
    }
}
