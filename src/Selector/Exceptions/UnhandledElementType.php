<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\EntityManager\Selector\Element;

class UnhandledElementType extends BaseException
{
    public function __construct(Element $element)
    {
        parent::__construct($element::class);
    }

    public function pattern(): string
    {
        return 'unhandled element type %s';
    }
}
