<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class RepositoryManager
{
    private array $repositories = [];

    public function __construct(
        private IdValues        $idValues,
        private MetaDataManager $metaDataManager,
    )
    {
    }

    public function forClass(string $className): Repository
    {
        if (!array_key_exists($className, $this->repositories)) {
            $this->repositories[$className] = new Repository(
                $this->idValues,
                $this->metaDataManager->get($className)
            );
        }

        return $this->repositories[$className];
    }
}
