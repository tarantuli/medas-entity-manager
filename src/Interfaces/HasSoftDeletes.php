<?php

declare(strict_types=1);

namespace Medas\EntityManager\Interfaces;

interface HasSoftDeletes
{
    public function softDelete(): void;
}
