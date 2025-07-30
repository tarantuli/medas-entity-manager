<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

/**
 * The entity manager will not manage class properties tagged with this attribute.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Unmanaged
{
}
