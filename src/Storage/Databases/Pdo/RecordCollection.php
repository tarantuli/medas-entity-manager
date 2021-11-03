<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

class RecordCollection implements \Medas\ServiceManager\Interfaces\Storage\RecordCollection
{
    public function __construct(array $records)
    {
    }

    public function next()
    {
        // TODO: Implement next() method.
    }

    public function key()
    {
        // TODO: Implement key() method.
    }

    public function valid()
    {
        // TODO: Implement valid() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    public function current(): Record
    {
        // TODO: Implement current() method.
    }
}
