<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class AfterFlushCallbacksDoNotSettle extends BaseException
{
    /** @param string[] $keys */
    public function __construct(int $rounds, array $keys)
    {
        parent::__construct($rounds, implode(', ', $keys));
    }

    public function pattern(): string
    {
        return 'after-flush callbacks did not settle after %s rounds: they keep asking for more, last of all %s';
    }
}
