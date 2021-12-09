<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\UnitOfWork;

use Medas\EntityManager\Storage\Databases\Pdo\Queries\Query;

class UnitOfWork
{
    /** @var Query[] */
    public array $creates = [];

    /** @var Query[] */
    public array $updates = [];

    public array $databases = [];

    public function addUpdate(Query $update)
    {
        if (!in_array($update->database, $this->databases)) {
            $this->databases[] = $update->database;
        }

        $this->updates[] = $update;
    }

    public function addCreate(Query $create)
    {
        if (!in_array($create->database, $this->databases)) {
            $this->databases[] = $create->database;
        }

        $this->creates[] = $create;
    }
}
