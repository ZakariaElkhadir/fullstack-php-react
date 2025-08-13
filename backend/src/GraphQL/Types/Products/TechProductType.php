<?php

namespace App\GraphQL\Types\Products;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\Interfaces\ProductInterface;

/**
 * TechProductType represents a technology product with specific attributes and features.
 */
class TechProductType
{
    private static ?ObjectType $type = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                "name" => "TechProduct",
                "description" =>
                    "A technology product with specific attributes and features",
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

                    "specifications" => [
                        "type" => Type::listOf(Type::string()),
                        "description" =>
                            "Technical specifications for the product",
                        "resolve" => function ($product) {
                            // Extract specifications from processed attributes
                            $attributes = $product->getAttributes();
                            $specs = [];
                            foreach ($attributes as $attr) {
                                if (
                                    in_array($attr["name"], [
                                        "Capacity",
                                        "Memory",
                                        "Storage",
                                        "Processor",
                                    ])
                                ) {
                                    $specs[] =
                                        $attr["name"] .
                                        ": " .
                                        implode(
                                            ", ",
                                            array_map(
                                                fn($item) => $item["value"],
                                                $attr["items"] ?? [],
                                            ),
                                        );
                                }
                            }
                            return $specs;
                        },
                    ],
                    "hasVariants" => [
                        "type" => Type::nonNull(Type::boolean()),
                        "description" =>
                            "Whether this tech product has variants (capacity, color)",
                        "resolve" => function ($product) {
                            $attributes = $product->getAttributes();
                            foreach ($attributes as $attr) {
                                if (
                                    in_array($attr["name"], [
                                        "Capacity",
                                        "Color",
                                    ]) &&
                                    count($attr["items"] ?? []) > 1
                                ) {
                                    return true;
                                }
                            }
                            return false;
                        },
                    ],
                    "availableCapacities" => [
                        "type" => Type::listOf(Type::string()),
                        "description" =>
                            "Available storage capacities for this tech product",
                        "resolve" => function ($product) {
                            $attributes = $product->getAttributes();
                            foreach ($attributes as $attr) {
                                if ($attr["name"] === "Capacity") {
                                    if (isset($attr["displayOrder"])) {
                                        return $attr["displayOrder"];
                                    }
                                    return array_map(
                                        fn($item) => $item["value"],
                                        $attr["items"] ?? [],
                                    );
                                }
                            }
                            return [];
                        },
                    ],
                    "warrantyInfo" => [
                        "type" => Type::string(),
                        "description" => "Warranty information for the product",
                        "resolve" => function ($product) {
                            return "Standard manufacturer warranty applies";
                        },
                    ],
                    "compatibilityNotes" => [
                        "type" => Type::listOf(Type::string()),
                        "description" => "Compatibility notes and requirements",
                        "resolve" => function ($product) {
                            $notes = [];
                            $category = $product->getCategoryName();

                            if (
                                strpos(
                                    strtolower($product->getName()),
                                    "laptop",
                                ) !== false
                            ) {
                                $notes[] =
                                    "Compatible with Windows, macOS, and Linux";
                            }
                            if (
                                strpos(
                                    strtolower($product->getName()),
                                    "headphone",
                                ) !== false
                            ) {
                                $notes[] = "Bluetooth 5.0 compatible";
                                $notes[] =
                                    "Works with all devices with audio jack";
                            }
                            if (
                                strpos(
                                    strtolower($product->getName()),
                                    "phone",
                                ) !== false
                            ) {
                                $notes[] = "Supports 5G networks";
                                $notes[] = "Compatible with wireless charging";
                            }

                            return $notes;
                        },
                    ],
                ],
            ]);
        }

        return self::$type;
    }

    /**
     * Reuse Price type structure
     */
    private static function getPriceType()
    {
        static $priceType = null;

        if ($priceType === null) {
            $priceType = new ObjectType([
                "name" => "TechPrice",
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
