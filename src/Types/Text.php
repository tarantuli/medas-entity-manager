<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Text extends Binary
{
    public function __construct(
        public int $minLength = 0,
        public int $maxLength = self::MAX_1_BYTE_LENGTH,
    )
    {
        parent::__construct($minLength, $maxLength);
    }
}
