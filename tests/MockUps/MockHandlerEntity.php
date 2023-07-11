<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\Attributes\{Entity, Handler, Id};
use Medas\EntityManager\Properties\SerializingHandler;
use Medas\EntityManager\Types\Guid;

#[Entity]
class MockHandlerEntity
{
    #[Id]
    private Guid $guid;

    #[Handler(SerializingHandler::class)]
    private PropertyClass $propertyClass;
}
