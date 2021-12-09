<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\EntityManager\Storage\Databases\Pdo\Table;
use Medas\Test\BaseTest;

class DatabaseManagerTest extends BaseTest
{
    public function testConnect(): void
    {
        self::assertInstanceOf(Database::class, db());
    }

    public function testGetTable(): void
    {
        $table = db()->getStore('database_manager_test');
        self::assertInstanceOf(Table::class, $table);
    }
}
