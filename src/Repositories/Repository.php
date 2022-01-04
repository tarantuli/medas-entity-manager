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
        $idValues = $this->idValues->extract($record->data(), $this->metaData);

        return em()->get($this->metaData->className, $idValues);
    }
}
