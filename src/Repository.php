<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{
    Attributes\EventListener,
    Attributes\Service,
    Exceptions\StorageExceptionType,
    Interfaces\StorageException
};

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
     * A selector with output values or groupings is refused, as its rows are not the entity's:
     * output values replace the entity's columns, and with a grouping a row is a group
     * (OutputValuesThroughRepository, GroupingsThroughRepository). The storage implementation
     * refuses it, not this class: it builds its query from the selector's definition anyway,
     * and can cache that work, where a check here would build the definition once more for
     * every fetch and count. A storage's SelectorRecordsFetcher, or the builder it hands the
     * selector to, must therefore make this check. Example: pdo-storage's SelectorQueryBuilder.
     * Such rows come from a store-level query instead (DefinitionQueryBuilder in pdo-storage).
     *
     * @return object[]
     */
    public function fetch(Selector\Selector $selector, array $arguments = [], bool $trackChanges = true): array
    {
        return array_map(
            fn(EntityWithDerivedValues $row) => $row->entity,
            $this->fetchWithDerivedValues($selector, $arguments, $trackChanges)
        );
    }

    /**
     * As fetch(), with per entity the derived values the selector's definition selects
     * (Selector\DerivedValues\DerivedValue). They are not set on the entity: they belong to
     * this query. An entity already in the identity map is not hydrated again; a lazy
     * reference to it is loaded (loaded()).
     *
     * @return EntityWithDerivedValues[]
     */
    public function fetchWithDerivedValues(
        Selector\Selector $selector,
        array             $arguments = [],
        bool              $trackChanges = true
    ): array
    {
        $rows = [];
        $fetchResult = null;

        foreach ($this->selectorRecordsFetcherManager->get() as $selectorRecordsFetcher) {
            $result = $selectorRecordsFetcher->fetch($selector, $arguments);

            if ($result->foundValue) {
                $fetchResult = $result;

                break;
            }
        }

        if ($fetchResult === null) {
            return [];
        }

        $metaData = $this->metaDataManager->get($selector->entity());
        $class = new \ReflectionClass($metaData->className);

        foreach ($fetchResult->value as $i => $record) {
            $idValue = $this->idValue->get($record, $metaData);

            $rows[] = new EntityWithDerivedValues(
                $this->loaded(
                    $class,
                    $this->entityManager->get($metaData->className, $idValue, $trackChanges)
                ),
                $fetchResult->derivedValues[$i] ?? [],
            );
        }

        return $rows;
    }

    // An entity already in the identity map as a lazy reference, such as a relation of one
    // loaded before, is loaded now, as a fetched row is expected to come back with its
    // values. Found in an account-backend list (#395): its normaliser, which skips
    // properties ReflectionProperty::isInitialized() calls uninitialised, gave such a row
    // with its id only.
    private function loaded(\ReflectionClass $class, object $entity): object
    {
        if ($class->isUninitializedLazyObject($entity)) {
            $class->initializeLazyObject($entity);
        }

        return $entity;
    }

    // A selector fetch() refuses is refused here too, by the storage (see fetch()): with a grouping
    // the count is one per group, and a list and its total come from the same selector
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
        return $this->fetch(
            new Selector\Selectors\FirstOf($selector),
            $arguments,
            $trackChanges
        )[0] ?? null;
    }

    /**
     * The entity with the given id, or null when no such row exists.
     *
     * Unlike EntityManager::get(), this checks: get() never looks whether the row
     * is there and, for an unknown id, returns an entity holding only that id. Use
     * this wherever the id comes from outside - a request, a message, a file -
     * and "it does not exist" has to be noticed.
     *
     * $trackChanges: false skips change-diffing for the returned entity -- see EntityManager::get().
     */
    /*
     * The return type is specified in PhpStorm in .phpstorm.meta.php
     */
    public function fetchById(string $entity, mixed $id, bool $trackChanges = true): object|null
    {
        $idProperty = $this->metaDataManager->get($entity)->idProperty->name;

        return $this->fetchOne(
            new Selector\Selectors\WithValues($entity, [$idProperty => $id]),
            trackChanges: $trackChanges
        );
    }

    #[EventListener]
    public function handleFetchEntityById(Events\FetchEntityById $event): void
    {
        $event->entity = $this->fetchById($event->type, $event->id);
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
