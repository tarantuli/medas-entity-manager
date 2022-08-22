<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Collections\Collection;

class ReferenceCollection implements Collection
{
    private int $index;
    private array $data;

    public function __construct(
        private readonly \Closure $fetcher,
    )
    {
    }

    public function data(): array
    {
        if (!isset($this->data)) {
            $this->data = ($this->fetcher)();
        }

        return $this->data;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data());
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data()[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data()[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data()[$offset]);
    }

    public function current(): mixed
    {
        return $this->data()[$this->index];
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return array_key_exists($this->index, $this->data());
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}
