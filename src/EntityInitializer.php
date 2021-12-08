<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Hydration\Hydrator;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityInitializer
{
    public function __construct(
        private Hydrator $hydrator,
    )
    {
    }

    public function initializeEntity(string $className, MetaData $metaData, mixed $id): object
    {
        $entity = new $className();
        $this->hydrator->setIdValues($metaData, $entity, $id);

        if ($metaData->entity->table) {
            $this->hydrator->hydrate($metaData, $entity);
        }

        return $entity;
    }
}
