<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\UnitOfWork;

use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class UnitOfWorkExecutor
{
    public function __construct(
        private Database $database,
    )
    {
    }

    public function execute(UnitOfWork $unitOfWork): bool
    {
        $this->database->beginTransaction();

        try {
            foreach ($unitOfWork->updates as $update) {
                $this->database->execute($update);
            }
        }
        catch (\Exception) {
            $this->database->rollbackTransaction();
            return false;
        }

        $this->database->commitTransaction();

        return true;
    }
}
