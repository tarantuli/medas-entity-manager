<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\MetaData\Compiler;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Interfaces\Cache;

#[Service]
class MetaDataManager
{
    public function __construct(
        private Cache                 $cache,
        private Compiler              $compiler,
        private PropertyAccessManager $propertyAccessManager,
    )
    {
    }

    public function get(string $className): MetaData
    {
        $class = new \ReflectionClass($className);
        $key = [static::class, $className];

        /** @var MetaData $metaData */
        $metaData = $this->cache->get($key, function () use ($className, $class) {
            return $this->compiler->compile($className, $class);
        });

        if (config('env') === 'dev' && $metaData->sourceFileDate !== filemtime($class->getFileName())) {
            $this->cache->remove($key);
            $metaData = $this->get($className);
        }

        $this->propertyAccessManager->makeAccessible($metaData);

        return $metaData;
    }
}
