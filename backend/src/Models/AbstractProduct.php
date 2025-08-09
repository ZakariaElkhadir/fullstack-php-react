<?php

namespace App\Models;

use App\Config\Database;
use mysqli;
use Exception;

abstract class AbstractProduct
{
    protected string $id;
    protected string $name;
    protected ?string $description;
    protected string $brand;
    protected string $categoryName;
    protected bool $inStock;
    protected array $gallery;
    protected array $prices;
    protected array $attributes;
    protected Database $database;

    public function __construct()
    {
        $this->database = new Database();
        $this->gallery = [];
        $this->prices = [];
        $this->attributes = [];
    }

    abstract protected function processAttributes(array $rawAttributes): array;

    /*s==================Getters====================*/

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function isInStock(): bool
    {
        return $this->inStock;
    }

    public function getGallery(): array
    {
        return $this->gallery;
    }

    public function getPrices(): array
    {
        return $this->prices;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /*s==================Setters====================*/
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function setCategoryName(string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    public function setInStock(bool $inStock): void
    {
        $this->inStock = $inStock;
    }

    public function setGallery(array $gallery): void
    {
        $this->gallery = $gallery;
    }

    public function setPrices(array $prices): void
    {
        $this->prices = $prices;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Load basic product data from database
     */
    protected function loadBasicData(string $productId): bool
    {
        try {
            $connection = $this->database->getConnection();

            $stmt = $connection->prepare("
                SELECT id, name, description, brand, category_name, in_stock 
                FROM products 
                WHERE id = ?
            ");

            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $connection->error);
            }

            $stmt->bind_param("s", $productId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $this->setId($row['id']);
                $this->setName($row['name']);
                $this->setDescription($row['description']);
                $this->setBrand($row['brand']);
                $this->setCategoryName($row['category_name']);
                $this->setInStock((bool)$row['in_stock']);

                $stmt->close();
                return true;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            throw new Exception("Error loading basic product data: " . $e->getMessage());
        }
    }

    /**
     * Load product gallery from database
     */
    protected function loadGallery(): void
    {
        try {
            $connection = $this->database->getConnection();

            $stmt = $connection->prepare("
                SELECT image_url 
                FROM product_galleries 
                WHERE product_id = ? 
                ORDER BY sort_order ASC
            ");

            if (!$stmt) {
                throw new Exception("Failed to prepare gallery statement: " . $connection->error);
            }

            $stmt->bind_param("s", $this->id);
            $stmt->execute();
            $result = $stmt->get_result();

            $gallery = [];
            while ($row = $result->fetch_assoc()) {
                $gallery[] = $row['image_url'];
            }

            $this->setGallery($gallery);
            $stmt->close();
        } catch (Exception $e) {
            throw new Exception("Error loading product gallery: " . $e->getMessage());
        }
    }

    /**
     * Load product prices with currency information
     */
    protected function loadPrices(): void
    {
        try {
            $connection = $this->database->getConnection();

            $stmt = $connection->prepare("
                SELECT pp.amount, c.code, c.label, c.symbol
                FROM product_prices pp
                JOIN currencies c ON pp.currency_code = c.code
                WHERE pp.product_id = ?
                ORDER BY pp.sort_order ASC
            ");

            if (!$stmt) {
                throw new Exception("Failed to prepare prices statement: " . $connection->error);
            }

            $stmt->bind_param("s", $this->id);
            $stmt->execute();
            $result = $stmt->get_result();

            $prices = [];
            while ($row = $result->fetch_assoc()) {
                $prices[] = [
                    'amount' => (float)$row['amount'],
                    'currency' => [
                        'label' => $row['label'],
                        'symbol' => $row['symbol'],
                        '__typename' => 'Currency'
                    ],
                    '__typename' => 'Price'
                ];
            }

            $this->setPrices($prices);
            $stmt->close();
        } catch (Exception $e) {
            throw new Exception("Error loading product prices: " . $e->getMessage());
        }
    }

    /**
     * Load product attributes with their items
     */
    protected function loadAttributes(): void
    {
        try {
            $connection = $this->database->getConnection();

            $stmt = $connection->prepare("
                SELECT DISTINCT 
                    ats.id as set_id,
                    ats.name as set_name,
                    ats.type as set_type
                FROM product_attributes pa
                JOIN attribute_sets ats ON pa.attribute_set_id = ats.id
                WHERE pa.product_id = ?
            ");

            if (!$stmt) {
                throw new Exception("Failed to prepare attributes statement: " . $connection->error);
            }

            $stmt->bind_param("s", $this->id);
            $stmt->execute();
            $result = $stmt->get_result();

            $attributes = [];
            while ($row = $result->fetch_assoc()) {
                $attributeItems = $this->loadAttributeItems($row['set_id']);

                $attributes[] = [
                    'id' => $row['set_id'],
                    'name' => $row['set_name'],
                    'type' => $row['set_type'],
                    'items' => $attributeItems,
                    '__typename' => 'AttributeSet'
                ];
            }

            $this->setAttributes($this->processAttributes($attributes));
            $stmt->close();
        } catch (Exception $e) {
            throw new Exception("Error loading product attributes: " . $e->getMessage());
        }
    }

    /**
     * Load items for a specific attribute set
     */
    private function loadAttributeItems(string $attributeSetId): array
    {
        try {
            $connection = $this->database->getConnection();

            $stmt = $connection->prepare("
                SELECT id, display_value, value
                FROM attribute_items
                WHERE attribute_set_id = ?
            ");

            if (!$stmt) {
                throw new Exception("Failed to prepare attribute items statement: " . $connection->error);
            }

            $stmt->bind_param("s", $attributeSetId);
            $stmt->execute();
            $result = $stmt->get_result();

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = [
                    'id' => $row['id'],
                    'displayValue' => $row['display_value'],
                    'value' => $row['value'],
                    '__typename' => 'Attribute'
                ];
            }

            $stmt->close();
            return $items;
        } catch (Exception $e) {
            throw new Exception("Error loading attribute items: " . $e->getMessage());
        }
    }

    /**
     * Convert object to array representation
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'brand' => $this->getBrand(),
            'category' => $this->getCategoryName(),
            'inStock' => $this->isInStock(),
            'gallery' => $this->getGallery(),
            'prices' => $this->getPrices(),
            'attributes' => $this->getAttributes(),
            '__typename' => 'Product'
        ];
    }
}
