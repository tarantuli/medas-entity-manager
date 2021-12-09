<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\UnitOfWork;

use Medas\EntityManager\Storage\Interfaces\Store;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class UnitOfWorkManager
{
    public function queueUpdate(UnitOfWork $unitOfWork, Store $store, array $updates, array $conditions)
    {
        $unitOfWork->addUpdate(
            $store->database->actionBuilder()->update($store, $updates, $conditions)
                ->setStorage($store->database)
        );
    }

    public function queueCreate(UnitOfWork $unitOfWork, Store $store, array $values, \Closure $onComplete = null)
    {
        $unitOfWork->addCreate(
            $store->database->actionBuilder()->create($store, $values)
                ->setOnComplete($onComplete)->setStorage($store->database)
        );
    }
}
