<?php

namespace Hilsonxhero\ElasticVision\Facade;

use Illuminate\Support\Facades\Facade;




/**
 * Class ElasticVisionFacade
 * @package Hilsonxhero\ElasticVision\Facade
 *
 * @method static string store()
 *
 * @see \Hilsonxhero\ElasticVision\
 */

class ElasticVisionFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ElasticVision';
    }
}
