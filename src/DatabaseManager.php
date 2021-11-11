<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Interfaces\Storage\Database;

#[Service]
class DatabaseManager
{
    /** @var Database[] */
    private array $storages = [];

    private string $default;

    public function __construct()
    {
        $this->declareGlobalHelper();
    }

    private function declareGlobalHelper(): void
    {
        require_once 'GlobalFunctions.php';
    }

    public function add(Database $database, string $name = 'default', bool $isDefault = false)
    {
        $this->storages[$name] = $database;

        if ($isDefault || count($this->storages) === 1) {
            $this->default = $name;
        }
    }

    public function get(string $name = null): Database
    {
        return $this->storages[$name ?? $this->default];
    }
}
