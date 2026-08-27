<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Exceptions\ReferenceFetchFailed;
use Medas\EntityManager\Interfaces\HasSoftDeletes;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\EntityManager\Selector\Selectors\WithValues;

/**
 * Wires up an entity's back-reference collections: every #[ReferencedBy] property
 * gets a lazy collection whose loader fetches the referencing entities on first
 * access.
 *
 * Used on both sides of an entity's life: the Hydrator calls it when loading an
 * entity, and persist() calls it when a new entity is registered - so a freshly
 * created entity's back-references behave exactly like a loaded one's instead of
 * sitting uninitialized until something trips over them.
 */
#[Service]
readonly class ReferenceInitializer
{
    public function __construct(
        private IdValue                                     $idValue,
        private MetaDataManager                             $metaDataManager,
        private ValueFetchers\SelectorRecordsFetcherManager $selectorRecordsFetcherManager,
    )
    {
    }

    public function initialize(object $entity, EntityManager $entityManager): void
    {
        $metaData = $this->metaDataManager->get($entity::class);

        foreach ($metaData->backReferences as $backReference) {
            // Back-reference properties are intentionally absent from
            // $metaData->properties, so reach for the reflection directly.
            $reflection = new \ReflectionProperty($entity::class, $backReference->name);

            // Never clobber a collection that is already set - e.g. an entity
            // that was hydrated and is now being (re)persisted.
            if ($reflection->isInitialized($entity)) {
                continue;
            }

            $collectionClass = $backReference->collectionClass;

            $reflection->setValue(
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
            $referencedEntity = $entityManager->get($metaData->className, $idValue);

            // Soft-deleted entities are excluded from back-reference collections:
            // a lazy collection reflects the live set, not tombstoned rows.
            if ($referencedEntity instanceof HasSoftDeletes && $referencedEntity->isSoftDeleted()) {
                continue;
            }

            $entities[] = $referencedEntity;
        }

        return $entities;
    }
}
