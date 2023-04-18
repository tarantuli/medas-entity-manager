<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\{Fetcher, IdValue, ReferenceCollection};
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\EntityManager\Selector\Selectors\WithValues;

#[Service]
class Hydrator
{
    public function __construct(
        private readonly IdValue         $idValue,
        private Fetcher|null             $fetcher,
        private readonly MetaDataManager $metaDataManager,
        private readonly ValueGetter     $valueGetter,
        private readonly ValueSetter     $valueSetter,
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
                new ReferenceCollection(fn() => $this->fetchReferences($entity, $reference)));
        }
    }

    private function fetchReferences(object $entity, MetaData\Reference $reference): array
    {
        $entities = [];
        $records = $this->fetcher->fetch(new WithValues($reference->entity, [$reference->property => $entity->id]));
        $metaData = $this->metaDataManager->get($reference->entity);

        foreach ($records as $record) {
            $idValue = $this->idValue->get($record, $metaData);
            $entities[] = em()->get($metaData->className, $idValue);
        }

        return $entities;
    }

    public function setValues(MetaData $metaData, object $entity, array $values): void
    {
        $this->valueSetter->setValues($metaData, $entity, $values);
    }
}
