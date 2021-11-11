<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\EntityManager\Attributes\Types\Integer;
use Medas\EntityManager\Storage\TableBlueprint;
use Medas\ServiceManager\Interfaces\Storage\Table;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private const TABLE_NAME = 'database_test';

    public function testCreateTableBlueprint(): TableBlueprint
    {
        $blueprint = new TableBlueprint(self::TABLE_NAME);
        self::assertInstanceOf(TableBlueprint::class, $blueprint);

        $blueprint->addField('id', new Integer());

        return $blueprint;
    }

    /** @depends testCreateTableBlueprint */
    public function testCreateTable(TableBlueprint $blueprint): Table
    {
        if ($table = db()->getTable(self::TABLE_NAME)) {
            $table->drop();
        }

        return db()->createTable($blueprint);
    }

    public function testGetByValues(): void
    {
        $table = db()->getTable('database_manager_test');

        $records = $table->getRecord(['id' => 1]);

        $this->assertEquals([], $records);

    }
}
