<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\{Entity, Id};

// Belongs to a rule of any concrete kind, through its abstract parent
#[Entity(store: 'mock_rule_tiers')]
class RuleTier
{
    #[Id]
    public int $id;

    public AbstractRule $rule;
}
