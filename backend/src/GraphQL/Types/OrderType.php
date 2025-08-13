<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType
{
    private static ?ObjectType $type = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                "name" => "Order",
                "description" => "Customer order information",
                "fields" => [
                    "id" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Unique order identifier",
                    ],
                    "customerEmail" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Customer email address",
                    ],
                    "items" => [
                        "type" => Type::nonNull(
                            Type::listOf(
                                Type::nonNull(self::getOrderItemType()),
                            ),
                        ),
                        "description" => "Items in the order",
                    ],
                    "totalAmount" => [
                        "type" => Type::nonNull(Type::float()),
                        "description" => "Total order amount",
                    ],
                    "currency" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Order currency",
                    ],
                    "status" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Order status",
                    ],
                    "createdAt" => [
                        "type" => Type::nonNull(Type::string()),
                        "description" => "Order creation timestamp",
                    ],
                ],
            ]);
        }

        return self::$type;
    }

    private static function getOrderItemType()
    {
        static $itemType = null;

        if ($itemType === null) {
            $itemType = new ObjectType([
                "name" => "OrderItem",
                "fields" => [
                    "productId" => ["type" => Type::nonNull(Type::string())],
                    "quantity" => ["type" => Type::nonNull(Type::int())],
                    "selectedAttributes" => [
                        "type" => Type::listOf(Type::string()),
                    ],
                    "price" => ["type" => Type::nonNull(Type::float())],
                ],
            ]);
        }

        return $itemType;
    }
}
