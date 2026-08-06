<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{AsSingleton, BasePackage, Interfaces\ServiceConfigBuilder};
use Medas\Json\JsonPackage;
use Medas\ObjectToArraySerializer\ObjectToArraySerializerPackage;

class EntityManagerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            JsonPackage::instance(),
            ObjectToArraySerializerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function initialize(ServiceConfigBuilder $config): void
    {
        parent::initialize($config);

        $config->addArgumentProcessor(EntityById::class);
    }

    public function hasMarkdownDocumentation(): bool
    {
        return true;
    }
}
