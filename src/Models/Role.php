<?php

namespace Og\Cruid\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Og\Cruid\Facades\Cruid;
use Og\Cruid\Tests\Database\Factories\RoleFactory;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        $userModel = Cruid::modelClass('User');

        return $this->belongsToMany($userModel, 'user_roles')
                    ->select(app($userModel)->getTable().'.*')
                    ->union($this->hasMany($userModel))->getQuery();
    }

    public function permissions()
    {
        return $this->belongsToMany(Cruid::modelClass('Permission'));
    }

    protected static function newFactory()
    {
        return RoleFactory::new();
    }
}
