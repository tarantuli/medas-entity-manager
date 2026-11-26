<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Casting;

use Medas\Core\Date;
use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity(store: 'mock_dated_entities')]
class DatedEntity
{
    #[Id]
    public int $id;

    public Date $day;
    public \DateTime $moment;
}
