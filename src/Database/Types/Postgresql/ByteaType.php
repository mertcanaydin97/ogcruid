<?php

namespace Og\Cruid\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Og\Cruid\Database\Types\Type;

class ByteaType extends Type
{
    public const NAME = 'bytea';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'bytea';
    }
}
