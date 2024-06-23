<?php

namespace Og\Cruid\Database\Types\Postgresql;

use Og\Cruid\Database\Types\Common\VarCharType;

class CharacterVaryingType extends VarCharType
{
    public const NAME = 'character varying';
    public const DBTYPE = 'varchar';
}
