<?php

namespace Og\Cruid\Database\Types\Postgresql;

use Og\Cruid\Database\Types\Common\CharType;

class CharacterType extends CharType
{
    public const NAME = 'character';
    public const DBTYPE = 'bpchar';
}
