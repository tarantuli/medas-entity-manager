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
use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Exceptions\{OriginalClassNotFound, ReferenceFetchFailed};
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\EntityManager\Selector\Selectors\WithValues;

#[Service]
readonly class Hydrator
{
    public function __construct(
        private EntityValueFetchersManager    $entityValueFetchersManager,
        private IdValue                       $idValue,
        private MetaDataManager               $metaDataManager,
        private OriginalClassFetcherManager   $originalClassFetcherManager,
        private SelectorRecordsFetcherManager $selectorRecordsFetcherManager,
        private ValueSetter                   $valueSetter,
    )
    {
    }

    public function hydrate(MetaData $metaData, object $entity, EntityManager $entityManager): void
    {
        foreach ($metaData->properties as $property) {
            if ($property->isId) {
                continue;
            }

            foreach ($this->entityValueFetchersManager->get() as $entityValueFetcher) {
                $fetchResult = $entityValueFetcher->fetch($metaData, $entity, $property);

                if ($fetchResult->foundValue) {
                    $this->valueSetter->set(
                        $metaData,
                        $entity,
                        $property->name,
                        $fetchResult->value
                    );

                    break;
                }
            }
        }

        foreach ($metaData->backReferences as $backReference) {
            $collectionClass = $backReference->collectionClass;

            // Back-reference properties are intentionally absent from $metaData->properties, so set the
            // lazy collection straight onto the entity instead of going through the ValueSetter (which
            // resolves through the properties list).
            new \ReflectionProperty(
                $entity::class,
                $backReference->name

            )->setValue(
                $entity,
                new $collectionClass(fn() => $this->fetchReferences($entity, $backReference, $entityManager))
            );
        }
    }

    private function fetchReferences(
        object                 $entity,
        MetaData\BackReference $backReference,
        EntityManager          $entityManager
    ): array
    {
        $foundRecords = false;
        $records = [];

        foreach ($this->selectorRecordsFetcherManager->get() as $selectorRecordsFetcher) {
            $fetchResult = $selectorRecordsFetcher->fetch(new WithValues(
                $backReference->entity,
                [$backReference->property => $entity->id]
            ));

            if ($fetchResult->foundValue) {
                $records = $fetchResult->value;
                $foundRecords = true;

                break;
            }
        }

        if (!$foundRecords) {
            throw new ReferenceFetchFailed(
                $entity::class,
                $backReference->name,
                $backReference->entity
            );
        }

        $metaData = $this->metaDataManager->get($backReference->entity);
        $entities = [];

        foreach ($records as $record) {
            $idValue = $this->idValue->get($record, $metaData);
            $entities[] = $entityManager->get($metaData->className, $idValue);
        }

        return $entities;
    }

    public function fetchOriginalClass(MetaData $metaData, mixed $id): string
    {
        foreach ($this->originalClassFetcherManager->get() as $originalClassFetcher) {
            $result = $originalClassFetcher->fetch($metaData, $id);

            if ($result->foundValue) {
                return $result->value;
            }
        }

        throw new OriginalClassNotFound($metaData->className, $id);
    }
}
