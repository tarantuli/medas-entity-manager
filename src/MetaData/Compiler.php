<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\EntityManager\{Attributes,
    Attributes\Entity,
    Attributes\Id,
    Attributes\IsNullable,
    Attributes\IsUnique,
    Exceptions\ClassIsNotAnEntityException,
    Exceptions\EntityHasNoIdPropertyException,
    Hydration\PropertyTypeNormalizer,
    MetaData
};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Compiler
{
    public function __construct(private PropertyTypeNormalizer $propertyTypeNormalizer)
    {
    }

    public function compile(string $className, \ReflectionClass $class): MetaData
    {
        if (!$attributes = $class->getAttributes(Entity::class)) {
            throw new ClassIsNotAnEntityException($className);
        }

        $metaData = new MetaData($className, $class);
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $metaData->entity = $attributes[0]->newInstance();
        $metaData->sourceFileDate = filemtime($class->getFileName());

        $this->determineProperties($class, $metaData);
        $this->determineIdProperties($metaData);

        return $metaData;
    }

    private function determineProperties(\ReflectionClass $class, MetaData $metaData)
    {
        foreach ($class->getProperties() as $property) {
            if ($attributes = $property->getAttributes(Attributes\Interfaces\Type::class, \ReflectionAttribute::IS_INSTANCEOF)) {
                /** @var Attributes\Interfaces\Type $type */
                $type = $attributes[0]->newInstance();

                $metaData->properties[] = new Property(
                    name: $property->name,
                    type: $type,
                    isId: !empty($property->getAttributes(Id::class)),
                    isNullable: !empty($property->getAttributes(IsNullable::class)),
                    isUnique: !empty($property->getAttributes(IsUnique::class)),
                    phpTypes: $this->propertyTypeNormalizer->getNames($property),
                    reflection: $property
                );
            }
        }
    }

    private function determineIdProperties(MetaData $metaData): void
    {
        $metaData->idProperties = [];
        $metaData->hasCompositeId = false;

        foreach ($metaData->properties as $property) {
            if ($property->isId) {
                $metaData->idProperties[] = $property;
                $metaData->idProperty = $property;
            }
        }

        if (count($metaData->idProperties) === 0) {
            throw new EntityHasNoIdPropertyException($metaData->className);
        }

        if (count($metaData->idProperties) > 1) {
            $metaData->idProperty = null;
            $metaData->hasCompositeId = true;
        }
    }
}
