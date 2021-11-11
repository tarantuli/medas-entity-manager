<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Exceptions\IdValueShouldBeAnArrayException;
use Medas\EntityManager\Exceptions\IdValueShouldBeAScalarException;
use Medas\EntityManager\Exceptions\MissingIdValueException;
use Medas\EntityManager\Hydration\Hydrator;
use Medas\EntityManager\Hydration\ValueSetter;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities = [];

    public function __construct(
        private PropertyAccessibleMaker $propertyAccessibleMaker,
        private ValueSetter             $valueSetter,
        private MetaDataManager         $metadataManager,
        private Hydrator                $hydrator,
    )
    {
    }

    public function get(string $className, mixed $id): object
    {
        $metaData = $this->metadataManager->get($className);
        $idHash = $this->getIdHash($id, $metaData);

        if (!array_key_exists($className, $this->entities)) {
            $this->entities[$className] = [];
            $this->propertyAccessibleMaker->makeAccessible($metaData);
        }

        if (!array_key_exists($idHash, $this->entities[$className])) {
            $this->entities[$className][$idHash] = $entity = new $className();
            $this->setIdValues($metaData, $entity, $id);

            if ($metaData->entity->table) {
                $this->hydrator->hydrate($entity, $metaData);
            }
        }

        return $this->entities[$className][$idHash];
    }

    private function getIdHash(mixed $id, MetaData $metaData): string
    {
        if (!$metaData->hasCompositeId) {
            if (!is_scalar($id)) {
                throw new IdValueShouldBeAScalarException($metaData->className, gettype($id));
            }

            return (string) $id;
        }

        if (!is_array($id)) {
            throw new IdValueShouldBeAnArrayException($metaData->className, gettype($id));
        }

        return $this->getComplexIdHash($id, $metaData);
    }

    private function getComplexIdHash(array $idValues, MetaData $metaData): string
    {
        return json_encode($this->getIdValues($idValues, $metaData));
    }

    private function getIdValues(array $idValues, MetaData $metaData): array
    {
        $idProperties = $metaData->idProperties;

        $values = [];

        foreach ($idProperties as $idProperty) {
            if (!array_key_exists($idProperty->name, $idValues)) {
                throw new MissingIdValueException($idProperty->name);
            }

            $values[$idProperty->name] = $idValues[$idProperty->name];
        }
        return $values;
    }

    private function setIdValues(MetaData $metaData, object $entity, mixed $id)
    {
        if ($metaData->hasCompositeId) {
            $idValues = $this->getIdValues($id, $metaData);
        }
        else {
            $idValues = [$metaData->idProperty->name => $id];
        }

        $this->valueSetter->setValues($metaData, $entity, $idValues);
    }
}
