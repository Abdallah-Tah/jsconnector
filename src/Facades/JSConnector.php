<?php

namespace Amohamed\JSConnector\Facades;

use Illuminate\Support\Facades\Facade;

class JSConnector extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'JSConnector';
    }
}