<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Flush;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{Entities\AfterFlushHandler, Snapshots\Changes};

// An after-flush handler that does what the running test scripts, and nothing without a script.
// Every after-flush handler in the package takes part in every flush of every test, so a test
// that sets a script must take it away again: see FlushTestClass.
#[Service]
class ScriptedAfterFlushHandler implements AfterFlushHandler
{
    /** @var (\Closure(Changes): bool)|null */
    private static \Closure|null $script = null;

    /** @param (\Closure(Changes): bool)|null $script */
    public static function script(\Closure|null $script): void
    {
        self::$script = $script;
    }

    public function handle(Changes $changes): bool
    {
        return self::$script === null ? false : (self::$script)($changes);
    }
}
