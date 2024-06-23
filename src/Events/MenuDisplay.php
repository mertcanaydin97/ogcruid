<?php

namespace Og\Cruid\Events;

use Illuminate\Queue\SerializesModels;
use Og\Cruid\Models\Menu;

class MenuDisplay
{
    use SerializesModels;

    public $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;

        // @deprecate
        //
        event('cruid.menu.display', $menu);
    }
}
