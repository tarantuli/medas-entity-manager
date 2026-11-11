<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\EntityManagerContext;
use Medas\EntityManagerTest\FlushTestClass;
use Medas\EntityManagerTest\MockUps\{Flush\FlushEntity, Flush\FlushLogEntry, RecordingFlusher};

// An #[AppendOnly] entity is written once: after its insert, and when it is fetched, flush() no longer diffs it.
class AppendOnlyTest extends FlushTestClass
{
    public function testAnInsertedEntryIsNoLongerTracked(): void
    {
        $context = new EntityManagerContext();
        $entityManager = $this->entityManager($context);
        $entry = new FlushLogEntry();

        $entry->id = 301;
        $entry->message = 'written once';

        $entityManager->persist($entry);
        $entityManager->flush();

        self::assertSame([$entry], RecordingFlusher::writes()[0]->createdEntities());
        self::assertTrue(isset($context->untrackedEntities[$entry]));
        self::assertFalse(isset($context->savedStates[$entry]));

        // Neither written as an update nor inserted a second time
        $entry->message = 'changed afterwards';

        $entityManager->flush();

        self::assertCount(1, RecordingFlusher::writes());
    }

    public function testAFetchedEntryIsNotTracked(): void
    {
        $context = new EntityManagerContext();
        $entityManager = $this->entityManager($context);
        $entry = $entityManager->get(FlushLogEntry::class, 302);

        self::assertTrue(isset($context->untrackedEntities[$entry]));
        self::assertFalse(isset($context->savedStates[$entry]));
        self::assertSame($entry, $entityManager->get(FlushLogEntry::class, 302));

        $entry->message = 'changed after the fetch';

        $entityManager->flush();

        self::assertSame([], RecordingFlusher::writes());
    }

    public function testAnEntryCanStillBeDeleted(): void
    {
        $entityManager = $this->entityManager();
        $entry = $entityManager->get(FlushLogEntry::class, 303);

        $entityManager->delete($entry);
        $entityManager->flush();

        self::assertSame([$entry], RecordingFlusher::writes()[0]->deletedEntities());
    }

    // The contrast: an ordinary entity stays tracked after its insert, so a later change is written
    public function testAnOrdinaryEntityStaysTrackedAfterItsInsert(): void
    {
        $context = new EntityManagerContext();
        $entityManager = $this->entityManager($context);
        $entity = new FlushEntity();

        $entity->id = 304;
        $entity->name = 'inserted';

        $entityManager->persist($entity);
        $entityManager->flush();

        self::assertFalse(isset($context->untrackedEntities[$entity]));

        $entity->name = 'changed afterwards';

        $entityManager->flush();

        $writes = RecordingFlusher::writes();

        self::assertCount(2, $writes);
        self::assertSame([$entity], $writes[1]->updatedEntities());
    }
}
