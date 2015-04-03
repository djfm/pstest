<?php

namespace PrestaShop\PSTest\Shop;

use Exception;

use PrestaShop\ConfMap\Configuration;

use PrestaShop_IoC_Container as Container;

abstract class Shop
{
    private $container;
    private $defaults;

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

    public function setDefaults(Configuration $defaults)
    {
        $this->defaults = $defaults;
        return $this;
    }

    public function getDefaults($name)
    {
        if (null === $name) {
            return $this->defaults;
        }

        if ($this->defaults) {
            return $this->defaults->get('defaults.' . $name);
        } else {
            return [];
        }
    }

    public function registerServices()
    {
        $this->getContainer()->bind(
            'PrestaShop\PSTest\Shop\Shop',
            function () {
                return $this;
            },
            true // share the shop
        );

        $this->getContainer()->bind(
            'PrestaShop\Selenium\Browser\BrowserInterface',
            function () {
                return $this->getBrowser();
            },
            true // share the browser
        );

        $this->getContainer()->bind(
            'back-office',
            'PrestaShop\PSTest\Shop\Service\BackOffice',
            true
        );
    }

    public function checkStandardErrorFeedback($errorMessage = '')
    {
        if (
            true  !== $this->getBrowser()->hasVisible('div.alert.alert-success') ||
            false !== $this->getBrowser()->hasVisible('div.alert.alert-error')
        ) {
            if (!$errorMessage) {
                $errorMessage = 'The page either reported an error or did not confirm success.';
            }

            throw new Exception($errorMessage);
        }

        return $this;
    }

    abstract public function getBrowser();

    abstract public function getFrontOfficeURL();
    abstract public function getBackOfficeURL();
}
