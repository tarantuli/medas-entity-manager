<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\MetaData;

interface OriginalClassFetcher
{
    public function fetch(MetaData $metaData, mixed $id): string;
}
