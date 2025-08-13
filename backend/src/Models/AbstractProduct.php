<?php

namespace App\Models;

use App\Config\Database;
use Exception;

/**
 * Fixed version of the simple AbstractProduct to meet ScandiWeb requirements
 */
abstract class AbstractProduct
{
    protected string $id;
    protected string $name;
    protected array $prices;        // Changed from single price
    protected array $gallery;       // Changed from images
    protected string $categoryName;
    protected bool $inStock;        // Changed from int to bool
    protected string $brand;        // Added required field
    protected ?string $description; // Added required field
    protected array $attributes;    // Added required field

    public function __construct(
        string $id,
        string $name,
        array $prices,          // Now array of price objects
        array $gallery,
        string $categoryName,
        bool $inStock,          // Now boolean
        string $brand,          // Added
        ?string $description,   // Added
        array $attributes = []  // Added
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->prices = $prices;
        $this->gallery = $gallery;
        $this->categoryName = $categoryName;
        $this->inStock = $inStock;
        $this->brand = $brand;
        $this->description = $description;
        $this->attributes = $attributes;
    }

    // REQUIRED: Abstract method for polymorphic attribute processing
    abstract protected function processAttributes(array $rawAttributes): array;

    // REQUIRED: Abstract method for type-specific behavior
    abstract public static function findById(string $id): ?self;

    // REQUIRED: Factory method for polymorphism
    abstract public static function createFromType(string $type): ?self;

    /**
     * Load product from database (required for task)
     */
    public static function loadFromDatabase(string $productId): ?array
    {
        try {
            $database = new Database();
            $connection = $database->getConnection();

            // Load basic data
            $stmt = $connection->prepare("
                SELECT p.*, pt.type_name 
                FROM products p 
                JOIN product_types pt ON p.type_id = pt.id 
                WHERE p.id = ?
            ");
            $stmt->bind_param("s", $productId);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$row = $result->fetch_assoc()) {
                return null;
            }

            // Load prices with currencies
            $priceStmt = $connection->prepare("
                SELECT pp.amount, c.label, c.symbol 
                FROM product_prices pp 
                JOIN currencies c ON pp.currency_id = c.id 
                WHERE pp.product_id = ?
            ");
            $priceStmt->bind_param("s", $productId);
            $priceStmt->execute();
            $priceResult = $priceStmt->get_result();

            $prices = [];
            while ($priceRow = $priceResult->fetch_assoc()) {
                $prices[] = [
                    'amount' => (float)$priceRow['amount'],
                    'currency' => [
                        'label' => $priceRow['label'],
                        'symbol' => $priceRow['symbol']
                    ]
                ];
            }

            // Load gallery
            $galleryStmt = $connection->prepare("
                SELECT image_url 
                FROM product_gallery 
                WHERE product_id = ? 
                ORDER BY sort_order
            ");
            $galleryStmt->bind_param("s", $productId);
            $galleryStmt->execute();
            $galleryResult = $galleryStmt->get_result();

            $gallery = [];
            while ($galleryRow = $galleryResult->fetch_assoc()) {
                $gallery[] = $galleryRow['image_url'];
            }

            // Load attributes
            $attrStmt = $connection->prepare("
                SELECT pa.name, pa.type, pai.display_value, pai.value
                FROM product_attributes pa
                JOIN product_attribute_items pai ON pa.id = pai.attribute_id
                WHERE pa.product_id = ?
            ");
            $attrStmt->bind_param("s", $productId);
            $attrStmt->execute();
            $attrResult = $attrStmt->get_result();

            $rawAttributes = [];
            while ($attrRow = $attrResult->fetch_assoc()) {
                $rawAttributes[$attrRow['name']]['name'] = $attrRow['name'];
                $rawAttributes[$attrRow['name']]['type'] = $attrRow['type'];
                $rawAttributes[$attrRow['name']]['items'][] = [
                    'displayValue' => $attrRow['display_value'],
                    'value' => $attrRow['value']
                ];
            }

            return [
                'id' => $row['id'],
                'name' => $row['name'],
                'prices' => $prices,
                'gallery' => $gallery,
                'category_name' => $row['category_name'],
                'in_stock' => (bool)$row['in_stock'],
                'brand' => $row['brand'],
                'description' => $row['description'],
                'type' => $row['type_name'],
                'raw_attributes' => array_values($rawAttributes)
            ];

        } catch (Exception $e) {
            throw new Exception("Error loading product: " . $e->getMessage());
        }
    }

    // Getters (required for GraphQL)
    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrices(): array { return $this->prices; }
    public function getGallery(): array { return $this->gallery; }
    public function getCategoryName(): string { return $this->categoryName; }
    public function isInStock(): bool { return $this->inStock; }
    public function getBrand(): string { return $this->brand; }
    public function getDescription(): ?string { return $this->description; }
    public function getAttributes(): array { return $this->attributes; }

    /**
     * Convert to array for GraphQL
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'prices' => $this->getPrices(),
            'gallery' => $this->getGallery(),
            'category' => $this->getCategoryName(),
            'inStock' => $this->isInStock(),
            'brand' => $this->getBrand(),
            'description' => $this->getDescription(),
            'attributes' => $this->getAttributes()
        ];
    }

    // Keep your original method but make it more useful
    public function getDetails(): array
    {
        return $this->toArray();
    }
}
