<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

interface Store
{
    public function getRecord(array $filters): StoreRecord;
}
