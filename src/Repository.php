<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\Fetcher;
use Medas\EntityManager\Entities\IdValues;
use Medas\EntityManager\Selector\Selector;
use Medas\EntityManager\Selector\Selectors\AllEntities;
use Medas\EntityManager\Selector\Selectors\WithValues;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Repository
{
    public function __construct(
        private IdValues        $idValues,
        private Fetcher         $fetcher,
        private MetaDataManager $metaDataManager,
    )
    {
    }

    /** @return object[] */
    public function fetchAll(string $entity): array
    {
        return $this->fetch(new AllEntities($entity));
    }

    /** @return object[] */
    public function fetch(Selector $selector, array $arguments = []): array
    {
        $entities = [];
        $records = $this->fetcher->fetch($selector, $arguments);
        $metaData = $this->metaDataManager->get($selector->entity());

        foreach ($records as $record) {
            $idValues = $this->idValues->extract($record, $metaData);
            $entities[] = em()->get($metaData->className, $idValues);
        }

        return $entities;
    }

    /**
     * The return value  is an object of type $entity. This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function getOrCreate(string $entity,
                                array  $values,
                                bool   $persistOnCreate = true,
                                bool   $flushOnPersist = true): object
    {
        if ($object = $this->fetchOne(new WithValues($entity, $values))) {
            return $object;
        }

        $object = em()->create($entity, $values);

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
}
