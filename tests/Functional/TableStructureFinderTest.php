<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\Test\BaseTest;

class TableStructureFinderTest extends BaseTest
{
    public function testFindStructure(): void
    {
        $table = db()->getStore('stored_entities');

        diedump($table->getStructure());
    }
}
