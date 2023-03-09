<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\{Fetcher, IdValue};
use Medas\EntityManager\Selector\{Selector, Selectors\AllEntities, Selectors\WithValues};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Repository
{
    public function __construct(
        private readonly IdValue         $idValue,
        private readonly Fetcher         $fetcher,
        private readonly MetaDataManager $metaDataManager,
    )
    {
    }

    /**
     * The return value is an array of objects of type $entity. This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function fetchAll(string $entity): array
    {
        return $this->fetch(new AllEntities($entity));
    }

    /** @return object[] */
    public function fetch(Selector $selector, array $arguments = []): array
    {
        $entities = [];
        $records = $this->fetcher->fetch($selector, $arguments);
        $metaData = $this->metaDataManager->get($selector->definition()->entity);

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
            new WithValues($entity, $values),
            $values,
            fn() => $values,
            $persistOnCreate,
            $flushOnPersist
        );
    }

    public function fetchOrCreate(
        Selector $selector,
        array    $values,
        \Closure $creationValues = null,
        bool     $persistOnCreate = true,
        bool     $flushOnPersist = true,
    ): object
    {
        if ($object = $this->fetchOne($selector, $values)) {
            return $object;
        }

        $object = em()->create(
            $selector->definition()->entity,
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

    public function fetchOne(Selector $selector, array $arguments = []): object|null
    {
        return $this->fetch($selector, $arguments)[0] ?? null;
    }

    public function fetchReferences(object $entity, MetaData\Reference $reference): array
    {
        return $this->fetch(new WithValues($reference->entity, [$reference->property => $entity->id]));
    }
}
