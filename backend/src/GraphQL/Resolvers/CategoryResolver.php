<?php

namespace App\GraphQL\Resolvers;

use App\Models\ClothesCategory;
use App\Models\TechCategory;

class CategoryResolver
{
    /**
     * Get all available categories
     */
    public static function getAllCategories($rootValue, array $args): array
    {
        $allCategories = [];

        try {
            $allCategories = array_merge(
                $allCategories,
                ClothesCategory::findAll(),
            );
        } catch (\Exception $e) {
            error_log("Error loading clothes categories: " . $e->getMessage());
        }

        try {
            $allCategories = array_merge(
                $allCategories,
                TechCategory::findAll(),
            );
        } catch (\Exception $e) {
            error_log("Error loading tech categories: " . $e->getMessage());
        }

        return $allCategories;
    }

    /**
     * Get a specific category by ID
     */
    public static function getCategoryById($rootValue, array $args): ?object
    {
        $id = $args["id"];
        $allCategories = self::getAllCategories(null, []);

        foreach ($allCategories as $category) {
            if ($category->getId() === $id) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Get category by name/type
     */
    public static function getCategoryByName($rootValue, array $args): ?object
    {
        $name = strtolower($args["name"]);

        return match ($name) {
            "clothes" => ClothesCategory::findAll()[0] ?? null,
            "tech" => TechCategory::findAll()[0] ?? null,
            default => null,
        };
    }

    /**
     * Get active categories only
     */
    public static function getActiveCategories($rootValue, array $args): array
    {
        $allCategories = self::getAllCategories(null, []);

        return array_filter(
            $allCategories,
            fn($category) => $category->isActive(),
        );
    }
}
