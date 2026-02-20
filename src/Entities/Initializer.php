<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{
    Exceptions\ClassDoesNotExist,
    Exceptions\UnknownProperties,
    Hydration\Hydrator,
    Hydration\ValueSetter,
    MetaData,
    MetaDataManager
};

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
        // Validate entity class
        if (!class_exists($className)) {
            throw new ClassDoesNotExist($className);
        }

        $metaData ??= $this->metaDataManager->get($className);

        // Validate all values are for known properties
        $unknownProperties = array_diff(
            array_keys($values),
            array_column($metaData->properties, 'name')
        );

        if ($unknownProperties) {
            throw new UnknownProperties($className, $unknownProperties);
        }

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
