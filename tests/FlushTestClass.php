<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest;

// For tests that look at what a flush() wrote: each starts with no recorded writes, and the
// after-flush handler script it sets never leaks into a later test.
abstract class FlushTestClass extends BaseTestClass
{
    protected function setUp(): void
    {
        MockUps\RecordingFlusher::clear();

        MockUps\Flush\ScriptedAfterFlushHandler::script(null);
    }

    protected function tearDown(): void
    {
        MockUps\Flush\ScriptedAfterFlushHandler::script(null);
    }
}
