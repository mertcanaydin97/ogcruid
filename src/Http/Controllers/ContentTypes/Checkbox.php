<?php

namespace Og\Cruid\Http\Controllers\ContentTypes;

class Checkbox extends BaseType
{
    /**
     * @return int
     */
    public function handle()
    {
        return (int) ($this->request->input($this->row->field) == 'on');
    }
}
