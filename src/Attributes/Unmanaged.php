<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

/**
 * Class properties tagged with this attribute will not be managed by the entity manager.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Unmanaged
{
}
