<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Integer extends BaseType
{
    const UNSIGNED_1_BYTE_MAX = 255;
    const UNSIGNED_2_BYTE_MAX = 65535;
    const UNSIGNED_3_BYTE_MAX = 16777215;
    const UNSIGNED_4_BYTE_MAX = 4294967295;
    const UNSIGNED_8_BYTE_MAX = 18446744073709551615;
    const SIGNED_1_BYTE_MIN = -128;
    const SIGNED_1_BYTE_MAX = 127;
    const SIGNED_2_BYTE_MIN = -32768;
    const SIGNED_2_BYTE_MAX = 32767;
    const SIGNED_3_BYTE_MIN = -8388608;
    const SIGNED_3_BYTE_MAX = 8388607;
    const SIGNED_4_BYTE_MIN = -2147483648;
    const SIGNED_4_BYTE_MAX = 2147483647;
    const SIGNED_8_BYTE_MIN = -9223372036854775808;
    const SIGNED_8_BYTE_MAX = 9223372036854775807;

    public function __construct(
        public int      $minValue = 0,
        public int|null $maxValue = null,
    )
    {
    }
}
