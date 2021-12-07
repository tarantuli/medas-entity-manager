<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Types;

use Medas\EntityManager\Attributes\Stored;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DateTime extends Stored
{
    public function __construct(
        public ?\DateTimeInterface $minValue = null,
        public ?\DateTimeInterface $maxValue = null,
        public ?\DateTimeZone      $timeZone = null
    )
    {
    }

    public function deserialize(mixed $value): \DateTime
    {
        return new \DateTime($value);
    }
}
