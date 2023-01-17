<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\Entities\Generator;

use Medas\EntityManager\Entities\Generator\EntityClassGenerator;
use Medas\EntityManagerTest\BaseTest;
use Medas\EntityManagerTest\MockUps\MockEntity;

class EntityClassGeneratorTest extends BaseTest
{
    public function testSimpleGeneration(): void
    {
        $content = service(EntityClassGenerator::class)->generate(MockEntity::class);

        diedump($content);
    }
}
