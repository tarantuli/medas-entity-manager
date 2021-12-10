<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure;

use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\EntityManager\Storage\Databases\Pdo\Table;

class TableStructureFinder
{
    public function __construct(private Database $database)
    {
    }

    public function find(Table $table): Blueprint
    {
        $blueprint = new Blueprint();

        $structure = $table->getStructure();
        diedump($structure);

        return $blueprint;
    }
}
