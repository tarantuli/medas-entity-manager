<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\UnitOfWork;

use Medas\EntityManager\Storage\Databases;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class UnitOfWorkManager
{
    public function queueUpdate(UnitOfWork $unitOfWork, Databases\Pdo\Table $table, array $updates, array $conditions)
    {
        $unitOfWork->addUpdate(
            $table->database->queryBuilder()->update($table, $updates, $conditions)
                ->database($table->database)
        );
    }

    public function queueCreate(UnitOfWork $unitOfWork, Databases\Pdo\Table $table, array $values, \Closure $onComplete = null)
    {
        $unitOfWork->addCreate(
        $table->database->queryBuilder()->create($table, $values)
            ->onComplete($onComplete)->database($table->database)
        );
    }
}
