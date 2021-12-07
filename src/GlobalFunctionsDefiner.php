<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class GlobalFunctionsDefiner
{
    public function __construct()
    {
        require_once __DIR__ . '/GlobalFunctions.php';
    }
}
