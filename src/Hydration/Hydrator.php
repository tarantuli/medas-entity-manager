<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\Entities\Fetcher;
use Medas\EntityManager\Entities\IdValues;
use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Hydrator
{
    public function __construct(
        private IdValues     $idValues,
        private Fetcher|null $fetcher,
        private ValueGetter  $valueGetter,
        private ValueSetter  $valueSetter,
    )
    {
    }

    public function hydrate(MetaData $metaData, object $entity): void
    {
        if (!$this->fetcher) {
            return;
        }

        foreach ($metaData->properties as $property) {
            $value = $this->fetcher->fetch($metaData, $property);

            $this->valueSetter->set($metaData, $entity, $property->name, $value);
        }

        /*$record = $this->fetcher->fetchRecord(
                    $metaData,
                    filters: $this->valueGetter->get($entity, $metaData->idProperties)
                );

                foreach ($metaData->properties as $property) {
                    $value = $record->get($property->name);
                    $value = $property->type->deserialize($value);

                    $this->valueSetter->set($metaData, $entity, $property->name, $value);
                }
        */
    }

    public function setIdValues(MetaData $metaData, object $entity, array $idValues)
    {
        $this->valueSetter->setValues($metaData, $entity, $idValues);
    }
}
