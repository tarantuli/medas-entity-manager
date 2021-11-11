<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\MetaData\Creator;
use Medas\ServiceManager\Attributes\Service;
use Symfony\Contracts\Cache\CacheInterface;

#[Service]
class MetaDataManager
{
    public function __construct(
        private CacheInterface $cache,
        private Creator $creator
    )
    {
    }

    public function get(string $className): MetaData
    {
        $class = new \ReflectionClass($className);
        $key = sha1($className);

        /** @var MetaData $metaData */
        $metaData = $this->cache->get($key, function () use ($className, $class) {
            return $this->creator->create($className, $class);
        });

        if (config('env') === 'dev' && $metaData->sourceFileDate !== filemtime($class->getFileName())) {
            $this->cache->delete($key);
            $metaData = $this->get($className);
        }

        return $metaData;
    }
}
