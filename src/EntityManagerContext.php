<?php

declare(strict_types=1);

namespace Medas\EntityManager;

class EntityManagerContext
{
    public function __construct(
        public array    $entities = [],
        public array    $initializing = [],
        public int      $entityCount = 0,
        public array    $entitiesToDelete = [],
        public \WeakMap $savedStates = new \WeakMap(),

        /**
         * Entities fetched via EntityManager::get(trackChanges: false) -- present in $entities
         * for identity-map caching, but must never reach ChangeFinder::gather(): having no saved
         * state would otherwise make gatherChanges() treat them as brand new and re-insert them.
         * Used as a weak set (value is always true); see EntityManager::trackableEntities().
         *
         * @var \WeakMap<object, true>
         */
        public \WeakMap $untrackedEntities = new \WeakMap(),
        public bool     $autoPersistOnCreate = false,
        public bool     $autoFlushOnCreate = false,
        public int|null $cachePurgeTriggerSize = null,
        public int|null $cachePurgeAmount = null,
        public array    $entityAccessTime = [],

        /**
         * Lazy ghosts in $entities that haven't been loaded yet (see EntityManager::reference()). They can't have
         * changed - any write loads them first - so flushing skips them, which also keeps it from loading them.
         * Used as a weak set (value is always true).
         *
         * @var \WeakMap<object, true>
         */
        public \WeakMap $unloadedEntities = new \WeakMap(),
    )
    {
    }
}
