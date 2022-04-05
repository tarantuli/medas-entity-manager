<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Hydration\Hydrator;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Initializer
{
    public function __construct(
        private Hydrator        $hydrator,
        private MetaDataManager $metaDataManager
    )
    {
    }

    public function initialize(string $className, array $values, MetaData $metaData = null): object
    {
        $metaData ?? $metaData = $this->metaDataManager->get($className);
        $entity = new $className();
        $this->hydrator->setValues($metaData, $entity, $values);

        return $entity;
    }

    public function initializeAndHydrate(string $className, array $values): object
    {
        $metaData = $this->metaDataManager->get($className);
        $entity = $this->initialize($className, $values, $metaData);

        $this->hydrator->hydrate($metaData, $entity);

        return $entity;
    }
}
