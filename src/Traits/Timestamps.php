<?php

declare(strict_types=1);

namespace Medas\EntityManager\Traits;

use Medas\EntityManager\Attributes\{
    Changes\DontLogChanges,
    IsCreationTimestamp,
    IsModificationTimestmap
};

trait Timestamps
{
    #[IsCreationTimestamp, DontLogChanges]
    private \DateTime $createdAt;

    #[IsModificationTimestmap, DontLogChanges]
    private \DateTime $modifiedAt;

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function modifiedAt(): \DateTime
    {
        return $this->modifiedAt;
    }
}
