<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Relations;

enum Action: string
{
    case Cascade = 'cascade';
    case SetNull = 'set null';
    case SetDefault = 'set default';
    case Restrict = 'restrict';
    case NoAction = 'no action';
}
