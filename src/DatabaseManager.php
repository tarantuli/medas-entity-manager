<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class DatabaseManager
{
    /**
     * @var Storage\Interfaces\Database[]
     */
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

    public function add(Storage\Interfaces\Database $database, string $name = 'default', bool $isDefault = false)
    {
        $this->storages[$name] = $database;

        if ($isDefault === true || count($this->storages) === 1) {
            $this->default = $name;
        }
    }

    public function get(string $name = null): Storage\Interfaces\Database
    {
        return $this->storages[$name ?? $this->default];
    }
}
