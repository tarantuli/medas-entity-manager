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
        private readonly IdValues     $idValues,
        private readonly Fetcher|null $fetcher,
        private readonly ValueGetter  $valueGetter,
        private readonly ValueSetter  $valueSetter,
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

    public function setValues(MetaData $metaData, object $entity, array $values): void
    {
        $this->valueSetter->setValues($metaData, $entity, $values);
    }
}
