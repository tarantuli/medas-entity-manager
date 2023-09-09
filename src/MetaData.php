<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Exceptions\PropertyDoesNotExist;

class MetaData
{
    public Entity $entity;

    /** @var MetaData\Property[] */
    public array $properties;

    /** @var MetaData\Reference[] */
    public array $references;

    public MetaData\Property|null $idProperty;

    public string|null $parent;

    public bool $storeOriginalEntityType;

    public string $storeRequestingParentClass;

    public int $sourceFileDate;

    public function __construct(
        public string $className,
    )
    {
    }

    public function property(string $propertyName): MetaData\Property
    {
        foreach ($this->properties as $property) {
            if ($property->name === $propertyName) {
                return $property;
            }
        }

        throw new PropertyDoesNotExist($this->className, $propertyName);
    }
}
