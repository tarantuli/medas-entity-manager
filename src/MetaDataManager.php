<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\MetaData\Compiler;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Cache\CacheManager;

#[Service]
class MetaDataManager
{
    public function __construct(
        private CacheManager          $cacheManager,
        private Compiler              $compiler,
        private PropertyAccessManager $propertyAccessManager,
    )
    {
    }

    public function get(string $className): MetaData
    {
        $metaData = $this->cacheManager->get()->get([static::class, $className], function () use ($className) {
            return $this->compiler->compile($className);
        });

        $this->propertyAccessManager->makeAccessible($metaData);

        return $metaData;
    }
}
