<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;

#[Service]
readonly class MetaDataManager
{
    public function __construct(
        private MetaData\Compiler     $compiler,
        private PropertyAccessManager $propertyAccessManager,
    )
    {
    }

    public function get(string $className): MetaData
    {
        $metaData = cache([static::class, $className], function () use ($className) {
            return $this->compiler->compile($className);
        });

        $this->propertyAccessManager->makeAccessible($metaData);

        return $metaData;
    }
}
