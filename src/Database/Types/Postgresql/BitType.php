<?php

namespace Og\Cruid\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Og\Cruid\Database\Types\Type;

class BitType extends Type
{
    public const NAME = 'bit';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        $length = empty($field['length']) ? 1 : $field['length'];

        return "bit({$length})";
    }
}
