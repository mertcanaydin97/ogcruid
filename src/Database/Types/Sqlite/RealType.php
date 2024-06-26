<?php

namespace Og\Cruid\Database\Types\Sqlite;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Og\Cruid\Database\Types\Type;

class RealType extends Type
{
    public const NAME = 'real';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'real';
    }
}
