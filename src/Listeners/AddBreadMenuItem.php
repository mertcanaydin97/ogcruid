<?php

namespace Og\Cruid\Listeners;

use Og\Cruid\Events\BreadAdded;
use Og\Cruid\Facades\Cruid;

class AddBreadMenuItem
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
     * Create a MenuItem for a given BREAD.
     *
     * @param BreadAdded $event
     *
     * @return void
     */
    public function handle(BreadAdded $bread)
    {
        if (config('cruid.bread.add_menu_item') && file_exists(base_path('routes/web.php'))) {
            $menu = Cruid::model('Menu')->where('name', config('cruid.bread.default_menu'))->firstOrFail();

            $menuItem = Cruid::model('MenuItem')->firstOrNew([
                'menu_id' => $menu->id,
                'title'   => $bread->dataType->getTranslatedAttribute('display_name_plural'),
                'url'     => '',
                'route'   => 'cruid.'.$bread->dataType->slug.'.index',
            ]);

            $order = Cruid::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target'     => '_self',
                    'icon_class' => $bread->dataType->icon,
                    'color'      => null,
                    'parent_id'  => null,
                    'order'      => $order,
                ])->save();
            }
        }
    }
}
