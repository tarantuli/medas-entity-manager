<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Lazy;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\ValueFetchers\{EntityValueFetcher, FetchResult};
use Medas\EntityManager\MetaData;

// Serves the records of the lazy-loading tests and counts how often each entity is
// loaded. Both entities have one property besides the id, so one fetch is one load.
// An id without a record gets no values, as from a store where the row is missing.
#[Service]
class LazyStorage implements EntityValueFetcher
{
    public const int DANGLING_AUTHOR_ID = 99;
    public const int MISSING_AUTHOR_ID = 98;

    private const array RECORDS = [
        LazyBook::class => [
            1 => ['author' => 1],
            2 => ['author' => 2],
            3 => ['author' => 3],
            4 => ['author' => 4],
            5 => ['author' => 5],
            6 => ['author' => 6],
            7 => ['author' => self::DANGLING_AUTHOR_ID],
        ],
        LazyAuthor::class => [
            1 => ['name' => 'Ada'],
            2 => ['name' => 'Grace'],
            3 => ['name' => 'Edsger'],
            4 => ['name' => 'Barbara'],
            5 => ['name' => 'Donald'],
            6 => ['name' => 'Niklaus'],
        ],
    ];

    /** @var array<string, int> */
    private static array $loads = [];

    public static function loadsOf(string $className, int $id): int
    {
        return self::$loads[$className . ':' . $id] ?? 0;
    }

    public function fetch(MetaData $metaData, object $entity, MetaData\Property $property): FetchResult
    {
        if (!array_key_exists($entity::class, self::RECORDS)) {
            return new FetchResult(false);
        }

        $key = $entity::class . ':' . $entity->id;
        self::$loads[$key] = (self::$loads[$key] ?? 0) + 1;
        $record = self::RECORDS[$entity::class][$entity->id] ?? null;

        if ($record === null || !array_key_exists($property->name, $record)) {
            return new FetchResult(false);
        }

        return new FetchResult(true, $record[$property->name]);
    }
}
