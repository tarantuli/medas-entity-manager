<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Hydration\Hydrator;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities = [];

    public function __construct(
        private GlobalFunctionsDefiner $globalFunctionsDefiner,
        private Hydrator               $hydrator,
        private IdHash                 $idHash,
        private MetaDataManager        $metadataManager,
        private PropertyAccessManager  $propertyAccessManager,
    )
    {
    }

    public function get(string $className, mixed $id): object
    {
        $metaData = $this->metadataManager->get($className);
        $idHash = $this->idHash->get($id, $metaData);

        if (!array_key_exists($className, $this->entities)) {
            $this->entities[$className] = [];
            $this->propertyAccessManager->makeAccessible($metaData);
        }

        if (!array_key_exists($idHash, $this->entities[$className])) {
            $this->entities[$className][$idHash] = $entity = new $className();
            $this->hydrator->setIdValues($metaData, $entity, $id);

            if ($metaData->entity->table) {
                $this->hydrator->hydrate($metaData, $entity);
            }
        }

        return $this->entities[$className][$idHash];
    }

}
