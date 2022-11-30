<?php

declare(strict_types=1);

namespace Medas\EntityManager\Traits;

use Medas\EntityManager\Attributes\{IsCreationTimestamp, IsModificationTimestmap, Property};

trait Timestamps
{
    #[Property, IsCreationTimestamp]
    private \DateTime $createdAt;

    #[Property, IsModificationTimestmap]
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
