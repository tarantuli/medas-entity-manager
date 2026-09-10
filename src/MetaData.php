<?php

declare(strict_types=1);

namespace Medas\EntityManager;

class MetaData
{
    public Attributes\Entity $entity;

    /** @var MetaData\Property[] */
    public array $properties;

    /** @var MetaData\BackReference[] */
    public array $backReferences;

    /** @var MetaData\ReadableField[] */
    public array $readableFields;

    /** @var MetaData\WritableField[] */
    public array $writableFields;

    public array $uniquePropertySets;
    public array $compoundIndexes;
    public MetaData\Property|null $idProperty;
    public MetaData\Inheritance $inheritance;

    /** @var class-string<Interfaces\OwnershipFilter>[] */
    public array $ownershipFilters;

    public int $sourceFileDate;
    public bool $softDeletes;

    /**
     * User-land attributes on the entity class that the metadata compiler does not
     * itself interpret - anything it does not own (see Compiler\AttributeCollector).
     * Held as constructed instances so consumers read their values directly.
     *
     * @var object[]
     */
    public array $attributes = [];

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

    // The first class-level attribute that is an instance of $class, or null. Reads
    // from the additional-attributes bag - owned attributes have their own accessors.
    public function attribute(string $class): object|null
    {
        return array_find($this->attributes ?? [], fn($attribute) => $attribute instanceof $class);
    }
}
