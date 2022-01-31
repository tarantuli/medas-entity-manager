<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Hydration\Hydrator;
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

    public function initialize(string $className, array $id): object
    {
        $metaData = $this->metaDataManager->get($className);
        $entity = new $className();
        $this->hydrator->setIdValues($metaData, $entity, $id);

        return $entity;
    }

    public function initializeAndHydrate(string $className, array $id): object
    {
        $entity = $this->initialize($className, $id);
        $metaData = $this->metaDataManager->get($className);

        if ($metaData->entity->store) {
            $this->hydrator->hydrate($metaData, $entity);
        }

        return $entity;
    }
}
