<?php

namespace PrestaShop\Selenium;

use PrestaShop\Selenium\Browser\Browser;

use RemoteWebDriver;

class SeleniumBrowserFactory
{
    private $serverSettings;
    private $browserSettings;

    public function __construct(SeleniumServerSettings $serverSettings, SeleniumBrowserSettings $browserSettings)
    {
        $this->serverSettings = $serverSettings;
        $this->browserSettings = $browserSettings;
    }

    public function makeBrowser()
    {
        $driver = RemoteWebDriver::create(
            $this->serverSettings->getURL(),
            $this->browserSettings->toArray()
        );
        return new Browser($driver);
    }
}
