<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage;

use Medas\EntityManager\Storage\Interfaces\Action;
use Medas\EntityManager\Storage\Interfaces\Storage;

abstract class BaseAction implements Action
{
    protected Storage $storage;
    private \Closure|null $onComplete = null;

    public function storage(): Storage
    {
        return $this->storage;
    }

    public function setStorage(Storage $database): self
    {
        $this->storage = $database;

        return $this;
    }

    public function onComplete(): ?\Closure
    {
        return $this->onComplete;
    }

    public function setOnComplete(\Closure|null $onComplete): self
    {
        $this->onComplete = $onComplete;

        return $this;
    }
}
