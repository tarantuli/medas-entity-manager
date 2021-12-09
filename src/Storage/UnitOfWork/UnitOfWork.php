<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\UnitOfWork;

use Medas\EntityManager\Storage\Interfaces\Action;

class UnitOfWork
{
    /** @var Action[] */
    public array $creates = [];

    /** @var Action[] */
    public array $updates = [];

    public array $storages = [];

    public function addUpdate(Action $update)
    {
        if (!in_array($update->storage(), $this->storages)) {
            $this->storages[] = $update->storage();
        }

        $this->updates[] = $update;
    }

    public function addCreate(Action $create)
    {
        if (!in_array($create->storage(), $this->storages)) {
            $this->storages[] = $create->storage();
        }

        $this->creates[] = $create;
    }
}
