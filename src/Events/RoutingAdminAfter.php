<?php

namespace Og\Cruid\Events;

use Illuminate\Queue\SerializesModels;

class RoutingAdminAfter
{
    use SerializesModels;

    public $router;

    public function __construct()
    {
        $this->router = app('router');

        // @deprecate
        //
        event('cruid.admin.routing.after', $this->router);
    }
}
