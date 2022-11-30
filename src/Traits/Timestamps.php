<?php

declare(strict_types=1);

namespace Medas\EntityManager\Traits;

use Medas\EntityManager\Attributes\IsCreationTimestamp;
use Medas\EntityManager\Attributes\IsModificationTimestmap;
use Medas\EntityManager\Attributes\Property;

trait Timestamps
{
    #[Property, IsCreationTimestamp]
    private \DateTime $createdAt;

    #[Property, IsModificationTimestmap]
    private \DateTime $modifiedAt;
}
