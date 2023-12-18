<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest;

use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    protected function entityManager(): Inspectors\EntityManagerInspector
    {
        return medas()->objectInstantiator()->instantiate(Inspectors\EntityManagerInspector::class);
    }
}
