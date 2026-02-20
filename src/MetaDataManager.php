<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;

#[Service]
readonly class MetaDataManager
{
    public function __construct(
        private MetaData\Compiler $compiler,
    )
    {
    }

    public function get(string $className): MetaData
    {
        return cache([static::class, $className], function () use ($className) {
            return $this->compiler->compile($className);
        });
    }
}
