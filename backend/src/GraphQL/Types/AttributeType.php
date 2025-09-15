<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeType
{
    private static ?ObjectType $type = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'AttributeSet',
                'description' => 'Product attribute set (like Size, Color)',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Attribute set identifier'
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Attribute name (Size, Color, etc.)'
                    ],
                    'type' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Attribute type (text, swatch, etc.)'
                    ],
                    'items' => [
                        'type' => Type::nonNull(Type::listOf(Type::nonNull(self::getAttributeItemType()))),
                        'description' => 'Available attribute options'
                    ]
                ]
            ]);
        }

        return self::$type;
    }

    private static function getAttributeItemType(): ObjectType
    {
        static $itemType = null;

        if ($itemType === null) {
            $itemType = new ObjectType([
                'name' => 'AttributeItem',
                'description' => 'Individual attribute option',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Item identifier'
                    ],
                    'displayValue' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Display value (e.g., "S", "Red")',
                        'resolve' => function ($root) {
                            return $root['display_value'] ?? $root['displayValue'] ?? $root['value'];
                        }
                    ],
                    'value' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Actual value (e.g., "Small", "Red")'
                    ]
                ]
            ]);
        }

        return $itemType;
    }
}