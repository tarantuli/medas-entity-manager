<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;

#[Service]
readonly class Repository
{
    public function __construct(
        private Entities\IdValue                                     $idValue,
        private MetaDataManager                                      $metaDataManager,
        private Entities\ValueFetchers\SelectorRecordsFetcherManager $selectorRecordsFetcherManager,
    )
    {
    }

    /**
     * The return value is an array of objects of type $entity. This is specified in PhpStorm in .phpstorm.meta.php
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
            $entities[] = em()->get($metaData->className, $idValue);
        }

        return $entities;
    }

    /**
     * The return value is an object of type $entity. This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function getOrCreate(
        string $entity,
        array  $values,
        bool   $persistOnCreate = true,
        bool   $flushOnPersist = true,
    ): object
    {
        return $this->fetchOrCreate(
            new Selector\Selectors\WithValues($entity, $values),
            $values,
            fn() => $values,
            $persistOnCreate,
            $flushOnPersist
        );
    }

    public function fetchOrCreate(
        Selector\Selector $selector,
        array             $values,
        \Closure          $creationValues = null,
        bool              $persistOnCreate = true,
        bool              $flushOnPersist = true,
    ): object
    {
        if ($object = $this->fetchOne($selector, $values)) {
            return $object;
        }

        $object = em()->create(
            $selector->entity(),
            $creationValues ? array_merge($values, $creationValues()) : $values
        );

        if ($persistOnCreate) {
            em()->persist($object);

            if ($flushOnPersist) {
                em()->flush();
            }
        }

        return $object;
    }

    public function fetchOne(Selector\Selector $selector, array $arguments = []): object|null
    {
        return $this->fetch($selector, $arguments)[0] ?? null;
    }

    public function fetchReferences(object $entity, MetaData\Reference $reference): array
    {
        return $this->fetch(new Selector\Selectors\WithValues(
            $reference->entity,
            [$reference->property => $entity->id]
        ));
    }
}
