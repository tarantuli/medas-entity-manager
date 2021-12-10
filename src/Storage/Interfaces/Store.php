<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

interface Store
{
    public function getRecord(array $filters): StoreRecord;

    public function prepareCreate(array $values): Action;

    public function prepareUpdate(array $updates, array $conditions): Action;

    public function prepareGet(array $filters): Action;
}
