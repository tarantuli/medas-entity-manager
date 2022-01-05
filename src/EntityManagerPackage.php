<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ServiceManager\BasePackage;

class EntityManagerPackage extends BasePackage
{
    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            ConfigManagerPackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
