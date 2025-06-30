<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CircularDependencyFound extends BaseException
{
    public function __construct(array $trace, mixed $current)
    {
        $currentInTrace = array_search($current, $trace);
        $currentIndex = array_search($currentInTrace, array_keys($trace));
        $circle = array_merge(array_slice($trace, $currentIndex), [$current]);

        parent::__construct(implode(' » ', $circle));
    }

    public function pattern(): string
    {
        return 'found a circular dependency between entities: %s';
    }
}
