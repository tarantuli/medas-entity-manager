<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Inspectors;

use Medas\EntityManager\EntityManager;
use Medas\ServiceManager\Service;

#[Service]
class EntityManagerInspector extends EntityManager
{
    public function getEntities(): array
    {
        return $this->entities;
    }
}
