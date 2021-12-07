<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class DatabaseManager
{
    /** @var Database[] */
    private array $databases = [];
    private string $default;

    public function add(Database $database, string $name = 'default', bool $isDefault = false)
    {
        $this->databases[$name] = $database;

        if ($isDefault || count($this->databases) === 1) {
            $this->default = $name;
        }
    }

    public function get(string $name = null): Database
    {
        return $this->databases[$name ?? $this->default];
    }
}
