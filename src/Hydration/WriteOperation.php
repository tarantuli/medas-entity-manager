<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

/**
 * Which write a payload is being applied for. Passed to a normalizer so it can
 * honor each writable field's create/update scope (#[IsWritable]'s onCreate /
 * onUpdate), so e.g., a set-once foreign key is accepted on create but ignored
 * on update.
 */
enum WriteOperation
{
    case Create;
    case Update;
}
