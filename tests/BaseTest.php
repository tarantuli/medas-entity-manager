<?php

declare(strict_types=1);

namespace Medas\Test;

use Medas\EntityManager\EntityManager;
use Medas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function getEntityManager(): EntityManager
    {
        $serviceManager = ServiceManager::get();
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $serviceManager->resolve(EntityManager::class);
    }
}
