<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Lazy;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity]
class LazyBook
{
    #[Id]
    public int $id;

    public LazyAuthor $author;
}
