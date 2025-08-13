<?php
namespace App\Models;
use PDO;
abstract class AbstractCategory
{
    protected ?string $id = null;
    protected ?string $name = null;
    protected ?string $description = null;
    protected bool $isActive = true;
    protected array $metadata = [];

    /**
     * @param string $id
     * @param string $name
     * @param string|null $description
     * @param bool $isActive
     * @param array $metadata
     */
    public function __construct(
        string $id,
        string $name,
        ?string $description = null,
        bool $isActive = true,
        array $metadata = [],
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
        $this->setIsActive($isActive);
        $this->setMetadata($metadata);
    }

    /**
     * @param array $metadata
     * @return array
     */
    abstract protected function processMetadata(array $metadata): array;

    /**
     * @return array
     */
    abstract public static function findAll(): array;
    abstract public function getDisplayName(): string;

    /**
     * @param array $data
     * @param PDO $connection
     * @return static
     */
    protected static function createFromArray(
        array $data,
        PDO $connection,
    ): static {
        return new static(
            $data["id"],
            $data["name"],
            $data["description"] ?? null,
            (bool) ($data["is_active"] ?? true),
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
    protected function setDescription(?string $description): void
    {
        $this->description = $description;
    }
    protected function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @param array $metadata
     * @return void
     */
    protected function setMetadata(array $metadata): void
    {
        $this->metadata = $this->processMetadata($metadata);
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
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function isActive(): bool
    {
        return $this->isActive;
    }
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "displayName" => $this->getDisplayName(),
            "description" => $this->getDescription(),
            "isActive" => $this->isActive(),
            "metadata" => $this->getMetadata(),
        ];
    }
}
