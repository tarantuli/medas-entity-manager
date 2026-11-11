<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\{EntityManager, EntityManagerContext};
use Medas\EntityManagerTest\FlushTestClass;
use Medas\EntityManagerTest\MockUps\{
    Flush\FlushEntity,
    Flush\FlushLogEntry,
    Flush\ScriptedAfterFlushHandler,
    RecordingFlusher
};

// A write that fails was rolled back, so what it would have written is still to be written: the entities keep the
// state they had before it, and the next flush writes them again. Repository::fetchOrCreate() relies on this when
// it catches a duplicate key (#307).
class FailedWriteTest extends FlushTestClass
{
    public function testWhatAFailedWriteHeldIsWrittenByTheNextFlush(): void
    {
        $context = new EntityManagerContext();
        $entityManager = $this->entityManager($context);
        $stored = $entityManager->get(FlushEntity::class, 1);
        $doomed = $entityManager->get(FlushEntity::class, 2);
        $created = new FlushEntity();

        $created->id = 401;
        $created->name = 'new';
        $stored->name = 'changed';

        $entityManager->persist($created);
        $entityManager->delete($doomed);

        $this->flushFailing($entityManager);

        self::assertSame([], RecordingFlusher::writes());
        self::assertFalse(isset($context->savedStates[$created]));

        $entityManager->flush();

        $writes = RecordingFlusher::writes();

        self::assertCount(1, $writes);
        self::assertSame([$created], $writes[0]->createdEntities());
        self::assertSame([$stored], $writes[0]->updatedEntities());
        self::assertSame([$doomed], $writes[0]->deletedEntities());
    }

    public function testAnAppendOnlyEntryOfAFailedWriteStaysTracked(): void
    {
        $context = new EntityManagerContext();
        $entityManager = $this->entityManager($context);
        $entry = new FlushLogEntry();

        $entry->id = 402;
        $entry->message = 'not written yet';

        $entityManager->persist($entry);

        $this->flushFailing($entityManager);

        self::assertFalse(isset($context->untrackedEntities[$entry]));

        $entityManager->flush();

        self::assertSame([$entry], RecordingFlusher::writes()[0]->createdEntities());
        self::assertTrue(isset($context->untrackedEntities[$entry]));
    }

    private function flushFailing(EntityManager $entityManager): void
    {
        RecordingFlusher::failNextWrite(new \RuntimeException('write rolled back'));

        try {
            $entityManager->flush();

            self::fail('The failing write did not fail the flush');
        }
        catch (\RuntimeException $exception) {
            self::assertSame('write rolled back', $exception->getMessage());
        }
    }

    // The contrast: when the write succeeded and a handler fails after it, the write stands and is not repeated
    public function testAWriteBeforeAFailingHandlerIsNotRepeated(): void
    {
        $entityManager = $this->entityManager();
        $created = new FlushEntity();

        $created->id = 403;
        $created->name = 'new';

        $entityManager->persist($created);

        ScriptedAfterFlushHandler::script(function (): bool {
            throw new \RuntimeException('handler failed');
        });

        try {
            $entityManager->flush();

            self::fail('The failing handler did not fail the flush');
        }
        catch (\RuntimeException $exception) {
            self::assertSame('handler failed', $exception->getMessage());
        }

        ScriptedAfterFlushHandler::script(null);

        $entityManager->flush();

        self::assertCount(1, RecordingFlusher::writes());
    }
}
