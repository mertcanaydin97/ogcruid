<?php

namespace Og\Cruid\Events;

class FileDeleted
{
    public $path;

    public function __construct($path)
    {
        $this->path = $path;
    }
}
