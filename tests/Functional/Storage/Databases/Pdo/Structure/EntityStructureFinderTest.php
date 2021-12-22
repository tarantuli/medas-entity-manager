<?php

declare(strict_types=1);

namespace Medas\Test\Functional\Storage\Databases\Pdo\Structure;

use Medas\EntityManager\Storage\Databases\Pdo\Structure\EntityStructureFinder;
use Medas\Test\BaseTest;
use Medas\Test\MockUps\StoredEntity;

class EntityStructureFinderTest extends BaseTest
{
    public function testFindStructure(): void
    {
        $esf = service(EntityStructureFinder::class);
        $structure = $esf->find(StoredEntity::class);

        self::assertEquals('stored_entities', $structure->name);
        self::assertEquals('datetime', $structure->fields['createdAt']->type);
        self::assertEquals('id', $structure->indexes['PRIMARY']->fields[0]->name);
    }
}
