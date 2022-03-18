<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\EntityManager\Exceptions\PropertyHasMultipleImplicitTypesException;
use Medas\EntityManager\Exceptions\PropertyHasNoImplicitTypeException;
use Medas\EntityManager\Types\{Binary, DateTime, Integer, Relation, Text, TypeFinder};
use Medas\Test\BaseTest;
use Medas\Test\MockUps\MockEntityTypes;

class TypeFinderTest extends BaseTest
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

    public function testNoPhpType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        $this->expectException(PropertyHasNoImplicitTypeException::class);

        $finder->find($class->getProperty('noPhpType'));
    }

    public function testMixedType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        $this->expectException(PropertyHasNoImplicitTypeException::class);

        $finder->find($class->getProperty('mixedType'));
    }

    public function testUnionType(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockEntityTypes::class);
        $this->expectException(PropertyHasMultipleImplicitTypesException::class);

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
}
