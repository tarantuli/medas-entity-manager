<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\EntityManager;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\Lazy\{LazyAuthor, LazyBook, LazyStorage};

// Pins down how a relation behaves as a PHP 8.4 lazy ghost (EntityManager::reference()):
// which accesses load the related entity and which do not. The engine decides most of
// this, so a PHP upgrade that changes it shows up here first.
class LazyReferenceTest extends BaseTestClass
{
    public function testARelationIsAGhostUntilItIsFollowed(): void
    {
        $book = $this->entityManagerService()->get(LazyBook::class, 1);

        self::assertTrue($this->isGhost($book->author));
        self::assertSame(0, LazyStorage::loadsOf(LazyAuthor::class, 1));
    }

    public function testReadingTheIdDoesNotLoad(): void
    {
        $book = $this->entityManagerService()->get(LazyBook::class, 2);

        self::assertSame(2, $book->author->id);
        self::assertSame(2, $book->author->id());
        self::assertTrue($this->isGhost($book->author));
        self::assertSame(0, LazyStorage::loadsOf(LazyAuthor::class, 2));
    }

    public function testReadingAnotherPropertyLoadsOnce(): void
    {
        $book = $this->entityManagerService()->get(LazyBook::class, 3);

        self::assertSame('Edsger', $book->author->name);
        self::assertSame('Edsger', $book->author->name);
        self::assertFalse($this->isGhost($book->author));
        self::assertSame(1, LazyStorage::loadsOf(LazyAuthor::class, 3));
    }

    public function testGetReturnsTheGhostWithoutLoadingIt(): void
    {
        $entityManager = $this->entityManagerService();
        $book = $entityManager->get(LazyBook::class, 4);
        $author = $entityManager->get(LazyAuthor::class, 4);

        self::assertSame($book->author, $author);
        self::assertTrue($this->isGhost($author));
        self::assertSame(0, LazyStorage::loadsOf(LazyAuthor::class, 4));
    }

    // The engine's choice, recorded here: asking whether a property is initialised loads
    // the ghost first, so the answer is about the loaded entity. Code that must not load
    // a relation (the REST normalizers skip uninitialised properties this way) has to
    // ask ReflectionClass::isUninitializedLazyObject() before it asks this.
    public function testAskingWhetherAPropertyIsInitialisedLoads(): void
    {
        $book = $this->entityManagerService()->get(LazyBook::class, 5);
        $nameProperty = new \ReflectionProperty(LazyAuthor::class, 'name');

        self::assertTrue($nameProperty->isInitialized($book->author));
        self::assertFalse($this->isGhost($book->author));
        self::assertSame(1, LazyStorage::loadsOf(LazyAuthor::class, 5));
    }

    public function testFlushDoesNotLoadGhosts(): void
    {
        $entityManager = $this->entityManagerService();
        $book = $entityManager->get(LazyBook::class, 6);

        $entityManager->flush();

        self::assertTrue($this->isGhost($book->author));
        self::assertSame(0, LazyStorage::loadsOf(LazyAuthor::class, 6));
    }

    private function isGhost(object $entity): bool
    {
        return new \ReflectionClass($entity)->isUninitializedLazyObject($entity);
    }

    // A relation to a record that does not exist is not noticed when the relation is
    // loaded, only when a property other than the id is read: the ghost is then
    // initialised, nothing is found, and the property stays uninitialised.
    public function testADanglingRelationFailsOnTheFirstReadOfAProperty(): void
    {
        $book = $this->entityManagerService()->get(LazyBook::class, 7);

        self::assertSame(LazyStorage::DANGLING_AUTHOR_ID, $book->author->id);

        $this->expectException(\Error::class);
        $this->expectExceptionMessageMatches('/must not be accessed before initialization/');

        /** @noinspection PhpExpressionResultUnusedInspection */
        $book->author->name;
    }

    // The same as before lazy loading: get() of an id without a record gives an
    // entity with only its id, not an error.
    public function testGettingAMissingEntityGivesOneWithOnlyItsId(): void
    {
        $author = $this->entityManagerService()->get(
            LazyAuthor::class,
            LazyStorage::MISSING_AUTHOR_ID
        );

        self::assertSame(LazyStorage::MISSING_AUTHOR_ID, $author->id);
        self::assertFalse(new \ReflectionProperty(LazyAuthor::class, 'name')->isInitialized($author));
    }

    // The shared entity manager, the one that answers FindEntity for relations.
    private function entityManagerService(): EntityManager
    {
        return service(EntityManager::class);
    }

    protected function setUp(): void
    {
        $this->entityManagerService()->clear();
    }
}
