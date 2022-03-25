<?php

declare(strict_types=1);

namespace Medas\Test;

use Medas\ServiceManager\ServiceManager;
use Medas\Test\Inspectors\EntityManagerInspector;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function entityManager(): EntityManagerInspector
    {
        $serviceManager = ServiceManager::get();
        return $serviceManager->instantiate(EntityManagerInspector::class);
    }
}
