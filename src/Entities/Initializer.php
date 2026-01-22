<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\{Attributes\Service, Types\Collection};
use Medas\EntityManager\{Hydration\Hydrator, Hydration\ValueSetter, MetaData, MetaDataManager};

#[Service]
readonly class Initializer
{
    public function __construct(
        private Hydrator        $hydrator,
        private MetaDataManager $metaDataManager,
        private ValueSetter     $valueSetter,
    )
    {
    }

    public function initialize(string $className, array $values = [], MetaData|null $metaData = null): object
    {
        $metaData ??= $this->metaDataManager->get($className);
        $entity = new $className();

        $this->initializeCollections($entity, $metaData);

        if ($values) {
            $this->valueSetter->setValues($metaData, $entity, $values, resetHistory: true);
        }

        return $entity;
    }

    private function initializeCollections(mixed $entity, MetaData $metaData): void
    {
        foreach ($metaData->properties as $property) {
            if ($property->type instanceof Collection) {
                $property->reflection->setValue($entity, new $property->type->collectionType);
            }
        }
    }

    public function initializeAndHydrate(string $className, mixed $id): object
    {
        $metaData = $this->metaDataManager->get($className);

        if ($metaData->inheritance->storeOriginalClass && $className === $metaData->inheritance->sharedParentClass) {
            $className = $this->hydrator->fetchOriginalClass($metaData, $id);
        }

        $entity = $this->initialize($className, [$metaData->idProperty->name => $id], $metaData);

        $this->hydrator->hydrate($metaData, $entity);

        return $entity;
    }
}
