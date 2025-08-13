<?php

namespace App\Models;

use PDO;

/**
 * AbstractAttribute class - defines the structure and common behavior for all attribute types
 */
abstract class AbstractAttribute
{
    protected ?string $id = null;
    protected ?string $name = null;
    protected ?string $type = null;
    protected bool $isRequired = false;
    protected array $items = [];

    public function __construct(
        string $id,
        string $name,
        string $type,
        bool $isRequired = false,
        array $items = [],
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setType($type);
        $this->setIsRequired($isRequired);
        $this->setItems($items);
    }

    abstract protected function processItems(array $rawItems): array;
    abstract protected function validateValue($value): bool;
    abstract public static function findAll(): array;
    abstract public function getDisplayFormat(): string;

    protected static function createFromArray(
        array $data,
        PDO $connection,
    ): static {
        return new static(
            $data["id"],
            $data["name"],
            $data["type"],
            (bool) ($data["is_required"] ?? false),
            [],
        );
    }

    // Setters
    protected function setId(string $id): void
    {
        $this->id = $id;
    }
    protected function setName(string $name): void
    {
        $this->name = $name;
    }
    protected function setType(string $type): void
    {
        $this->type = $type;
    }
    protected function setIsRequired(bool $isRequired): void
    {
        $this->isRequired = $isRequired;
    }
    protected function setItems(array $items): void
    {
        $this->items = $this->processItems($items);
    }

    // Getters
    public function getId(): ?string
    {
        return $this->id;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function isRequired(): bool
    {
        return $this->isRequired;
    }
    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "type" => $this->getType(),
            "displayFormat" => $this->getDisplayFormat(),
            "isRequired" => $this->isRequired(),
            "items" => $this->getItems(),
        ];
    }
}
