<?php

namespace Og\Cruid\Listeners;

use Og\Cruid\Events\BreadDeleted;
use Og\Cruid\Facades\Cruid;

class DeleteBreadMenuItem
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
     * Delete a MenuItem for a given BREAD.
     *
     * @param BreadDeleted $bread
     *
     * @return void
     */
    public function handle(BreadDeleted $bread)
    {
        if (config('cruid.bread.add_menu_item')) {
            $menuItem = Cruid::model('MenuItem')->where('route', 'cruid.'.$bread->dataType->slug.'.index');

            if ($menuItem->exists()) {
                $menuItem->delete();
            }
        }
    }
}
