<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Flush;

use Medas\EntityManager\Attributes\{AppendOnly, Entity, Id};

#[AppendOnly]
#[Entity]
class FlushLogEntry
{
    #[Id]
    public int $id;

    public string $message = '';
}
