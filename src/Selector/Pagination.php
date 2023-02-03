<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector;

class Pagination implements Element
{
    public static function c(int $page, int $perPage): static
    {
        return new static($page, $perPage);
    }

    public function __construct(
        public int $page,
        public int $perPage,
    )
    {
    }
}
