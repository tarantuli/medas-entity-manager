<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\Entities\Fetcher;
use Medas\EntityManager\Entities\IdValues;
use Medas\EntityManager\Entities\ReferenceCollection;
use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Hydrator
{
    public function __construct(
        private readonly IdValues    $idValues,
        private Fetcher|null         $fetcher,
        private readonly ValueGetter $valueGetter,
        private readonly ValueSetter $valueSetter,
    )
    {
    }

    public function setFetcher(Fetcher|null $fetcher): void
    {
        $this->fetcher = $fetcher;
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

        foreach ($metaData->references as $reference) {
            $this->valueSetter->set(
                $metaData, $entity, $reference->name,
                new ReferenceCollection(fn() => $this->fetcher->fetchReferences($metaData, $entity, $reference)));
        }
    }

    public function setValues(MetaData $metaData, object $entity, array $values): void
    {
        $this->valueSetter->setValues($metaData, $entity, $values);
    }
}
