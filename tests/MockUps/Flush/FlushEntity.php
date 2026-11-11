<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Flush;

use Medas\EntityManager\Attributes\{Entity, Id};

// An ordinary entity for the flush tests. There is no record behind it, so get() gives one with
// only its id, and the default name.
#[Entity]
class FlushEntity
{
    #[Id]
    public int $id;

    public string $name = '';
}
