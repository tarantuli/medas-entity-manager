<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{Exceptions\IdPropertyNotGiven, MetaData, MetaDataManager};

#[Service]
readonly class IdValue
{
    public function __construct(
        private MetaDataManager $metaDataManager,
    )
    {
    }

    public function get(array|\ArrayAccess $values, MetaData $metaData): mixed
    {
        $name = $metaData->idProperty->name;

        if (!isset($values[$name])) {
            throw new IdPropertyNotGiven($metaData->className, $name);
        }

        return $values[$name];
    }

    public function asArray(array|\ArrayAccess $values, MetaData $metaData): array
    {
        return [$metaData->idProperty->name => $this->get($values, $metaData)];
    }

    public function fromEntity(object $entity): mixed
    {
        $metaData = $this->metaDataManager->get($entity::class);

        return $metaData->idProperty->reflection->getValue($entity);
    }
}
