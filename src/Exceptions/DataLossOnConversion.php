<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class DataLossOnConversion extends BaseException
{
    public function __construct(string $name, $value, $intValue)
    {
        parent::__construct($name, $value, $intValue);
    }

    public function pattern(): string
    {
        return 'Data loss on conversion to integer for %s: %s => %s';
    }
}
