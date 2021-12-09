<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Storage\UnitOfWork\UnitOfWork;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWorkExecutor;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Flusher
{
    public function __construct(
        private Persister          $entityPersister,
        private UnitOfWorkExecutor $unitOfWorkExecutor,
    )
    {
    }

    public function flush(array $entities, \SplObjectStorage $savedStates): bool
    {
        $unitOfWork = new UnitOfWork();

        foreach ($entities as $entity) {
            $this->entityPersister->prepare(
                $entity,
                $savedStates[$entity] ?? null,
                $unitOfWork
            );
        }

        return $this->unitOfWorkExecutor->execute($unitOfWork);
    }
}
