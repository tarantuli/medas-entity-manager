<?php

declare(strict_types=1);

namespace Medas\EntityManager;

class EntityManagerContext
{
    public function __construct(
        public array             $entities = [],
        public array             $initializing = [],
        public int               $entityCount = 0,
        public array             $entitiesToDelete = [],
        public \SplObjectStorage $savedStates = new \SplObjectStorage(),
        public bool              $autoPersistOnCreate = false,
        public bool              $autoFlushOnCreate = false,
        public int|null          $cachePurgeTriggerSize = null,
        public int|null          $cachePurgeAmount = null,
        public array             $entityAccessTime = [],
    )
    {
    }
}
