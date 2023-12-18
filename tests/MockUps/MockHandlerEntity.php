<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\Core\Attributes\Handler;
use Medas\EntityManager\{
    Attributes\Entity,
    Attributes\Id,
    Properties\SerializingHandler,
    Types\Guid
};

#[Entity]
class MockHandlerEntity
{
    #[Id]
    private Guid $guid;

    #[Handler(SerializingHandler::class)]
    private PropertyClass $propertyClass;
}
