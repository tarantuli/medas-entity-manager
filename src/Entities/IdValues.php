<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Exceptions\IdPropertyNotGiven;
use Medas\EntityManager\Exceptions\IdValueShouldBeAnArray;
use Medas\EntityManager\Exceptions\NonIdPropertyGiven;
use Medas\EntityManager\Hydration\ValueGetter;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class IdValues
{
    public function __construct(
        private readonly MetaDataManager $metaDataManager,
        private readonly ValueGetter     $valueGetter,
    )
    {
    }

    public function normalize(string $className, mixed $id): array
    {
        $metaData = $this->metaDataManager->get($className);

        if (!is_iterable($id)) {
            if ($metaData->hasCompositeId) {
                throw new IdValueShouldBeAnArray($className, get_debug_type($id));
            }

            return [$metaData->idProperty->name => $id];
        }

        $idValues = $this->extract($id, $metaData);

        if ($superfluousValues = array_diff_key($id, $idValues)) {
            throw new NonIdPropertyGiven(
                $className,
                implode(', ', array_keys($superfluousValues))
            );
        }

        return $idValues;
    }

    public function extract(array|\ArrayAccess $values, MetaData $metaData): array
    {
        $idProperties = $metaData->idProperties;
        $idValues = [];

        foreach ($idProperties as $idProperty) {
            if (!isset($values[$idProperty->name])) {
                throw new IdPropertyNotGiven($metaData->className, $idProperty->name);
            }

            $idValues[$idProperty->name] = $values[$idProperty->name];
        }

        return $idValues;
    }

    public function fromEntity(object $entity): array
    {
        $metaData = $this->metaDataManager->get($entity::class);

        return $this->valueGetter->get($entity, $metaData->idProperties);
    }
}
