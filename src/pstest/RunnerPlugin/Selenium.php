<?php

namespace PrestaShop\PSTest\RunnerPlugin;

use PrestaShop\Selenium\SeleniumServerFactory;
use PrestaShop\Selenium\SeleniumBrowserFactory;
use PrestaShop\Selenium\SeleniumBrowserSettings;
use PrestaShop\Selenium\Xvfb\XvfbServerFactory;

use PrestaShop\TestRunner\RunnerPlugin;

class Selenium extends RunnerPlugin
{
    private $server;

    public function setup()
    {
        $ssf = new SeleniumServerFactory;

        $this->server = $ssf->makeServer();
    }

    public function teardown()
    {
        $this->server->shutDown();
    }

    public function getRunnerPluginData()
    {
        return $this->server->getSettings();
    }
}
