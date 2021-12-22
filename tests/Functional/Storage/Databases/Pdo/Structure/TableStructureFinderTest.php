<?php

declare(strict_types=1);

namespace Medas\Test\Functional\Storage\Databases\Pdo\Structure;

use Medas\EntityManager\Storage\Databases\Pdo\Structure\TableStructureFinder;
use Medas\Test\BaseTest;

class TableStructureFinderTest extends BaseTest
{
    public function testFindStructure(): void
    {
        $tsf = new TableStructureFinder(db());
        $structure = $tsf->find(db()->getStore('stored_entities'));

        self::assertEquals('stored_entities', $structure->name);
        self::assertEquals('datetime', $structure->fields['createdAt']->type);
        self::assertEquals('id', $structure->indexes['PRIMARY']->fields[0]->name);
    }
}
