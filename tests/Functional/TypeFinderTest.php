<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\Exceptions\PropertyHasMultipleImplicitTypes;
use Medas\EntityManager\Exceptions\PropertyHasNoImplicitType;
use Medas\EntityManager\Types\{Binary, DateTime, Guid, Integer, Relation, Text, TypeFinder};
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\MockEntityTypes;

class TypeFinderTest extends BaseTestClass
{
    public function testIdProperty(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(Integer::class, $finder->find($class->getProperty('id')));
    }

    public function testImplicitType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(Text::class, $finder->find($class->getProperty('implicitType')));
    }

    public function testExplicitType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(Binary::class, $finder->find($class->getProperty('explicitType')));
    }

    public function testRelationType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(Relation::class, $finder->find($class->getProperty('relation')));
    }

    public function testGuidType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(Guid::class, $finder->find($class->getProperty('guid')));
    }

    public function testNoPhpType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        $this->expectException(PropertyHasNoImplicitType::class);

        $finder->find($class->getProperty('noPhpType'));
    }

    public function testMixedType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        $this->expectException(PropertyHasNoImplicitType::class);

        $finder->find($class->getProperty('mixedType'));
    }

    public function testUnionType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        $this->expectException(PropertyHasMultipleImplicitTypes::class);

        $finder->find($class->getProperty('unionType'));
    }

    public function testPipeNullableType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(Text::class, $finder->find($class->getProperty('pipeNullableType')));
    }

    public function testQuestionMarkNullableType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(Text::class, $finder->find($class->getProperty('questionMarkNullableType')));
    }

    public function testDateTimeType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(DateTime::class, $finder->find($class->getProperty('dateTime')));
    }

    public function testEnumType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        self::assertInstanceOf(Relation::class, $finder->find($class->getProperty('enum')));
    }
}
