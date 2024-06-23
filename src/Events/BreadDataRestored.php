<?php

namespace Og\Cruid\Events;

use Illuminate\Queue\SerializesModels;
use Og\Cruid\Models\DataType;

class BreadDataRestored
{
    use SerializesModels;

    public $dataType;

    public $data;

    public function __construct(DataType $dataType, $data)
    {
        $this->dataType = $dataType;

        $this->data = $data;

        event(new BreadDataChanged($dataType, $data, 'Restored'));
    }
}
