<?php

declare(strict_types=1);

namespace Medas\EntityManager\Repositories;

use Medas\EntityManager\Entities\Fetcher;
use Medas\EntityManager\Entities\IdValues;
use Medas\EntityManager\MetaData;

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
    public function findAll(array $conditions): array
    {
        $entities = [];
        $records = $this->fetcher->fetchAll($this->metaData, $conditions);

        foreach ($records as $record) {
            $idValues = $this->idValues->extract($record, $this->metaData);
            $entities[] = em()->get($this->metaData->className, $idValues);
        }

        return $entities;
    }

    public function getOrCreate(array $conditions,
                                bool  $persistOnCreate = true,
                                bool  $flushOnPersist = true): object
    {
        if ($object = $this->findOne($conditions)) {
            return $object;
        }

        $object = em()->create($this->metaData->className, $conditions);

        if ($persistOnCreate) {
            em()->persist($object);

            if ($flushOnPersist) {
                em()->flush();
            }
        }

        return $object;
    }

    public function findOne(array $conditions): object|null
    {
        if (!$record = $this->fetcher->fetchRecord($this->metaData, $conditions)) {
            return null;
        }

        $idValues = $this->idValues->extract($record, $this->metaData);

        return em()->get($this->metaData->className, $idValues);
    }
}
