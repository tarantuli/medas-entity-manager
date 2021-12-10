<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

interface Storage
{
    public function getStore(string $name): Store;

    public function beginTransaction(): void;

    public function rollbackTransaction(): void;

    public function commitTransaction(): void;

    public function lastGeneratedValue(): int|null;
}
