<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Exceptions\PropertyDoesNotExistException;

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

    public function __construct(public string $className)
    {
    }

    public function property(string $propertyName): MetaData\Property
    {
        foreach ($this->properties as $property) {
            if ($property->name === $propertyName) {
                return $property;
            }
        }

        throw new PropertyDoesNotExistException($this->className, $propertyName);
    }
}
