<?php

namespace Og\Cruid\Listeners;

use Og\Cruid\Events\BreadAdded;
use Og\Cruid\Facades\Cruid;

class AddBreadPermission
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Create Permission for a given BREAD.
     *
     * @param BreadAdded $event
     *
     * @return void
     */
    public function handle(BreadAdded $bread)
    {
        if (config('cruid.bread.add_permission') && file_exists(base_path('routes/web.php'))) {
            // Create permission
            //
            // Permission::generateFor(Str::snake($bread->dataType->slug));
            $role = Cruid::model('Role')->where('name', config('cruid.bread.default_role'))->firstOrFail();

            // Get permission for added table
            $permissions = Cruid::model('Permission')->where(['table_name' => $bread->dataType->name])->get()->pluck('id')->all();

            // Assign permission to admin
            $role->permissions()->attach($permissions);
        }
    }
}
