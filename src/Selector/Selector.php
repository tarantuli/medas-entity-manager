<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector;

interface Selector
{
    public function entity(): string;
    public function get(): Definition;
}
