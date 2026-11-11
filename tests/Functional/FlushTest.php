<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\{Exceptions\FlushDoesNotSettle, Snapshots\Changes};
use Medas\EntityManagerTest\FlushTestClass;
use Medas\EntityManagerTest\MockUps\{
    Flush\FlushEntity,
    Flush\ScriptedAfterFlushHandler,
    RecordingFlusher
};

// What flush() writes when its after-flush handlers change entities: those changes are written in
// further rounds of the same flush, until the handlers change nothing more.
class FlushTest extends FlushTestClass
{
    public function testAHandlersChangeToAStoredEntityIsWrittenInTheSameFlush(): void
    {
        $entityManager = $this->entityManager();
        $stored = $entityManager->get(FlushEntity::class, 1);
        $created = $this->newEntity(101);

        $entityManager->persist($created);

        ScriptedAfterFlushHandler::script(function (Changes $changes) use ($created, $stored): bool {
            if (!in_array($created, $changes->createdEntities(), true)) {
                return false;
            }

            $stored->name = 'changed by handler';

            return true;
        });

        $entityManager->flush();

        $writes = RecordingFlusher::writes();

        self::assertCount(2, $writes);
        self::assertSame([$created], $writes[0]->createdEntities());
        self::assertSame([], $writes[0]->updatedEntities());
        self::assertSame([], $writes[1]->createdEntities());
        self::assertSame([$stored], $writes[1]->updatedEntities());
        self::assertSame('changed by handler', $writes[1]->entityChanges($stored)['name']->current);

        // What was written is the new baseline, so the next flush has nothing left to write
        $entityManager->flush();

        self::assertCount(2, RecordingFlusher::writes());
    }

    // A handler that answers false is taken at its word: what it changed anyway waits for the next flush
    public function testAHandlerThatReportsNoChangesEndsTheFlush(): void
    {
        $entityManager = $this->entityManager();
        $stored = $entityManager->get(FlushEntity::class, 2);

        $entityManager->persist($this->newEntity(102));

        ScriptedAfterFlushHandler::script(function () use ($stored): bool {
            $stored->name = 'changed without saying so';

            return false;
        });

        $entityManager->flush();

        self::assertCount(1, RecordingFlusher::writes());

        ScriptedAfterFlushHandler::script(null);

        $entityManager->flush();

        $writes = RecordingFlusher::writes();

        self::assertCount(2, $writes);
        self::assertSame([$stored], $writes[1]->updatedEntities());
    }

    public function testAFlushWhoseHandlersKeepChangingEntitiesGivesUp(): void
    {
        $entityManager = $this->entityManager();
        $stored = $entityManager->get(FlushEntity::class, 3);
        $round = 0;

        $entityManager->persist($this->newEntity(103));

        ScriptedAfterFlushHandler::script(function () use ($stored, &$round): bool {
            $stored->name = 'round ' . ++$round;

            return true;
        });

        try {
            $entityManager->flush();

            self::fail('The flush settled although its handler changed an entity in every round');
        }
        catch (FlushDoesNotSettle $exception) {
            self::assertStringContainsString(FlushEntity::class, $exception->getMessage());
        }

        // It gave up after a bounded number of writes rather than looping on
        self::assertGreaterThan(1, count(RecordingFlusher::writes()));
        self::assertSame(count(RecordingFlusher::writes()), $round);
    }

    private function newEntity(int $id): FlushEntity
    {
        $entity = new FlushEntity();

        $entity->id = $id;
        $entity->name = 'new';

        return $entity;
    }
}
