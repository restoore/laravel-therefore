<?php
/**
 * Created by PhpStorm.
 * User: SinfCONGf
 * Date: 25/08/2016
 * Time: 14:11
 */

namespace Restoore\Therefore\Facades;


use Illuminate\Support\Facades\Facade;

class Therefore extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'therefore'; }
}