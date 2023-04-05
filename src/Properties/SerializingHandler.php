<?php

declare(strict_types=1);

namespace Medas\EntityManager\Properties;

use Medas\EntityManager\Types\Binary;
use Medas\EntityManager\Types\Text;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class SerializingHandler implements Handler
{
    private Text $type;

    public function __construct()
    {
        $this->type = new Text(maxLength: Binary::MAX_2_BYTE_LENGTH);
    }

    public function type(): Text
    {
        return $this->type;
    }

    public function serialize(mixed $value): string
    {
        return serialize($value);
    }

    public function unserialize(mixed $value): mixed
    {
        return unserialize($value);
    }
}
