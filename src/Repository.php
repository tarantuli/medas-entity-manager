<?php

declare(strict_types=1);

namespace Medas\EntityManager;

class Repository
{
    public function __construct(
        private IdValues $idValues,
        private MetaData $metaData,
    )
    {
    }

    public function findOne(array $conditions): object
    {
        $record = $this->metaData->getTable()->getRecord($conditions);
        $idValues = $this->idValues->extract($record->data(), $this->metaData);

        return em()->get($this->metaData->className, $idValues);
    }
}
