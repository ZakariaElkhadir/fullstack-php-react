<?php

namespace App\GraphQL\Resolvers;

use App\Models\SwatchAttribute;
/*
 *this resolver handles attributes separately from products
 *this meets the requirement that attributes should be resolved through their own classes
 */
class AttributeResolver
{
    public static function getAttributeById($rootValue, array $args): ?object
    {
        $id = $args["id"];
        $allAttributes = self::getAllAttributes(null, []);

        foreach ($allAttributes as $attribute) {
            if ($attribute->getId() === $id) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * Get multiple attributes by their IDs
     * This is called when resolving product attributes
     */
    public static function getAttributesByIds($rootValue, array $args): array
    {
        $ids = $args["ids"] ?? [];
        $allAttributes = self::getAllAttributes(null, []);

        $result = [];
        foreach ($allAttributes as $attribute) {
            if (in_array($attribute->getId(), $ids)) {
                $result[] = $attribute;
            }
        }

        return $result;
    }

    /**
     * Get all available attributes
     */
    public static function getAllAttributes($rootValue, array $args): array
    {
        $allAttributes = [];

        try {
            $allAttributes = array_merge(
                $allAttributes,
                SwatchAttribute::findAll(),
            );
        } catch (\Exception $e) {
            error_log("Error loading swatch attributes: " . $e->getMessage());
        }

        return $allAttributes;
    }

    /**
     * Get attributes by type
     */
    public static function getAttributesByType($rootValue, array $args): array
    {
        $type = strtolower($args["type"]);

        return match ($type) {
            "swatch" => SwatchAttribute::findAll(),
            // 'text' => TextAttribute::findAll(),
            default => [],
        };
    }

    /**
     * SPECIAL: Resolve attributes for a product
     * This is called from product resolvers to get full attribute data
     */
    public static function resolveProductAttributes($product): array
    {
        $productAttributes = $product->getAttributes();
        $attributeIds = array_map(fn($attr) => $attr["id"], $productAttributes);

        $fullAttributes = [];
        $allAttributes = self::getAllAttributes(null, []);

        foreach ($allAttributes as $attribute) {
            if (in_array($attribute->getId(), $attributeIds)) {
                $fullAttributes[] = $attribute;
            }
        }

        return $fullAttributes;
    }
}
