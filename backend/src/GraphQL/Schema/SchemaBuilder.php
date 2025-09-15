<?php

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

use App\GraphQL\Types\Interfaces\ProductInterface;
use App\GraphQL\Types\Interfaces\CategoryInterface;
use App\GraphQL\Types\Interfaces\AttributeInterface;
use App\GraphQL\Types\AttributeType;
use App\GraphQL\Types\Products\ClothesProductType;
use App\GraphQL\Types\Products\TechProductType;
use App\GraphQL\Types\Products\ProductType;
use App\GraphQL\Types\Categories\ClothesCategoryType;
use App\GraphQL\Types\Categories\TechCategoryType;
use App\GraphQL\Types\Attributes\SwatchAttributeType;
use App\GraphQL\Types\OrderType;

use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\Resolvers\CategoryResolver;
use App\GraphQL\Resolvers\AttributeResolver;
use App\GraphQL\Resolvers\OrderResolver;

/**
 * Schema builder class
 */
class SchemaBuilder
{
    public static function build(): Schema
    {
       
        $queryType = new ObjectType([
            "name" => "Query",
            "description" => "Root query type",
            "fields" => [
              
                "products" => [
                    "type" => Type::listOf(ProductInterface::getType()),
                    "description" =>
                        "Get all products with optional category filter",
                    "args" => [
                        "category" => [
                            "type" => Type::string(),
                            "description" =>
                                "Filter by category (clothes, tech, all)",
                        ],
                    ],
                    "resolve" => [ProductResolver::class, "getAllProducts"],
                ],
                "product" => [
                    "type" => ProductInterface::getType(),
                    "description" => "Get a specific product by ID",
                    "args" => [
                        "id" => ["type" => Type::nonNull(Type::string())],
                    ],
                    "resolve" => [ProductResolver::class, "getProductById"],
                ],
                "searchProducts" => [
                    "type" => Type::listOf(ProductInterface::getType()),
                    "description" => "Search products by name or description",
                    "args" => [
                        "query" => ["type" => Type::nonNull(Type::string())],
                    ],
                    "resolve" => [ProductResolver::class, "searchProducts"],
                ],
                "featuredProducts" => [
                    "type" => Type::listOf(ProductInterface::getType()),
                    "description" => "Get featured/recommended products",
                    "args" => [
                        "limit" => ["type" => Type::int(), "defaultValue" => 6],
                    ],
                    "resolve" => [
                        ProductResolver::class,
                        "getFeaturedProducts",
                    ],
                ],

                // CATEGORY QUERIES
                "categories" => [
                    "type" => Type::listOf(CategoryInterface::getType()),
                    "description" => "Get all available categories",
                    "resolve" => [CategoryResolver::class, "getAllCategories"],
                ],
                "category" => [
                    "type" => CategoryInterface::getType(),
                    "description" => "Get a specific category by ID",
                    "args" => [
                        "id" => ["type" => Type::nonNull(Type::string())],
                    ],
                    "resolve" => [CategoryResolver::class, "getCategoryById"],
                ],

                // ATTRIBUTE QUERIES (Separate resolution!)
                "attributes" => [
                    "type" => Type::listOf(AttributeInterface::getType()),
                    "description" => "Get all available attributes",
                    "resolve" => [AttributeResolver::class, "getAllAttributes"],
                ],
                "attribute" => [
                    "type" => AttributeInterface::getType(),
                    "description" => "Get a specific attribute by ID",
                    "args" => [
                        "id" => ["type" => Type::nonNull(Type::string())],
                    ],
                    "resolve" => [AttributeResolver::class, "getAttributeById"],
                ],
                "attributesByIds" => [
                    "type" => Type::listOf(AttributeInterface::getType()),
                    "description" => "Get multiple attributes by their IDs",
                    "args" => [
                        "ids" => [
                            "type" => Type::listOf(
                                Type::nonNull(Type::string()),
                            ),
                        ],
                    ],
                    "resolve" => [
                        AttributeResolver::class,
                        "getAttributesByIds",
                    ],
                ],

                // ORDER QUERIES
                "order" => [
                    "type" => OrderType::getType(),
                    "description" => "Get order by ID",
                    "args" => [
                        "id" => ["type" => Type::nonNull(Type::string())],
                    ],
                    "resolve" => [OrderResolver::class, "getOrderById"],
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            "name" => "Mutation",
            "description" => "Root mutation type",
            "fields" => [
                "createOrder" => [
                    "type" => self::getOrderResultType(),
                    "description" => "Create a new order",
                    "args" => [
                        "orderInput" => [
                            "type" => Type::nonNull(self::getOrderInputType()),
                            "description" => "Order data",
                        ],
                    ],
                    "resolve" => [OrderResolver::class, "createOrder"],
                ],
            ],
        ]);

        return new Schema(
            new SchemaConfig()
                ->setQuery($queryType)
                ->setMutation($mutationType)
                ->setTypes([
                  
                    ProductInterface::getType(),
                    CategoryInterface::getType(),
                    AttributeInterface::getType(),
                    AttributeType::getType(),
                    ClothesProductType::getType(),
                    TechProductType::getType(),
                    ProductType::getType(),
                    ClothesCategoryType::getType(),
                    TechCategoryType::getType(),
                    SwatchAttributeType::getType(),
                    OrderType::getType(),
                ]),
        );
    }

    private static function getOrderInputType()
    {
        static $inputType = null;

        if ($inputType === null) {
            $inputType = new \GraphQL\Type\Definition\InputObjectType([
                "name" => "OrderInput",
                "fields" => [
                    "customerEmail" => [
                        "type" => Type::nonNull(Type::string()),
                    ],
                    "items" => [
                        "type" => Type::listOf(self::getOrderItemInputType()),
                    ],
                    "totalAmount" => ["type" => Type::nonNull(Type::float())],
                    "currency" => ["type" => Type::string()],
                ],
            ]);
        }

        return $inputType;
    }

    private static function getOrderItemInputType()
    {
        static $inputType = null;

        if ($inputType === null) {
            $inputType = new \GraphQL\Type\Definition\InputObjectType([
                "name" => "OrderItemInput",
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

        return $inputType;
    }

    private static function getOrderResultType()
    {
        static $resultType = null;

        if ($resultType === null) {
            $resultType = new ObjectType([
                "name" => "OrderResult",
                "fields" => [
                    "success" => ["type" => Type::nonNull(Type::boolean())],
                    "orderId" => ["type" => Type::string()],
                    "message" => ["type" => Type::string()],
                ],
            ]);
        }

        return $resultType;
    }
}
