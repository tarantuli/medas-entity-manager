<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\Core\Attributes\Handler;
use Medas\EntityManager\{
    Attributes\Entity,
    Attributes\Id,
    Properties\SerializingHandler,
    Types\Uuid
};

#[Entity]
class MockHandlerEntity
{
    #[Id]
    private Uuid $uuid;

    #[Handler(SerializingHandler::class)]
    private PropertyClass $propertyClass;
}
