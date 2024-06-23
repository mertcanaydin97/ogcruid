<?php

namespace Og\Cruid\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Og\Cruid\Contracts\User as UserContract;
use Og\Cruid\Tests\Database\Factories\UserFactory;
use Og\Cruid\Traits\CruidUser;

class User extends Authenticatable implements UserContract
{
    use CruidUser, HasFactory;

    protected $guarded = [];

    public $additional_attributes = ['locale'];

    public function getAvatarAttribute($value)
    {
        return $value ?? config('cruid.user.default_avatar', 'users/default.png');
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = $value ? $value->toJson() : json_encode([]);
    }

    public function getSettingsAttribute($value)
    {
        return collect(json_decode((string)$value));
    }

    public function setLocaleAttribute($value)
    {
        $this->settings = $this->settings->merge(['locale' => $value]);
    }

    public function getLocaleAttribute()
    {
        return $this->settings->get('locale');
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
