<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class Value implements Operant
{
    public static function c(mixed $value, bool $isTrusted = false): static
    {
        return new static($value, $isTrusted);
    }

    public function __construct(
        public mixed $value,
        public bool  $isTrusted = false,
    )
    {
    }
}
