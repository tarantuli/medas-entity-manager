<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData\Compiler;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{
    Attributes,
    Exceptions\MultipleReferencePropertiesFound,
    Exceptions\NoReferencePropertyFound,
    Hydration\PropertyTypeNormalizer,
    MetaData,
    MetaData\Property,
    MetaData\Reference,
    Properties\PropertyManager,
    Types\TypeFinder
};

#[Service]
readonly class PropertyProcessor
{
    public function __construct(
        private PropertyManager        $propertyManager,
        private PropertyTypeNormalizer $propertyTypeNormalizer,
        private TypeFinder             $typeFinder,
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
        if (attribute(Attributes\Unmanaged::class, $property)) {
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
            onDelete: $onDelete ? $onDelete->action : Attributes\Relations\Action::NoAction,
            onUpdate: $onUpdate ? $onUpdate->action : Attributes\Relations\Action::NoAction,
            phpTypes: $this->propertyTypeNormalizer->names($property),
            reflection: $property,
            handler: $handler ? $handler::class : null,
        );
    }

    private function processReferences(\ReflectionProperty $property, MetaData $metaData): void
    {
        $references = attribute(Attributes\References::class, $property);

        if (!$references) {
            return;
        }

        if ($references->property === null) {
            $targetClass = new \ReflectionClass($references->entity);
            $targetProperties = [];

            foreach ($targetClass->getProperties() as $targetProperty) {
                if (in_array($metaData->className, $this->propertyTypeNormalizer->names($targetProperty))) {
                    $targetProperties[] = $targetProperty;
                }
            }

            if (count($targetProperties) === 0) {
                throw new NoReferencePropertyFound(
                    $property->name,
                    $references->entity,
                    $metaData->className
                );
            }

            if (count($targetProperties) >= 2) {
                throw new MultipleReferencePropertiesFound(
                    $property->name,
                    $references->entity,
                    $metaData->className,
                    array_map(fn($p) => $p->name, $targetProperties)
                );
            }

            $references->property = $targetProperties[0]->name;
        }

        $metaData->references[] = new Reference(
            name: $property->name,
            entity: $references->entity,
            property: $references->property,
        );
    }
}
