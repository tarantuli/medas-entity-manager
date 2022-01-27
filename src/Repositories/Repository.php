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

    public function findOne(array $conditions): object
    {
        $record = $this->fetcher->fetchRecord($this->metaData, $conditions);
        $idValues = $this->idValues->extract($record, $this->metaData);

        return em()->get($this->metaData->className, $idValues);
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
}
