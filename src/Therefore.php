<?php
/**
 * Created by PhpStorm.
 * User: SinfCONGf
 * Date: 19/08/2016
 * Time: 14:53
 */

namespace Restoore\Therefore;

class Therefore extends \SoapClient
{

    public function __construct($wsdl, array $options )
    {
        parent::__construct($wsdl, $options);
    }

}