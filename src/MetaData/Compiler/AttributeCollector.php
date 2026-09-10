<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData\Compiler;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Attributes;

// Collects the attributes on a class, property or method that the metadata compiler
// does not itself interpret. The owned attributes below already have typed homes on
// the MetaData/Property; everything else is handed back as constructed instances, so
// user-land features (template variables, and the like) can read their own attributes
// straight off the cached metadata without reflecting again at runtime.
//
// "Owned" is matched by instance, mirroring the attribute() helper: a subclass of an
// owned attribute (e.g. #[Id] extending #[IsReadable]) counts as owned too. Leaking an
// owned attribute into the bag would be harmless - consumers filter by their own type -
// but the list is kept complete so the partition stays meaningful.
#[Service]
readonly class AttributeCollector
{
    /** @var class-string[] */
    private const array OWNED = [
        Attributes\Entity::class,
        Attributes\Entity\StoreConfigOption::class,
        Attributes\Entity\StorageConfigOption::class,
        Attributes\StoreOriginalEntityType::class,
        Attributes\UniquePropertySet::class,
        Attributes\CompoundIndex::class,
        Attributes\AddOwnershipFilter::class,
        Attributes\Id::class,
        Attributes\IsGeneratedValue::class,
        Attributes\IsCreationTimestamp::class,
        Attributes\IsModificationTimestamp::class,
        Attributes\IsUnique::class,
        Attributes\IsIndex::class,
        Attributes\IsReadable::class,
        Attributes\IsWritable::class,
        Attributes\Unmanaged::class,
        Attributes\ReferencedBy::class,
        Attributes\Relations\OnDelete::class,
        Attributes\Relations\OnUpdate::class,
    ];

    /** @return object[] */
    public function collect(\ReflectionClass|\ReflectionProperty|\ReflectionMethod $target): array
    {
        $attributes = [];

        foreach ($target->getAttributes() as $attribute) {
            if ($this->isOwned($attribute->getName())) {
                continue;
            }

            $attributes[] = $attribute->newInstance();
        }

        return $attributes;
    }

    private function isOwned(string $class): bool
    {
        return array_any(self::OWNED, fn($owned) => is_a($class, $owned, true));
    }
}
