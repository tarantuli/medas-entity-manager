<?php

declare(strict_types=1);

namespace Medas\EntityManager\Repositories;

use Medas\EntityManager\Entities\Fetcher;
use Medas\EntityManager\Entities\IdValues;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\Selector\Selector;
use Medas\EntityManager\Selector\Selectors\WithValues;

class Repository
{
    public function __construct(
        private IdValues $idValues,
        private Fetcher  $fetcher,
        private MetaData $metaData,
    )
    {
    }

    /** @return object[] */
    public function findAll(Selector $selector): array
    {
        $entities = [];
        $records = $this->fetcher->fetchAll($selector);

        foreach ($records as $record) {
            $idValues = $this->idValues->extract($record, $this->metaData);
            $entities[] = em()->get($this->metaData->className, $idValues);
        }

        return $entities;
    }

    public function getOrCreate(array $values,
                                bool  $persistOnCreate = true,
                                bool  $flushOnPersist = true): object
    {
        if ($object = $this->findOne(new WithValues($this->metaData->className, $values))) {
            return $object;
        }

        $object = em()->create($this->metaData->className, $values);

        if ($persistOnCreate) {
            em()->persist($object);

            if ($flushOnPersist) {
                em()->flush();
            }
        }

        return $object;
    }

    public function findOne(Selector $selector): object|null
    {
        if (!$record = $this->fetcher->fetchRecord($selector)) {
            return null;
        }

        $idValues = $this->idValues->extract($record, $this->metaData);

        return em()->get($this->metaData->className, $idValues);
    }
}
