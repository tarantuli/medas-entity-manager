<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Interfaces;

interface Action
{
    public function execute(): void;

    public function storage(): Storage;

    public function onComplete(): ?\Closure;
}
