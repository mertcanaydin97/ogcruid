<?php

namespace Og\Cruid\Models;

use Illuminate\Database\Eloquent\Model;
use Og\Cruid\Events\SettingUpdated;

class Setting extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    public $timestamps = false;

    protected $dispatchesEvents = [
        'updating' => SettingUpdated::class,
    ];
}
