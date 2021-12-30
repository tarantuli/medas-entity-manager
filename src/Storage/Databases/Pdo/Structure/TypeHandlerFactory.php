<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure;

use Medas\EntityManager\Attributes\Interfaces\Type;
use Medas\EntityManager\Storage\Databases\Pdo\Exceptions\UnhandledTypeException;
use Medas\EntityManager\Storage\Databases\Pdo\Structure\TypeHandlers\BinaryHandler;
use Medas\EntityManager\Storage\Databases\Pdo\Structure\TypeHandlers\DateTimeHandler;
use Medas\EntityManager\Storage\Databases\Pdo\Structure\TypeHandlers\IntegerHandler;
use Medas\EntityManager\Storage\Databases\Pdo\Structure\TypeHandlers\TextHandler;
use Medas\EntityManager\Types\Binary;
use Medas\EntityManager\Types\DateTime;
use Medas\EntityManager\Types\Integer;
use Medas\EntityManager\Types\Text;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class TypeHandlerFactory
{
    public function __construct(
        private BinaryHandler   $binaryHandler,
        private DateTimeHandler $dateTimeHandler,
        private IntegerHandler  $integerHandler,
        private TextHandler     $textHandler,
    )
    {
    }

    public function for(Type $type): TypeHandlers\TypeHandler
    {
        return match ($type::class) {
            Binary::class => $this->binaryHandler,
            DateTime::class => $this->dateTimeHandler,
            Integer::class => $this->integerHandler,
            Text::class => $this->textHandler,
            default => throw new UnhandledTypeException($type),
        };
    }
}
