<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Hydration\Hydrator;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;

#[Service]
class Initializer
{
    public function __construct(
        private readonly Hydrator        $hydrator,
        private readonly MetaDataManager $metaDataManager
    )
    {
    }

    public function initialize(string $className, array $values, MetaData $metaData = null): object
    {
        $entity = new $className();

        if ($values) {
                $metaData ?? $metaData = $this->metaDataManager->get($className);
            $this->hydrator->setValues($metaData, $entity, $values);
        }

        return $entity;
    }

    public function initializeAndHydrate(string $className, mixed $id): object
    {
        $metaData = $this->metaDataManager->get($className);
        $entity = $this->initialize($className, [$metaData->idProperty->name => $id], $metaData);

        $this->hydrator->hydrate($metaData, $entity);

        return $entity;
    }
}
