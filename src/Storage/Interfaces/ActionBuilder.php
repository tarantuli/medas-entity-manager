<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

interface ActionBuilder
{
    public function create(Store $store, array $values): Action;

    public function update(Store $store, array $updates, array $conditions): Action;

    /** @param Store[] $stores */
    public function select(array $stores, array $filters): Action;
}
