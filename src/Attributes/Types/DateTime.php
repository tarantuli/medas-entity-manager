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

    /** @param \DateTime|null $value */
    public function serialize(mixed $value): string|null
    {
        return $value instanceof \DateTime ? $value->format(DATE_ISO8601) : $value;
    }
}
