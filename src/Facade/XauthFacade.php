<?php

namespace Hilsonxhero\Xauth\Facade;

use Illuminate\Support\Facades\Facade;




/**
 * Class XauthFacade
 * @package Hilsonxhero\Xauth\Facade
 *
 * @method static string store()
 *
 * @see \Hilsonxhero\Xauth\
 */

class XauthFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Xauth';
    }
}
