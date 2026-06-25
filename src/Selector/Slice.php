<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector;

class Slice implements Element
{
    public static function c(int $from, int $count): static
    {
        return new static($from, $count);
    }

    public static function fromPagination(Pagination $pagination): static
    {
        return new static(($pagination->page - 1) * $pagination->perPage, $pagination->perPage);
    }

    public function __construct(
        public int $from,
        public int $count,
    )
    {
    }
}
