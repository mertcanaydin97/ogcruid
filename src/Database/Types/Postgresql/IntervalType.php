<?php

namespace Og\Cruid\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Og\Cruid\Database\Types\Type;

class IntervalType extends Type
{
    public const NAME = 'interval';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'interval';
    }
}
