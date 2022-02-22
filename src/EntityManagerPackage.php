<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Cache\CachePackage;
use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ServiceManager\AsSingleton;
use Medas\ServiceManager\BasePackage;

class EntityManagerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            ConfigManagerPackage::class,
            CachePackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
