<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\IdValues;
use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Hydrator
{
    public function __construct(
        private IdValues    $idValues,
        private ValueGetter $valueGetter,
        private ValueSetter $valueSetter,
    )
    {
    }

    public function hydrate(MetaData $metaData, object $entity): void
    {
        $record = $metaData->getTable()->getRecord(
            filters: $this->valueGetter->getValues($entity, $metaData->idProperties)
        );

        foreach ($metaData->properties as $property) {
            $value = $record->get($property->name);
            $value = $property->type->deserialize($value);

            $this->valueSetter->set($metaData, $entity, $property->name, $value);
        }
    }

    public function setIdValues(MetaData $metaData, object $entity, mixed $id)
    {
        if ($metaData->hasCompositeId) {
            $idValues = $this->idValues->get($id, $metaData);
        }
        else {
            $idValues = [$metaData->idProperty->name => $id];
        }

        $this->valueSetter->setValues($metaData, $entity, $idValues);
    }
}
