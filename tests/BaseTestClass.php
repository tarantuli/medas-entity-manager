<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest;

use Medas\EntityManagerTest\Inspectors\EntityManagerInspector;
use Medas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    protected function entityManager(): EntityManagerInspector
    {
        $serviceManager = ServiceManager::get();
        return $serviceManager->instantiate(EntityManagerInspector::class);
    }
}
