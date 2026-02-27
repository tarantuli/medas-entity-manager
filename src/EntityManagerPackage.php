<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{AsSingleton, BasePackage};
use Medas\ObjectToArraySerializer\ObjectToArraySerializerPackage;

class EntityManagerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            ObjectToArraySerializerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function hasMarkdownDocumentation(): bool
    {
        return true;
    }
}
