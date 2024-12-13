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

    public function property(string $propertyName, bool $ignoreUnknownProperties = false): MetaData\Property|null
    {
        foreach ($this->properties as $property) {
            if ($property->name === $propertyName) {
                return $property;
            }
        }

        if (!$ignoreUnknownProperties) {
            throw new Exceptions\PropertyDoesNotExist($this->className, $propertyName);
        }

        return null;
    }
}
