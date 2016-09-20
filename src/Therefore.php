<?php

namespace Restoore\Therefore;

class Therefore extends \SoapClient
{

    public function __construct($wsdl, array $options )
    {
        parent::__construct($wsdl, $options);
    }

}