<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\Fetcher;
use Medas\EntityManager\Entities\IdValues;
use Medas\EntityManager\Repositories\Repository;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RepositoryManager
{
    private array $repositories = [];

    public function __construct(
        private IdValues        $idValues,
        private Fetcher|null    $fetcher,
        private MetaDataManager $metaDataManager,
    )
    {
    }

    public function forClass(string $className): Repository
    {
        if (!array_key_exists($className, $this->repositories)) {
            $this->repositories[$className] = new Repository(
                $this->idValues,
                $this->fetcher,
                $this->metaDataManager->get($className)
            );
        }

        return $this->repositories[$className];
    }
}
