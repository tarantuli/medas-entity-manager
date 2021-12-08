<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Types;

use Medas\EntityManager\Attributes\BaseType;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DateTime extends BaseType
{
    public function __construct(
        public ?\DateTimeInterface $minValue = null,
        public ?\DateTimeInterface $maxValue = null,
        public ?\DateTimeZone      $timeZone = null
    )
    {
    }

    public function deserialize(mixed $value): \DateTime|null
    {
        return $value === null ? null : new \DateTime($value);
    }
}
