<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

interface RecordCollection extends \Iterator
{
    public function current(): Record;
}
