<?php

namespace App\Models;

use PDO;

/**
 * AbstractProduct class - defines the structure and common behavior for all product types
 */
abstract class AbstractProduct
{
    protected ?string $id = null;
    protected ?string $name = null;
    protected array $prices = [];
    protected array $gallery = [];
    protected ?string $categoryName = null;
    protected bool $inStock = false;
    protected ?string $brand = null;
    protected ?string $description = null;
    protected array $attributes = [];

    public function __construct(
        $id,
        $name,
        $prices,
        $gallery,
        ?string $categoryName,
        $inStock,
        $brand,
        $description,
        $attributes
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setPrices($prices);
        $this->setGallery($gallery);
        $this->setCategoryName($categoryName);
        $this->setInStock($inStock);
        $this->setBrand($brand);
        $this->setDescription($description);
        $this->setAttributes($attributes);
    }

    abstract protected function processAttributes(array $rawAttributes): array;
    abstract public static function findAll(): array;

    protected static function createFromArray(array $data, PDO $connection): static
    {
        $prices = static::loadPrices($data['id'], $connection);
        $gallery = static::loadGallery($data['id'], $connection);
        $attributes = static::loadAttributes($data['id'], $connection);

        return new static(
            $data['id'],
            $data['name'],
            $prices,
            $gallery,
            $data['category_name'] ?? '',
            (bool)($data['in_stock'] ?? false),
            $data['brand'] ?? '',
            $data['description'] ?? null,
            $attributes
        );
    }

    protected static function loadPrices(string $productId, PDO $connection): array
    {
        $sql = "SELECT pp.amount, c.code, c.label, c.symbol 
                FROM product_prices pp 
                JOIN currencies c ON pp.currency_code = c.code 
                WHERE pp.product_id = ? 
                ORDER BY pp.sort_order";

        $stmt = $connection->prepare($sql);
        $stmt->execute([$productId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function loadGallery(string $productId, PDO $connection): array
    {
        $sql = "SELECT image_url 
                FROM product_galleries 
                WHERE product_id = ? 
                ORDER BY sort_order";

        $stmt = $connection->prepare($sql);
        $stmt->execute([$productId]);

        $gallery = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $gallery[] = $row['image_url'];
        }

        return $gallery;
    }

    protected static function loadAttributes(string $productId, PDO $connection): array
    {
        $sql = "SELECT ats.id, ats.name, ats.type,
                       ati.id as item_id, ati.display_value, ati.value
                FROM product_attributes pa
                JOIN attribute_sets ats ON pa.attribute_set_id = ats.id
                LEFT JOIN attribute_items ati ON ats.id = ati.attribute_set_id
                WHERE pa.product_id = ?
                ORDER BY ats.name, ati.display_value";

        $stmt = $connection->prepare($sql);
        $stmt->execute([$productId]);

        $attributes = [];
        $currentSet = null;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($currentSet !== $row['id']) {
                $currentSet = $row['id'];
                $attributes[$currentSet] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'items' => []
                ];
            }

            if ($row['item_id']) {
                $attributes[$currentSet]['items'][] = [
                    'id' => $row['item_id'],
                    'display_value' => $row['display_value'],
                    'value' => $row['value']
                ];
            }
        }

        return array_values($attributes);
    }

    protected function setId(string $id): void { $this->id = $id; }
    protected function setName(string $name): void { $this->name = $name; }
    protected function setPrices(array $prices): void { $this->prices = $prices; }
    protected function setGallery(array $gallery): void { $this->gallery = $gallery; }
    protected function setCategoryName(?string $categoryName): void { $this->categoryName = $categoryName; }
    protected function setInStock(bool $inStock): void { $this->inStock = $inStock; }
    protected function setBrand(string $brand): void { $this->brand = $brand; }
    protected function setDescription(?string $description): void { $this->description = $description; }
    protected function setAttributes(array $attributes): void { $this->attributes = $attributes; }

    public function getId(): ?string { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function getPrices(): array { return $this->prices; }
    public function getGallery(): array { return $this->gallery; }
    public function getCategoryName(): ?string { return $this->categoryName; }
    public function isInStock(): bool { return $this->inStock; }
    public function getBrand(): ?string { return $this->brand; }
    public function getDescription(): ?string { return $this->description; }
    public function getAttributes(): array { return $this->attributes; }

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
}
