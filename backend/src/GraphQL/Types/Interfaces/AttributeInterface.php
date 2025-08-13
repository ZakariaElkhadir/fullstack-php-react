<?php

namespace App\GraphQL\Types\Interfaces;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
/**
 * Interface for all attribute types
 */
class AttributeInterface
{
    private static ?InterfaceType $interface = null;

    public static function getType(): InterfaceType
    {
        if (self::$interface === null) {
            self::$interface = new InterfaceType([
                "name" => "Attribute",
                "description" => "Common interface for all attribute types",
                "fields" => [
                    "id" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Unique identifier for the attribute",
                    ],
                    "name" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Attribute name (Size, Color, etc.)",
                    ],
                    "type" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Attribute type (swatch, text, etc.)",
                    ],
                    "displayFormat" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" =>
                            "How this attribute should be displayed",
                    ],
                    "isRequired" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether this attribute is required for selection",
                    ],
                    "items" => [
                        "type" => Type::nonNull(
                            Type::listOf(
                                Type::nonNull(self::getAttributeItemType()),
                            ),
                        ),
                        "description" => "Available attribute items/options",
                    ],
                ],
                "resolveType" => function ($value) {
                    if ($value instanceof \App\Models\SwatchAttribute) {
                        return \App\GraphQL\Types\Attributes\SwatchAttributeType::getType();
                    }

                    throw new \Exception("Unknown attribute type");
                },
            ]);
        }

        return self::$interface;
    }

    private static function getAttributeItemType()
    {
        static $itemType = null;

        if ($itemType === null) {
            $itemType = new \GraphQL\Type\Definition\ObjectType([
                "name" => "AttributeItem",
                "description" => "Individual attribute option",
                "fields" => [
                    "id" => ["type" => Type::nonNull(Type::string())],
                    "displayValue" => ["type" => Type::nonNull(Type::string())],
                    "value" => ["type" => Type::nonNull(Type::string())],
                    "sortOrder" => ["type" => Type::int()],
                ],
            ]);
        }

        return $itemType;
    }
}
