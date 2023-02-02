<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector;

interface Selector
{
    public function definition(): Definition;
}
