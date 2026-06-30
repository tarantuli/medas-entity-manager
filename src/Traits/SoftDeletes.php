<?php

declare(strict_types=1);

namespace Medas\EntityManager\Traits;

trait SoftDeletes
{
    public \DateTime|null $deletedAt = null;

    public function softDelete(): void
    {
        $this->deletedAt = new \DateTime();
    }
}
