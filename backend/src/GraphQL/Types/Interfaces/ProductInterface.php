<?php

namespace App\GraphQL\Types\Interfaces;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class ProductInterface
{
  private static ?InterfaceType $interface = null;

  public static function getType(): InterfaceType
  {
    if (self::$interface === null) {
      self::$interface = new InterfaceType([
        'name' => 'Product',
        'description' => 'Common interface for all product types',
        'fields' => [
          'id' => [
            'type' => Type::nonNull(Type::string()),
            'description' => 'Unique identifier for the product'
          ],
          'name' => [
            'type' => Type::nonNull(Type::string()),
            'description' => 'Product name'
          ],
          'prices' => [
            'type' => Type::nonNull(Type::listOf(Type::nonNull(self::getPriceType()))),
            'description' => 'Product prices in different currencies'
          ],
          'gallery' => [
            'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
            'description' => 'Product image URLs'
          ],
          'category' => [
            'type' => Type::string(),
            'description' => 'Product category name'
          ],
          'inStock' => [
            'type' => Type::nonNull(Type::boolean()),
            'description' => 'Whether the product is in stock'
          ],
          'brand' => [
            'type' => Type::string(),
            'description' => 'Product brand'
          ],
          'description' => [
            'type' => Type::string(),
            'description' => 'Product description'
          ],
          'attributes' => [
            'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
            'description' => 'List of attribute IDs (resolved separately by AttributeResolver)'
          ]
        ],
        'resolveType' => function ($value) {

          if ($value instanceof \App\Models\ClothesProduct) {
            return \App\GraphQL\Types\Products\ClothesProductType::getType();
          }
          if ($value instanceof \App\Models\TechProduct) {
            return \App\GraphQL\Types\Products\TechProductType::getType();
          }

          return \App\GraphQL\Types\Products\ProductType::getType();
        }
      ]);
    }

    return self::$interface;
  }

  /**
   * Helper method to create the Price type used in the interface
   */
  private static function getPriceType()
  {
    static $priceType = null;

    if ($priceType === null) {
      $priceType = new \GraphQL\Type\Definition\ObjectType([
        'name' => 'Price',
        'description' => 'Price information with currency',
        'fields' => [
          'amount' => [
            'type' => Type::nonNull(Type::float()),
            'description' => 'Price amount'
          ],
          'code' => [
            'type' => Type::nonNull(Type::string()),
            'description' => 'Currency code (USD, EUR, etc.)'
          ],
          'label' => [
            'type' => Type::string(),
            'description' => 'Currency label'
          ],
          'symbol' => [
            'type' => Type::string(),
            'description' => 'Currency symbol ($, €, etc.)'
          ]
        ]
      ]);
    }

    return $priceType;
  }
}
