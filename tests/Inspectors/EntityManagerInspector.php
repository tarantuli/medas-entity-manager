<?php

declare(strict_types=1);

namespace Medas\Test\Inspectors;

use Medas\EntityManager\EntityManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManagerInspector extends EntityManager
{
    public function getEntities(): array
    {
        return $this->entities;
    }
}
