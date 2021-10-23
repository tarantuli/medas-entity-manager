<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Types;

use Medas\EntityManager\Attributes\Stored;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DateTime extends Stored
{
    public function __construct(
        public ?\DateTimeImmutable $minValue = null,
        public ?\DateTimeImmutable $maxValue = null,
        public ?\DateTimeZone      $timeZone = null
    )
    {
    }
}
