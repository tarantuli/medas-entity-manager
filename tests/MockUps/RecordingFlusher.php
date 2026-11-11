<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{Entities\Flusher, Snapshots\Changes};

// The only Flusher in the test package, so the FlushManager service is given this one. It writes
// nothing, but keeps what each write was handed, so a test can see what a flush() wrote and in how
// many writes. Kept statically, as the service instance is shared by every test: clear() it first.
#[Service]
class RecordingFlusher implements Flusher
{
    /** @var Changes[] */
    private static array $writes = [];

    public static function clear(): void
    {
        self::$writes = [];
    }

    /** @return Changes[] */
    public static function writes(): array
    {
        return self::$writes;
    }

    public function flush(Changes $changes): void
    {
        self::$writes[] = $changes;
    }
}
