<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\MetaData;

interface Fetcher
{
    public function fetchRecord(MetaData $metaData, array $filters);
}
