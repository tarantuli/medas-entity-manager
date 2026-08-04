<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData\Compiler;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\Core\Collections\ReferenceCollection;
use Medas\EntityManager\{
    Attributes,
    ConfigOptions\DefaultOnDeleteAction,
    ConfigOptions\DefaultOnUpdateAction,
    Exceptions\MultipleReferencePropertiesFound,
    Exceptions\NoReferencePropertyFound,
    Exceptions\ReferencedByPropertyMustBeReferenceCollection,
    Hydration\PropertyTypeNormalizer,
    MetaData,
    MetaData\BackReference,
    MetaData\Property,
    Properties\PropertyManager,
    Types\TypeFinder
};

#[Service]
readonly class PropertyProcessor
{
    public function __construct(
        private PropertyManager             $propertyManager,
        private PropertyTypeNormalizer      $propertyTypeNormalizer,
        private TypeFinder                  $typeFinder,

        #[ConfigValue(DefaultOnDeleteAction::class)]
        private Attributes\Relations\Action $defaultOnDeleteAction = Attributes\Relations\Action::Restrict,

        #[ConfigValue(DefaultOnUpdateAction::class)]
        private Attributes\Relations\Action $defaultOnUpdateAction = Attributes\Relations\Action::Cascade,
    )
    {
    }

    public function processProperties(\ReflectionClass $class, MetaData $metaData): void
    {
        $properties = $this->getSortedProperties($class);

        foreach ($properties as $property) {
            $this->processProperty($property, $metaData);
            $this->processReferences($property, $metaData);
        }
    }

    private function getSortedProperties(\ReflectionClass $class): array
    {
        $parents = $this->gatherParents($class);
        $properties = $class->getProperties();

        usort(
            $properties,
            function (\ReflectionProperty $a, \ReflectionProperty $b) use ($parents) {
                // Parents deeper in the chain have lower values, sorting them in front
                return $parents[$a->class] - $parents[$b->class];
            });

        return $properties;
    }

    private function gatherParents(\ReflectionClass $parent): array
    {
        $counter = 0;
        $parents = [$parent->name => $counter];

        while (false !== $parent = $parent->getParentClass()) {
            $parents[$parent->name] = --$counter;
        }

        return $parents;
    }

    private function processProperty(\ReflectionProperty $property, MetaData $metaData): void
    {
        // #[ReferencedBy] properties are the inverse side of a relation. They are not stored, so they
        // must stay out of $metaData->properties entirely - otherwise every consumer that iterates it
        // (storage fetch/persist, snapshots, schema generation) would mistake them for owned columns or
        // collections. They are handled exclusively via processReferences() / $metaData->backReferences.
        if (attribute(Attributes\Unmanaged::class, $property)
            || attribute(Attributes\ReferencedBy::class, $property)) {
            return;
        }

        $type = $this->typeFinder->find($property);
        $isNullable = $property->getType()->allowsNull();
        $onDelete = attribute(Attributes\Relations\OnDelete::class, $property);
        $onUpdate = attribute(Attributes\Relations\OnUpdate::class, $property);
        $handler = $this->propertyManager->getHandler($property);

        $metaData->properties[] = new Property(
            name: $property->name,
            type: $type,
            hasDefault: $property->hasDefaultValue(),
            default: $property->hasDefaultValue() ? $property->getDefaultValue() : null,
            isId: !empty($property->getAttributes(Attributes\Id::class, \ReflectionAttribute::IS_INSTANCEOF)),
            isGeneratedValue: !empty($property->getAttributes(Attributes\IsGeneratedValue::class)),
            isCreationTimestamp: !empty($property->getAttributes(Attributes\IsCreationTimestamp::class)),
            isModificationTimestamp: !empty($property->getAttributes(Attributes\IsModificationTimestamp::class)),
            isNullable: $isNullable,
            isUnique: !empty($property->getAttributes(Attributes\IsUnique::class)),
            isIndex: !empty($property->getAttributes(Attributes\IsIndex::class)),
            onDelete: $onDelete ? $onDelete->action : $this->defaultOnDeleteAction,
            onUpdate: $onUpdate ? $onUpdate->action : $this->defaultOnUpdateAction,
            phpTypes: $this->propertyTypeNormalizer->names($property),
            reflection: $property,
            handler: $handler ? $handler::class : null,
        );
    }

    private function processReferences(\ReflectionProperty $property, MetaData $metaData): void
    {
        $referencedBy = attribute(Attributes\ReferencedBy::class, $property);

        if (!$referencedBy) {
            return;
        }

        $collectionClass = $this->referenceCollectionClass($property);

        if ($referencedBy->property === null) {
            $targetClass = new \ReflectionClass($referencedBy->entity);
            $targetProperties = [];

            foreach ($targetClass->getProperties() as $targetProperty) {
                if (in_array($metaData->className, $this->propertyTypeNormalizer->names($targetProperty))) {
                    $targetProperties[] = $targetProperty;
                }
            }

            if (count($targetProperties) === 0) {
                throw new NoReferencePropertyFound(
                    $property->name,
                    $referencedBy->entity,
                    $metaData->className
                );
            }

            if (count($targetProperties) >= 2) {
                throw new MultipleReferencePropertiesFound(
                    $property->name,
                    $referencedBy->entity,
                    $metaData->className,
                    array_map(fn($p) => $p->name, $targetProperties)
                );
            }

            $referencedBy->property = $targetProperties[0]->name;
        }

        $metaData->backReferences[] = new BackReference(
            name: $property->name,
            entity: $referencedBy->entity,
            property: $referencedBy->property,
            collectionClass: $collectionClass,
        );
    }

    /**
     * A #[ReferencedBy] property is hydrated by handing the collection a lazy loader closure (see
     * Hydrator), which only works for a ReferenceCollection. Enforce that here - at compile time -
     * and return the concrete collection class so the Hydrator can instantiate it without the property
     * having to live in $metaData->properties.
     */
    private function referenceCollectionClass(\ReflectionProperty $property): string
    {
        $typeNames = $this->propertyTypeNormalizer->names($property);

        if (count($typeNames) !== 1 || !is_a($typeNames[0], ReferenceCollection::class, true)) {
            throw new ReferencedByPropertyMustBeReferenceCollection(
                $property,
                $typeNames === [] ? 'none' : implode('|', $typeNames)
            );
        }

        /** @noinspection PhpStrictTypeCheckingInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $typeNames[0];
    }
}
