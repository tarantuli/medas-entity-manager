<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

interface Store
{
    public function storage(): Storage;

    public function getRecord(array $filters): StoreRecord|null;

    public function prepareCreate(array $values): Action;

    public function prepareUpdate(array $updates, array $conditions): Action;

    public function prepareGet(array $filters): Action;
}
