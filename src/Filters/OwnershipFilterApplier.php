<?php

declare(strict_types=1);

namespace Medas\EntityManager\Filters;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\MetaData;

#[Service]
readonly class OwnershipFilterApplier
{
    public function apply(MetaData $metaData, array $filters): array
    {
        foreach ($metaData->ownershipFilters as $className) {
            $filters = service($className)->addFilters($filters);
        }

        return $filters;
    }
}
