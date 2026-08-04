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
    public function fetchAll(string $entity): array
    {
        return $this->fetch(new Selector\Selectors\AllEntities($entity));
    }

    /** @return object[] */
    public function fetch(Selector\Selector $selector, array $arguments = []): array
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
            $entities[] = $this->entityManager->get($metaData->className, $idValue);
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
    public function getOrCreate(
        string        $entity,
        array         $values,
        \Closure|null $creationValues = null,
        bool          $persistOnCreate = true,
        bool          $flushOnPersist = true,
    ): object
    {
        return $this->fetchOrCreate(
            new Selector\Selectors\WithValues($entity, $values),
            $values,
            $creationValues,
            $persistOnCreate,
            $flushOnPersist
        );
    }

    public function fetchOrCreate(
        Selector\Selector $selector,
        array             $values,
        \Closure|null     $creationValues = null,
        bool              $persistOnCreate = true,
        bool              $flushOnPersist = true,
    ): object
    {
        if ($object = $this->fetchOne($selector, $values)) {
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

                    return $this->fetchOne($selector, $values) ?? throw $e;
                }
            }
        }

        return $object;
    }

    public function fetchOne(Selector\Selector $selector, array $arguments = []): object|null
    {
        return $this->fetch($selector, $arguments)[0] ?? null;
    }

    public function fetchReferences(object $entity, MetaData\BackReference $backReference): array
    {
        return $this->fetch(new Selector\Selectors\WithValues(
            $backReference->entity,
            [$backReference->property => $entity->id]
        ));
    }
}
