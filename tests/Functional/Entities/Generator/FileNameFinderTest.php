<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\Entities\Generator;

use Medas\EntityManager\Entities\Generator\FileNameFinder;
use Medas\EntityManagerTest\BaseTest;

class FileNameFinderTest extends BaseTest
{
    public function testCorrectFileNameGeneration(): void
    {
        $fileName = service(FileNameFinder::class)->find('Medas\EntityManagerTest\MockUps\EntityName');

        self::assertStringContainsString('MockUps\EntityName', $fileName);
    }
}
