<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\MetaData;

interface Fetcher
{
    public function fetch(MetaData\Property $property): mixed;
}
