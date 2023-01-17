<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest;

use Medas\ServiceManager\ServiceManager;
use Medas\EntityManagerTest\Inspectors\EntityManagerInspector;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function entityManager(): EntityManagerInspector
    {
        $serviceManager = ServiceManager::get();
        return $serviceManager->instantiate(EntityManagerInspector::class);
    }
}
