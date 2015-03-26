<?php

namespace PrestaShop\PSTest\Shop;

use Exception;

use PrestaShop_IoC_Container as Container;

abstract class Shop
{
    private $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function get($serviceName)
    {
        return $this->getContainer()->make($serviceName);
    }
}
