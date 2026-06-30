<?php

declare(strict_types=1);

namespace Medas\EntityManager\Traits;

trait SoftDeletes
{
    public \DateTime|null $deletedAt = null;

    public function softDelete(): void
    {
        $now = new \DateTime();
        $this->deletedAt = $now;

        foreach ($this->softDeleteUniqueFields() as $property) {
            $this->$property .= $now->format('YmdHis');
        }
    }

    /**
     * The text properties that should have the deletion timestamp appended to their value
     * when soft deleting, so that a new entity can reuse the original unique value without
     * colliding with the soft-deleted one.
     */
    protected function softDeleteUniqueFields(): array
    {
        return [];
    }
}
