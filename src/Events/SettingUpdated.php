<?php

namespace Og\Cruid\Events;

use Illuminate\Queue\SerializesModels;
use Og\Cruid\Models\Setting;

class SettingUpdated
{
    use SerializesModels;

    public $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }
}
