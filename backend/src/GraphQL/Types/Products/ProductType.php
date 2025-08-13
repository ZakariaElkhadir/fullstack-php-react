<?php

namespace App\GraphQL\Types\Products;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\Interfaces\ProductInterface;
/**
 * Generic product type for products that don't fit specific categories
 */
class ProductType
{
    private static ?ObjectType $type = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                "name" => "GenericProduct",
                "description" =>
                    'Generic product type for products that don\'t fit specific categories',
                "interfaces" => [ProductInterface::getType()],
                "fields" => [
                    "id" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn($product) => $product->getId(),
                    ],
                    "name" => [
                        "type" => Type::nonNull(Type::string()),
                        "resolve" => fn($product) => $product->getName(),
                    ],
                    "prices" => [
                        "type" => Type::nonNull(
                            Type::listOf(Type::nonNull(self::getPriceType())),
                        ),
                        "resolve" => fn($product) => $product->getPrices(),
                    ],
                    "gallery" => [
                        "type" => Type::nonNull(
                            Type::listOf(Type::nonNull(Type::string())),
                        ),
                        "resolve" => fn($product) => $product->getGallery(),
                    ],
                    "category" => [
                        "type" => Type::string(),
                        "resolve" => fn(
                            $product,
                        ) => $product->getCategoryName(),
                    ],
                    "inStock" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "resolve" => fn($product) => $product->isInStock(),
                    ],
                    "brand" => [
                        "type" => Type::string(),
                        "resolve" => fn($product) => $product->getBrand(),
                    ],
                    "description" => [
                        "type" => Type::string(),
                        "resolve" => fn($product) => $product->getDescription(),
                    ],
                    "attributes" => [
                        "type" => Type::nonNull(
                            Type::listOf(Type::nonNull(Type::string())),
                        ),
                        "resolve" => function ($product) {
                            $attributes = $product->getAttributes();
                            return array_map(
                                fn($attr) => $attr["id"],
                                $attributes,
                            );
                        },
                    ],
                ],
            ]);
        }

        return self::$type;
    }

    private static function getPriceType()
    {
        static $priceType = null;

        if ($priceType === null) {
            $priceType = new ObjectType([
                "name" => "GenericPrice",
                "fields" => [
                    "amount" => ["type" => Type::nonNull(Type::float())],
                    "code" => ["type" => Type::nonNull(Type::string())],
                    "label" => ["type" => Type::string()],
                    "symbol" => ["type" => Type::string()],
                ],
            ]);
        }

        return $priceType;
    }
}
