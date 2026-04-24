<?php

declare(strict_types=1);

namespace Medas\EntityManager\Traits;

use Medas\EntityManager\Attributes\{
    Changes\DontLogChanges,
    IsCreationTimestamp,
    IsModificationTimestamp
};

trait Timestamps
{
    #[IsCreationTimestamp, DontLogChanges]
    public \DateTime $createdAt;

    #[IsModificationTimestamp, DontLogChanges]
    public \DateTime $modifiedAt;

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function modifiedAt(): \DateTime
    {
        return $this->modifiedAt;
    }
}
