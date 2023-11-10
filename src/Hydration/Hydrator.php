<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\{
    IdValue,
    ValueFetchers\EntityValueFetchersManager,
    ValueFetchers\OriginalClassFetcherManager,
    ValueFetchers\SelectorRecordsFetcherManager
};
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\EntityManager\Selector\Selectors\WithValues;

#[Service]
readonly class Hydrator
{
    public function __construct(
        private IdValue                       $idValue,
        private EntityValueFetchersManager    $entityValueFetchersManager,
        private SelectorRecordsFetcherManager $selectorRecordsFetcherManager,
        private OriginalClassFetcherManager   $originalClassFetcherManager,
        private MetaDataManager               $metaDataManager,
        private ValueGetter                   $valueGetter,
        private ValueSetter                   $valueSetter,
    )
    {
    }

    public function hydrate(MetaData $metaData, object $entity): void
    {
        foreach ($metaData->properties as $property) {
            foreach ($this->entityValueFetchersManager->get() as $entityValueFetcher) {
                $fetchResult = $entityValueFetcher->fetch($metaData, $entity, $property);

                if ($fetchResult->foundValue) {
                    $this->valueSetter->set($metaData, $entity, $property->name, $fetchResult->value);
                    break;
                }
            }
        }

        foreach ($metaData->references as $reference) {
            $collectionClass = $metaData->property($reference->name)->phpTypes[0];

            $this->valueSetter->set(
                $metaData,
                $entity,
                $reference->name,
                new $collectionClass(fn() => $this->fetchReferences($entity, $reference))
            );
        }
    }

    public function fetchOriginalClass(MetaData $metaData, mixed $id): string
    {
        foreach ($this->originalClassFetcherManager->get() as $originalClassFetcher) {
            $result = $originalClassFetcher->fetch($metaData, $id);

            if ($result->foundValue) {
                return $result->value;
            }
        }

        throw new \Exception('found no original class');
    }

    private function fetchReferences(object $entity, MetaData\Reference $reference): array
    {
        $foundRecords = false;
        $records = [];

        foreach ($this->selectorRecordsFetcherManager->get() as $selectorRecordsFetcher) {
            $fetchResult = $selectorRecordsFetcher->fetch(new WithValues(
                $reference->entity,
                [$reference->property => $entity->id]
            ));

            if ($fetchResult->foundValue) {
                $records = $fetchResult->value;
                $foundRecords = true;

                break;
            }
        }

        if (!$foundRecords) {
            throw new \Exception('found no references');
        }

        $metaData = $this->metaDataManager->get($reference->entity);
        $entities = [];

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
