<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\ServiceManager\Interfaces\Storage\Database;
use Medas\ServiceManager\Interfaces\Storage\Table;
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
