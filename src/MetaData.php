<?php

declare(strict_types=1);

namespace Medas\EntityManager;

class MetaData
{
    public Attributes\Entity $entity;

    /** @var MetaData\Property[] */
    public array $properties;

    /** @var MetaData\Reference[] */
    public array $references;
    public MetaData\Property|null $idProperty;
    public MetaData\Inheritance $inheritance;
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

        throw new Exceptions\PropertyDoesNotExist($this->className, $propertyName);
    }
}
