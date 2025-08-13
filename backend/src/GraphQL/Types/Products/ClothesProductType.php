<?php

namespace App\GraphQL\Types\Products;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\Interfaces\ProductInterface;
/**
 * Represents a clothing product with specific attributes and features.
 */
class ClothesProductType
{
    private static ?ObjectType $type = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                "name" => "ClothesProduct",
                "description" =>
                    "A clothing product with specific attributes and features",
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

                    "sizeGuide" => [
                        "type" => Type::string(),
                        "description" => "Size guide information for clothing",
                        "resolve" => function ($product) {
                            $attributes = $product->getAttributes();
                            foreach ($attributes as $attr) {
                                if (
                                    $attr["name"] === "Size" &&
                                    isset($attr["displayOrder"])
                                ) {
                                    return implode(", ", $attr["displayOrder"]);
                                }
                            }
                            return null;
                        },
                    ],
                    "hasVariants" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether this clothing item has size/color variants",
                        "resolve" => function ($product) {
                            // Clothes always have variants (size, color)
                            return true;
                        },
                    ],
                    "availableSizes" => [
                        "type" => Type::listOf(Type::string()),
                        "description" =>
                            "Available sizes for this clothing item",
                        "resolve" => function ($product) {
                            $attributes = $product->getAttributes();
                            foreach ($attributes as $attr) {
                                if ($attr["name"] === "Size") {
                                    return array_map(
                                        fn($item) => $item["value"],
                                        $attr["items"] ?? [],
                                    );
                                }
                            }
                            return [];
                        },
                    ],
                ],
            ]);
        }

        return self::$type;
    }

    /**
     * Reuse the Price type from ProductInterface
     */
    private static function getPriceType()
    {
        static $priceType = null;

        if ($priceType === null) {
            $priceType = new ObjectType([
                "name" => "ClothesPrice",
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
