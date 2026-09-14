<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, Exceptions\StorageExceptionType, Interfaces\StorageException};

#[Service]
readonly class Repository
{
    public function __construct(
        private Entities\IdValue                                     $idValue,
        private Entities\ValueFetchers\SelectorRecordsFetcherManager $selectorRecordsFetcherManager,
        private EntityManager                                        $entityManager,
        private MetaDataManager                                      $metaDataManager,
    )
    {
    }

    /**
     * The return value is an array of objects of type `$entity`.
     */
    /*
     * This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function fetchAll(string $entity, bool $trackChanges = true): array
    {
        return $this->fetch(
            new Selector\Selectors\AllEntities($entity),
            trackChanges: $trackChanges
        );
    }

    /**
     * $trackChanges: false skips change-diffing for every entity this returns -- see
     * EntityManager::get(). Use it for a fetch you know won't be mutated (an existence check, a
     * lookup used only to read or to decide whether to create something).
     *
     * @return object[]
     */
    public function fetch(Selector\Selector $selector, array $arguments = [], bool $trackChanges = true): array
    {
        $entities = [];
        $records = [];

        foreach ($this->selectorRecordsFetcherManager->get() as $selectorRecordsFetcher) {
            $fetchResult = $selectorRecordsFetcher->fetch($selector, $arguments);

            if ($fetchResult->foundValue) {
                $records = $fetchResult->value;

                break;
            }
        }

        $metaData = $this->metaDataManager->get($selector->entity());

        foreach ($records as $record) {
            $idValue = $this->idValue->get($record, $metaData);
            $entities[] = $this->entityManager->get($metaData->className, $idValue, $trackChanges);
        }

        return $entities;
    }

    public function fetchCount(Selector\Selector $selector, array $arguments = []): int|null
    {
        foreach ($this->selectorRecordsFetcherManager->get() as $selectorRecordsFetcher) {
            $fetchResult = $selectorRecordsFetcher->fetchCount($selector, $arguments);

            if ($fetchResult->foundValue) {
                return $fetchResult->value;
            }
        }

        return null;
    }

    /**
     * The return value is an object of type `$entity`.
     */
    /*
     * This is specified in PhpStorm in .phpstorm.meta.php
     */
    /**
     * $trackChanges only governs the found branch -- an entity this creates is always tracked
     * (it has to be, to get persisted at all), so the flag has nothing to do there.
     */
    public function getOrCreate(
        string        $entity,
        array         $values,
        \Closure|null $creationValues = null,
        bool          $persistOnCreate = true,
        bool          $flushOnPersist = true,
        bool          $trackChanges = true,
    ): object
    {
        return $this->fetchOrCreate(
            new Selector\Selectors\WithValues($entity, $values),
            $values,
            $creationValues,
            $persistOnCreate,
            $flushOnPersist,
            $trackChanges,
        );
    }

    /** $trackChanges only governs the found branch -- see getOrCreate(). */
    public function fetchOrCreate(
        Selector\Selector $selector,
        array             $values,
        \Closure|null     $creationValues = null,
        bool              $persistOnCreate = true,
        bool              $flushOnPersist = true,
        bool              $trackChanges = true,
    ): object
    {
        if ($object = $this->fetchOne($selector, $values, $trackChanges)) {
            return $object;
        }

        $object = $this->entityManager->create(
            $selector->entity(),
            $creationValues ? array_merge($values, $creationValues()) : $values
        );

        if ($persistOnCreate) {
            $this->entityManager->persist($object);

            if ($flushOnPersist) {
                try {
                    $this->entityManager->flush();
                }
                catch (StorageException $e) {
                    if ($e->exceptionType !== StorageExceptionType::DuplicateKey) {
                        throw $e;
                    }

                    // A concurrent request inserted the same row between our SELECT and INSERT.
                    // Discard the entity we just created and return the one that won the race.
                    $this->entityManager->discard($object);

                    return $this->fetchOne($selector, $values, $trackChanges) ?? throw $e;
                }
            }
        }

        return $object;
    }

    /** $trackChanges: false skips change-diffing for the returned entity -- see EntityManager::get(). */
    public function fetchOne(
        Selector\Selector $selector,
        array             $arguments = [],
        bool              $trackChanges = true
    ): object|null
    {
        return $this->fetch($selector, $arguments, $trackChanges)[0] ?? null;
    }

    public function fetchReferences(
        object                 $entity,
        MetaData\BackReference $backReference,
        bool                   $trackChanges = true
    ): array
    {
        return $this->fetch(
            new Selector\Selectors\WithValues(
                $backReference->entity,
                [$backReference->property => $entity->id]
            ),
            trackChanges: $trackChanges
        );
    }
}
