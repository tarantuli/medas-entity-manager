<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\MetaData;

use Medas\EntityManager\MetaData\Compiler;
use Medas\EntityManagerTest\{BaseTestClass, MockUps\MockEntity, MockUps\MockNullableProperties};

class CompilerTest extends BaseTestClass
{
    // The id carries no IsReadable or IsWritable of its own: #[Id] alone makes it
    // readable, and writable when the entity is created but not afterwards.
    public function testIdIsReadableAndWritableOnCreationOnly(): void
    {
        $metaData = service(Compiler::class)->compile(MockEntity::class);

        self::assertContains('id', array_column($metaData->readableFields, 'source'));

        $writableIdFields = array_values(array_filter(
            $metaData->writableFields,
            fn($field) => $field->source === 'id'
        ));

        self::assertCount(1, $writableIdFields);
        self::assertTrue($writableIdFields[0]->onCreate);
        self::assertFalse($writableIdFields[0]->onUpdate);
    }

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
