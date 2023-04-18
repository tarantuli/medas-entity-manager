<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest;

use Medas\Core\GlobalRepository;
use Medas\EntityManagerTest\Inspectors\EntityManagerInspector;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    protected function entityManager(): EntityManagerInspector
    {
        return GlobalRepository::objectInstantiator()->instantiate(EntityManagerInspector::class);
    }
}
