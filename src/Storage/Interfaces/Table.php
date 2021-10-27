<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

interface Table
{
    public function getByValues(array $values): RecordCollection;
}
