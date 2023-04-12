<?php

declare(strict_types=1);

namespace Medas\EntityManager\Properties;

use Medas\Core\Interfaces\Type;

interface Handler
{
    public function type(): Type;

    public function serialize(mixed $value): mixed;

    public function unserialize(mixed $value): mixed;
}
