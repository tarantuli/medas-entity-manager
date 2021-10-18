<?php

declare(strict_types=1);

namespace EntityManager;

use EntityManager\Attributes\Entity;
use EntityManager\Exceptions\ClassIsNotAnEntityException;
use EntityManager\Exceptions\EntityDoesNotDefineIdPropertiesException;
use EntityManager\Exceptions\IdValueNotGivenException;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities = [];

    public function get(string $className, mixed $id): object
    {
        $metaData = $this->getMetaData($className);

        $idHash = $this->getIdHash($id, $metaData);

        if (!array_key_exists($idHash, $this->entities[$className])) {
            $this->entities[$className][$idHash] = $entity = new $className();
        }

        return $this->entities[$className][$idHash];
    }

    private function getMetaData(string $className): mixed
    {
        $class = new \ReflectionClass($className);

        if (!$class->getAttributes(Entity::class)) {
            throw new ClassIsNotAnEntityException($className);
        }

        if (!array_key_exists($className, $this->entities)) {
            $this->entities[$className] = [];
        }

        return MetaData::forClass($className);
    }

    private function getIdHash(mixed $id, MetaData $metaData): string
    {
        $idPropertyNames = $metaData->getIdPropertyNames();

        if (count($idPropertyNames) === 0) {
            throw new EntityDoesNotDefineIdPropertiesException($metaData->getClassName());
        }

        if (count($idPropertyNames) === 1) {
            return (string) $id;
        }

        return $this->getComplexIdHash($id, $idPropertyNames);
    }

    private function getComplexIdHash(array $id, array $idPropertyNames): string|false
    {
        $values = [];

        foreach ($idPropertyNames as $idPropertyName) {
            if (!array_key_exists($idPropertyName, $id)) {
                throw new IdValueNotGivenException($idPropertyName);
            }

            $values[] = $id[$idPropertyName];
        }

        return json_encode($values);
    }
}
