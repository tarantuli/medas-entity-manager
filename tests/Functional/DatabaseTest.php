<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testGetByValues(): void
    {
        $table = db()->getTable('database_manager_test');

        $records = [];// $table->getByValues(['id' => 1]);

        $this->assertEquals([], $records);

    }
}
