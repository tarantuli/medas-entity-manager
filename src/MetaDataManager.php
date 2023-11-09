<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager};

#[Service]
readonly class MetaDataManager
{
    public function __construct(
        private CacheManager          $cacheManager,
        private MetaData\Compiler     $compiler,
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
