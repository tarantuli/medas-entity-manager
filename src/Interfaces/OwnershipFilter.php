<?php

declare(strict_types=1);

namespace Medas\EntityManager\Interfaces;

interface OwnershipFilter
{
    public function addFilters(array $filters): array;
}
