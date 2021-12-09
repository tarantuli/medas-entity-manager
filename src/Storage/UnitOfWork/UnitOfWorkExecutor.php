<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\UnitOfWork;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class UnitOfWorkExecutor
{
    public function execute(UnitOfWork $unitOfWork): bool
    {
        foreach ($unitOfWork->databases as $database) {
            $database->beginTransaction();
        }

        try {
            foreach ($unitOfWork->creates as $create) {
                $create->database->execute($create);
            }

            foreach ($unitOfWork->updates as $update) {
                $update->database->execute($update);
            }
        }
        catch (\Exception) {
            foreach ($unitOfWork->databases as $database) {
                $database->rollbackTransaction();
            }
            return false;
        }

        foreach ($unitOfWork->databases as $database) {
            $database->commitTransaction();
        }

        return true;
    }
}
