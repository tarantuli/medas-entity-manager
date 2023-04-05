<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

class PropertyClass
{
    public function __construct(
        public int $min = 0,
        public int $max = 255,
    )
    {
    }
}
