<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\EntityManager\Storage\Databases\Pdo\Table;
use Medas\Test\BaseTestCase;

class DatabaseManagerTest extends BaseTestCase
{
    public function testConnect(): void
    {
        self::assertInstanceOf(Database::class, db());
    }

    public function testGetTable(): void
    {
        $table = db()->getTable('database_manager_test');
        self::assertInstanceOf(Table::class, $table);
    }
}
