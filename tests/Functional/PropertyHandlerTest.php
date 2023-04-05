<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\Types\{Text, TypeFinder};
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\MockHandlerEntity;

class PropertyHandlerTest extends BaseTestClass
{
    public function testHandler(): void
    {
        $finder = service(TypeFinder::class);
        $class = new \ReflectionClass(MockHandlerEntity::class);

        self::assertInstanceOf(Text::class, $finder->find($class->getProperty('propertyClass')));
    }
}
