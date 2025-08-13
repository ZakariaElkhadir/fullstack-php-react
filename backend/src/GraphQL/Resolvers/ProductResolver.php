<?php

namespace App\GraphQL\Resolvers;

use App\Models\ClothesProduct;
use App\Models\TechProduct;
use App\Models\Products;
/**
 * ProductResolver class for GraphQL product queries
 */
class ProductResolver
{
    /**
     * Get all products with optional category filtering
     */
    public static function getAllProducts($rootValue, array $args): array
    {
        $category = $args["category"] ?? null;

        if ($category) {
            return match (strtolower($category)) {
                "clothes" => ClothesProduct::findAll(),
                "tech" => TechProduct::findAll(),
                "all" => self::getAllProductsFromAllTypes(),
                default => [],
            };
        }

        return self::getAllProductsFromAllTypes();
    }

    /**
     * Get a specific product by ID
     */
    public static function getProductById($rootValue, array $args): ?object
    {
        $id = $args["id"];

        $allProducts = self::getAllProductsFromAllTypes();

        foreach ($allProducts as $product) {
            if ($product->getId() === $id) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Get products by category (for category pages)
     */
    public static function getProductsByCategory($rootValue, array $args): array
    {
        $categoryName = $args["categoryName"];

        return match (strtolower($categoryName)) {
            "clothes" => ClothesProduct::findAll(),
            "tech" => TechProduct::findAll(),
            default => [],
        };
    }

    /**
     * Search products by name or description
     */
    public static function searchProducts($rootValue, array $args): array
    {
        $searchTerm = strtolower($args["query"]);
        $allProducts = self::getAllProductsFromAllTypes();

        return array_filter($allProducts, function ($product) use (
            $searchTerm,
        ) {
            $name = strtolower($product->getName() ?? "");
            $description = strtolower($product->getDescription() ?? "");

            return strpos($name, $searchTerm) !== false ||
                strpos($description, $searchTerm) !== false;
        });
    }

    /**
     * Get featured/recommended products
     */
    public static function getFeaturedProducts($rootValue, array $args): array
    {
        $limit = $args["limit"] ?? 6;
        $allProducts = self::getAllProductsFromAllTypes();

        $inStockProducts = array_filter(
            $allProducts,
            fn($product) => $product->isInStock(),
        );

        return array_slice($inStockProducts, 0, $limit);
    }

    /**
     * Helper method to get products from all types
     */
    private static function getAllProductsFromAllTypes(): array
    {
        $allProducts = [];

        try {
            $allProducts = array_merge($allProducts, ClothesProduct::findAll());
        } catch (\Exception $e) {
            error_log("Error loading clothes products: " . $e->getMessage());
        }

        try {
            $allProducts = array_merge($allProducts, TechProduct::findAll());
        } catch (\Exception $e) {
            error_log("Error loading tech products: " . $e->getMessage());
        }

        return $allProducts;
    }
}
