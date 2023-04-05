<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\Attributes\{Entity, Id, Property};
use Medas\EntityManager\Properties\SerializingHandler;
use Medas\EntityManager\Types\Guid;

#[Entity]
class MockHandlerEntity
{
    #[Id]
    private Guid $guid;

    #[Property(handler: SerializingHandler::class)]
    private PropertyClass $propertyClass;
}
