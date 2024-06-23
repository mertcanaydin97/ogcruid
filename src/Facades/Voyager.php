<?php

namespace Og\Cruid\Facades;

use Illuminate\Support\Facades\Facade;

class Cruid extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @method static string image($file, $default = '')
     * @method static $this useModel($name, $object)
     *
     * @see \Og\Cruid\Cruid
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cruid';
    }
}
