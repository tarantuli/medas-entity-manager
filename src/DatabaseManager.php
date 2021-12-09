<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Storage\Interfaces\Storage;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class DatabaseManager
{
    /** @var Storage[] */
    private array $storages = [];
    private string $default;

    public function add(Storage $storage, string $name = 'default', bool $isDefault = false)
    {
        $this->storages[$name] = $storage;

        if ($isDefault || count($this->storages) === 1) {
            $this->default = $name;
        }
    }

    public function get(string $name = null): Storage
    {
        return $this->storages[$name ?? $this->default];
    }
}
