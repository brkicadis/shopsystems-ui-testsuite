<?php

namespace Helper\Config;

/**
 * Class GenericConfig
 * @package Helper\Config
 */
class GenericConfig
{

    /**
     * @var string
     */
    public $paymentMethodName = '';


    /**
     * @return array
     */
    public function getConfigFileName()
    {
        //process generic "config.json" file to get the name of payment
        //return specific config files for specific payment methods
        return (['foo' => 'bar']);
    }

    /**
     *
     */
    public function processConfigFile()
    {
        //process json file and return it as a map
    }
}