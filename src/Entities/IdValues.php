<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Exceptions\IdPropertyNotGivenException;
use Medas\EntityManager\Exceptions\IdValueShouldBeAnArrayException;
use Medas\EntityManager\Exceptions\NonIdPropertyGivenException;
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

        if (is_scalar($id)) {
            if ($metaData->hasCompositeId) {
                throw new IdValueShouldBeAnArrayException($className, get_debug_type($id));
            }

            return [$metaData->idProperty->name => $id];
        }

        $idValues = $this->extract($id, $metaData);

        if ($superfluousValues = array_diff_key($id, $idValues)) {
            throw new NonIdPropertyGivenException(
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
                throw new IdPropertyNotGivenException($metaData->className, $idProperty->name);
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
