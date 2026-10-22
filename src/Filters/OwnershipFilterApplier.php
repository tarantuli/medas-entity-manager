<?php

declare(strict_types=1);

namespace Medas\EntityManager\Filters;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Interfaces\AddsOwnershipConditions;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\Selector\Conditions\Condition;

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

    /**
     * The selector conditions of the entity's ownership filters that add any.
     *
     * @return Condition[]
     */
    public function conditions(MetaData $metaData): array
    {
        $conditions = [];

        foreach ($metaData->ownershipFilters as $className) {
            $filter = service($className);

            if ($filter instanceof AddsOwnershipConditions) {
                array_push($conditions, ...$filter->addConditions());
            }
        }

        return $conditions;
    }
}
