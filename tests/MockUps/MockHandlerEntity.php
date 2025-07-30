<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\Core\Attributes\Handler;
use Medas\Core\Types\Uuid;
use Medas\EntityManager\{Attributes\Entity,Attributes\Id,Properties\SerializingHandler};

#[Entity]
class MockHandlerEntity
{
    #[Id]
    private Uuid $uuid;

    #[Handler(SerializingHandler::class)]
    private PropertyClass $propertyClass;
}
