<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Attributes\Service;
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

        if ($values) {
            $this->valueSetter->setValues($metaData, $entity, $values, resetHistory: true);
        }

        return $entity;
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
