<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Types;

use Medas\EntityManager\Attributes\Stored;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Binary extends Stored
{
    const MAX_1_BYTE_LENGTH = 255;
    const MAX_2_BYTE_LENGTH = 65535;
    const MAX_3_BYTE_LENGTH = 16777215;
    const MAX_4_BYTE_LENGTH = 4294967295;

    public function __construct(
        public int $minLength = 0,
        public int $maxLength = self::MAX_1_BYTE_LENGTH
    )
    {
    }
}
