<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest;

use Medas\EntityManager\{EntityManager, EntityManagerContext};
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    // A fresh entity manager, not the shared service, so a test starts with an empty
    // identity map. Pass a context to look into that identity map from the test.
    protected function entityManager(EntityManagerContext $context = new EntityManagerContext()): EntityManager
    {
        return medas()->objectInstantiator()->instantiate(
            EntityManager::class,
            ['context' => $context]
        );
    }
}
