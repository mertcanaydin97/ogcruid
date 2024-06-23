<?php

namespace Og\Cruid\Database\Types\Postgresql;

use Og\Cruid\Database\Types\Common\DoubleType;

class DoublePrecisionType extends DoubleType
{
    public const NAME = 'double precision';
    public const DBTYPE = 'float8';
}
