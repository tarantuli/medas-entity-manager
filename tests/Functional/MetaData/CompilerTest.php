<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\MetaData;

use Medas\EntityManager\MetaData\Compiler;
use Medas\EntityManagerTest\{BaseTestClass, MockUps\MockNullableProperties};

class CompilerTest extends BaseTestClass
{
    public function testNullability(): void
    {
        $metaData = service(Compiler::class)->compile(MockNullableProperties::class);

        self::assertFalse($metaData->property('stringType')->isNullable);
        self::assertTrue($metaData->property('mixedType')->isNullable);
        self::assertFalse($metaData->property('unionType')->isNullable);
        self::assertTrue($metaData->property('pipeNullableType')->isNullable);
        self::assertTrue($metaData->property('questionMarkNullableType')->isNullable);
    }
}
