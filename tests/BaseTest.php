<?php

declare(strict_types=1);

namespace Medas\Test;

use Medas\EntityManager\EntityManager;
use Medas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function entityManager(): EntityManager
    {
        $serviceManager = ServiceManager::get();
        return $serviceManager->resolve(EntityManager::class);
    }
}
