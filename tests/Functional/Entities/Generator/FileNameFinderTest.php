<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\Entities\Generator;

use Medas\EntityManager\Entities\Generator\FileNameFinder;
use Medas\EntityManagerTest\BaseTestClass;

class FileNameFinderTest extends BaseTestClass
{
    public function testCorrectFileNameGeneration(): void
    {
        chdir(__DIR__ . '/../../../..');
        $fileName = service(FileNameFinder::class)->find('Medas\EntityManagerTest\MockUps\EntityName');

        self::assertStringContainsString('MockUps\EntityName', $fileName);
    }
}
