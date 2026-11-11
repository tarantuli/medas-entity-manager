<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\{
    EntityManagerContext,
    Exceptions\AfterFlushCallbacksDoNotSettle,
    Snapshots\Changes
};
use Medas\EntityManagerTest\FlushTestClass;
use Medas\EntityManagerTest\MockUps\{
    Flush\FlushEntity,
    Flush\ScriptedAfterFlushHandler,
    RecordingFlusher
};

// EntityManager::afterFlush(): work that runs once the outermost flush has written everything, and
// whose own changes that same flush writes.
class AfterFlushTest extends FlushTestClass
{
    public function testACallbackRunsOnceTheFlushHasWrittenEverything(): void
    {
        $entityManager = $this->entityManager();
        $writesSeen = [];

        $entityManager->persist($this->newEntity(201));
        $entityManager->afterFlush(function () use (&$writesSeen): void {
            $writesSeen[] = count(RecordingFlusher::writes());
        });

        // Registered outside a flush, it waits for the next one
        self::assertSame([], $writesSeen);

        $entityManager->flush();

        self::assertSame([1], $writesSeen);

        // Run means gone: a later flush does not run it again
        $entityManager->flush();

        self::assertSame([1], $writesSeen);
    }

    public function testAFlushInsideAFlushLeavesTheCallbacksToTheOutermostOne(): void
    {
        $entityManager = $this->entityManager();
        $stored = $entityManager->get(FlushEntity::class, 1);
        $created = $this->newEntity(202);
        $log = [];

        $entityManager->persist($created);
        $entityManager->afterFlush(function () use (&$log): void {
            $log[] = 'callback';
        });

        ScriptedAfterFlushHandler::script(
            function (Changes $changes) use ($created, $entityManager, $stored, &$log)
            : bool {
            if (!in_array($created, $changes->createdEntities(), true)) {
                return false;
            }

            $stored->name = 'written by a nested flush';

            $entityManager->flush();

            $log[] = 'nested flush';

            return false;
        });

        $entityManager->flush();

        self::assertSame(['nested flush', 'callback'], $log);
        self::assertSame([$stored], RecordingFlusher::writes()[1]->updatedEntities());
    }

    public function testAKeyGivenTwiceRunsTheFirstCallbackOnly(): void
    {
        $entityManager = $this->entityManager();
        $ran = [];

        $entityManager->afterFlush(function () use (&$ran): void {
            $ran[] = 'first';
        }, 'same key');

        $entityManager->afterFlush(function () use (&$ran): void {
            $ran[] = 'second';
        }, 'same key');

        // Without a key every callback is one of its own
        $entityManager->afterFlush(function () use (&$ran): void {
            $ran[] = 'unkeyed one';
        });

        $entityManager->afterFlush(function () use (&$ran): void {
            $ran[] = 'unkeyed two';
        });

        $entityManager->flush();

        self::assertSame(['first', 'unkeyed one', 'unkeyed two'], $ran);
    }

    public function testACallbackRegisteredByACallbackRunsInTheNextRound(): void
    {
        $entityManager = $this->entityManager();
        $stored = $entityManager->get(FlushEntity::class, 2);
        $log = [];

        $entityManager->afterFlush(function () use ($entityManager, $stored, &$log): void {
            $stored->name = 'changed by a callback';

            $entityManager->afterFlush(function () use (&$log): void {
                $log[] = 'second, after ' . count(RecordingFlusher::writes()) . ' write';
            });

            $log[] = 'first';
        });

        $entityManager->flush();

        self::assertSame(['first', 'second, after 1 write'], $log);

        // What the callback changed was written by the same flush
        $writes = RecordingFlusher::writes();

        self::assertCount(1, $writes);
        self::assertSame([$stored], $writes[0]->updatedEntities());
    }

    public function testCallbacksThatKeepAskingForMoreGiveUp(): void
    {
        $entityManager = $this->entityManager();
        $again = function () use ($entityManager, &$again): void {
            $entityManager->afterFlush($again, 'asks again');
        };

        $entityManager->afterFlush($again, 'asks again');

        $this->expectException(AfterFlushCallbacksDoNotSettle::class);
        $this->expectExceptionMessageMatches('/asks again/');

        $entityManager->flush();
    }

    public function testTheCallbacksOfAFailedFlushAreDropped(): void
    {
        $context = new EntityManagerContext();
        $entityManager = $this->entityManager($context);
        $ran = false;

        $entityManager->persist($this->newEntity(203));
        $entityManager->afterFlush(function () use (&$ran): void {
            $ran = true;
        });

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

        self::assertFalse($ran);
        self::assertSame([], $context->afterFlushCallbacks);
        self::assertFalse($context->flushing);

        ScriptedAfterFlushHandler::script(null);

        $entityManager->persist($this->newEntity(204));
        $entityManager->flush();

        self::assertFalse($ran);
    }

    private function newEntity(int $id): FlushEntity
    {
        $entity = new FlushEntity();

        $entity->id = $id;
        $entity->name = 'new';

        return $entity;
    }
}
