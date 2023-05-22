<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest;

use Medas\EntityManagerTest\Inspectors\EntityManagerInspector;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    protected function entityManager(): EntityManagerInspector
    {
        return medas()->objectInstantiator()->instantiate(EntityManagerInspector::class);
    }
}
