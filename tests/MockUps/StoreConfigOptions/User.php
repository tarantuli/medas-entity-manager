<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\StoreConfigOptions;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity, Entity\StoreConfigOption(UserStoreConfigOption::class)]
class User
{
    #[Id]
    public int $id;
}
