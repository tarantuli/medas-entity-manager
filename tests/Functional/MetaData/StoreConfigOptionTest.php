<?php

declare(strict_types=1);

namespace Functional\MetaData;

use Medas\EntityManager\MetaData\Compiler;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\StoreConfigOptions\User;

class StoreConfigOptionTest extends BaseTestClass
{
    public function testStoreConfigOption(): void
    {
        $metaData = service(Compiler::class)->compile(User::class);

        self::assertEquals('atypical-user-store', $metaData->entity->store);
    }
}
