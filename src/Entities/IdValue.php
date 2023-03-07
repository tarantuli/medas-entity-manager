<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Exceptions\IdPropertyNotGiven;
use Medas\EntityManager\Hydration\ValueGetter;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class IdValue
{
    public function __construct(
        private readonly MetaDataManager $metaDataManager,
        private readonly ValueGetter     $valueGetter,
    )
    {
    }

    public function extract(array|\ArrayAccess $values, MetaData $metaData): mixed
    {
        $name = $metaData->idProperty->name;

        if (!isset($values[$name])) {
            throw new IdPropertyNotGiven($metaData->className, $name);
        }

        return $values[$name];
    }

    public function fromEntity(object $entity): array
    {
        $metaData = $this->metaDataManager->get($entity::class);

        return $metaData->idProperty->reflection->getValue($entity);
    }
}
