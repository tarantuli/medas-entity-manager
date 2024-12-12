<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
readonly class EntityValueFetchersManager
{
    private array $fetchers;

    public function __construct(
        private CacheManager      $cacheManager,
        private ImplementorFinder $implementorFinder,
    )
    {
        $classNames = $this->cacheManager->get()->get(__CLASS__, fn() => $this->gather());
        $this->fetchers = array_map(fn(string $name) => service($name), $classNames);
    }

    /** @return string[] */
    private function gather(): array
    {
        return array_map(
            fn(object $service) => $service::class,
            $this->implementorFinder->find(EntityValueFetcher::class)
        );
    }

    /** @return EntityValueFetcher[] */
    public function get(): array
    {
        return $this->fetchers;
    }
}
