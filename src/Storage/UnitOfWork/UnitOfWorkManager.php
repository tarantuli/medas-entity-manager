<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\UnitOfWork;

use Medas\EntityManager\Storage\Databases;
use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class UnitOfWorkManager
{
    public function __construct(private Database $database)
    {
    }

    public function queueUpdate(UnitOfWork $unitOfWork, Databases\Pdo\Table $table, array $updates, array $conditions)
    {
        $unitOfWork->updates[] = $this->database->queryBuilder()->update($table, $updates, $conditions);
    }

    public function queueCreate(UnitOfWork $unitOfWork, Databases\Pdo\Table $table, array $values, \Closure $onComplete = null)
    {
        $unitOfWork->creates[] = $this->database->queryBuilder()->create($table, $values)
            ->onComplete($onComplete);
    }
}
