<?php

namespace Cann\Admin\OAuth\Facades;

use Illuminate\Support\Facades\Facade;

class GoldenAdmin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Cann\Admin\OAuth\GoldenAdmin::class;
    }
}
