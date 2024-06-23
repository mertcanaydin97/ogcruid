<?php

namespace Og\Cruid\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Og\Cruid\Database\Types\Type;

class GeometryCollectionType extends Type
{
    public const NAME = 'geometrycollection';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'geometrycollection';
    }
}
