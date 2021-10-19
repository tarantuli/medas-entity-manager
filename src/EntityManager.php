<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Exceptions\IdValueNotGivenException;
use Medas\EntityManager\Hydration\ValueSetter;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities = [];

    public function __construct(
        private PropertyAccessibleMaker $propertyAccessibleMaker,
        private ValueSetter             $valueSetter
    )
    {
    }

    public function get(string $className, mixed $id): object
    {
        $metaData = MetaData::forClass($className);
        $idHash = $this->getIdHash($id, $metaData);

        if (!array_key_exists($className, $this->entities)) {
            $this->entities[$className] = [];
            $this->propertyAccessibleMaker->makeAccessible($metaData);
        }

        if (!array_key_exists($idHash, $this->entities[$className])) {
            $this->entities[$className][$idHash] = $entity = new $className();
            $this->setIdValues($metaData, $entity, $id);
        }

        return $this->entities[$className][$idHash];
    }

    private function getIdHash(mixed $id, MetaData $metaData): string
    {
        if (!$metaData->hasCompositeId()) {
            return (string) $id;
        }

        return $this->getComplexIdHash($id, $metaData);
    }

    private function getComplexIdHash(array $idValues, MetaData $metaData): string
    {
        return json_encode($this->getIdValues($idValues, $metaData));
    }

    private function getIdValues(array $idValues, MetaData $metaData): array
    {
        $idProperties = $metaData->getIdProperties();

        $values = [];

        foreach ($idProperties as $idProperty) {
            if (!array_key_exists($idProperty, $idValues)) {
                throw new IdValueNotGivenException($idProperty->name);
            }

            $values[] = $idValues[$idProperty->name];
        }
        return $values;
    }

    private function setIdValues(MetaData $metaData, object $entity, mixed $id)
    {
        if ($metaData->hasCompositeId()) {
            $idValues = $this->getIdValues($id, $metaData);
        }
        else {
            $idValues = [$metaData->getIdProperty()->name => $id];
        }

        $this->valueSetter->setValues($metaData, $entity, $idValues);
    }
}
