<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DateTime extends BaseType
{
    public function __construct(
        public \DateTimeInterface|null $minValue = null,
        public \DateTimeInterface|null $maxValue = null,
        public \DateTimeZone|null      $timeZone = null,
    )
    {
    }
}
