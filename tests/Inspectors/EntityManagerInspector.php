<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Inspectors;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\EntityManager;

#[Service]
class EntityManagerInspector extends EntityManager
{
    public function getEntities(): array
    {
        return $this->entities;
    }
}
