<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

use Medas\EntityManager\Storage\TableBlueprint;

interface Database
{
    public function createTable(TableBlueprint $blueprint): void;

    public function updateTable(TableBlueprint $blueprint): void;

    public function deleteTable(string $name): void;

    public function getTable(string $name): Table;
}
