<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure;

use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\EntityManager\Storage\Databases\Pdo\Structure\Blueprint\Field;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityStructureFinder
{
    public function __construct(
        private MetaDataManager $metaDataManager,
        private TypeHandlerFactory $typeHandlerFactory,
    )
    {
    }

    public function find(string $className): Blueprint
    {
        $metaData = $this->metaDataManager->get($className);
        $blueprint = new Blueprint();

        $this->findName($metaData, $blueprint);
        $this->findFields($metaData, $blueprint);

        return $blueprint;
    }

    private function findName(MetaData $metaData, Blueprint $blueprint): void
    {
        $blueprint->name = $metaData->entity->store;
    }

    private function findFields(MetaData $metaData, Blueprint $blueprint): void
    {
        foreach ($metaData->properties as $property) {
            $handler = $this->typeHandlerFactory->for($property->type);
            $blueprint->addField(new Field($property->name, $handler->getFieldType($property)));
        }
    }
}
