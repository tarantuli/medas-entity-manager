<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PurgeAmountShouldBeLessThanTriggerSize extends BaseException
{
    public function __construct(int $purgeAmount, int $triggerSize)
    {
        parent::__construct($purgeAmount, $triggerSize);
    }

    public function pattern(): string
    {
        return 'The purge amount %u should be less than the trigger size %u';
    }
}
