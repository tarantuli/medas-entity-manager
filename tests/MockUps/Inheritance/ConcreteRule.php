<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity(store: 'mock_concrete_rules')]
class ConcreteRule extends AbstractRule
{
}
