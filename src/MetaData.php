<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Exceptions\PropertyDoesNotExistException;
use Medas\EntityManager\Storage\Databases\Pdo\Table;

class MetaData
{
    public Entity $entity;

    /** @var MetaData\Property[] */
    public array $properties;

    public ?MetaData\Property $idProperty;
    /** @var MetaData\Property[] */
    public array $idProperties;
    public bool $hasCompositeId;

    public int $sourceFileDate;

    public function __construct(public string $className, public \ReflectionClass $reflection)
    {
    }

    public function getProperty(string $propertyName): MetaData\Property
    {
        foreach ($this->properties as $property) {
            if ($property->name === $propertyName) {
                return $property;
            }
        }

        throw new PropertyDoesNotExistException($this->className, $propertyName);
    }

    public function getTable(): Table
    {
        return db($this->entity->db)->getTable($this->entity->table);
    }
}
