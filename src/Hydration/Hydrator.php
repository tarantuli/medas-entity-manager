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
            $fetchResult = $this->fetcher->fetchValue($metaData, $entity, $property);

            if ($fetchResult->foundValue) {
                $this->valueSetter->set($metaData, $entity, $property->name, $fetchResult->value);
            }
        }
    }

    public function setValues(MetaData $metaData, object $entity, array $values)
    {
        $this->valueSetter->setValues($metaData, $entity, $values);
    }
}
