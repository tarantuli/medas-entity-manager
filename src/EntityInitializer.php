<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Hydration\Hydrator;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityInitializer
{
    public function __construct(
        private Hydrator        $hydrator,
        private MetaDataManager $metaDataManager
    )
    {
    }

    public function initializeEntity(string $className, mixed $id): object
    {
        $metaData = $this->metaDataManager->get($className);
        $entity = new $className();
        $this->hydrator->setIdValues($metaData, $entity, $id);

        if ($metaData->entity->table) {
            $this->hydrator->hydrate($metaData, $entity);
        }

        return $entity;
    }
}
