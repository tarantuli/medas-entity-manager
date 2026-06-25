<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector;

class Slice implements Element
{
    public static function c(int $count, int $from = 0): static
    {
        return new static($count, $from);
    }

    public static function fromPagination(Pagination $pagination): static
    {
        return new static($pagination->perPage, ($pagination->page - 1) * $pagination->perPage);
    }

    public function __construct(
        public int $count,
        public int $from = 0,
    )
    {
    }
}
