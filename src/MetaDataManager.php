<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Attributes\Id;
use Medas\EntityManager\Attributes\IsNullable;
use Medas\EntityManager\Attributes\IsUnique;
use Medas\EntityManager\Exceptions\ClassIsNotAnEntityException;
use Medas\EntityManager\Exceptions\EntityHasNoIdPropertyException;
use Medas\EntityManager\Hydration\PropertyTypeNormalizer;
use Medas\EntityManager\MetaData\Property;
use Medas\ServiceManager\Attributes\Service;
use Symfony\Contracts\Cache\CacheInterface;

#[Service]
class MetaDataManager
{
    public function __construct(
        private CacheInterface         $cache,
        private PropertyTypeNormalizer $propertyTypeNormalizer
    )
    {
    }

    public function get(string $className): MetaData
    {
        $class = new \ReflectionClass($className);
        $key = sha1($className);

        /** @var MetaData $metaData */
        $metaData = $this->cache->get($key, function () use ($className, $class) {
            return $this->create($className, $class);
        });

        if (env('env') === 'dev' && $metaData->sourceFileDate !== filemtime($class->getFileName())) {
            $this->cache->delete($key);
            $metaData = $this->get($className);
        }

        return $metaData;
    }

    private function create(string $className, \ReflectionClass $class): MetaData
    {
        if (!$class->getAttributes(Entity::class)) {
            throw new ClassIsNotAnEntityException($className);
        }

        $metaData = new MetaData($className);
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
