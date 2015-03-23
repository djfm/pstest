<?php

namespace PrestaShop\Selenium;

use PrestaShop\Selenium\Browser\Browser;

use RemoteWebDriver;

class SeleniumBrowserFactory
{
    private $serverSettings;
    private $browserSettings;

    private $browsers = [];

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

        $browser = new Browser($driver);

        $this->browsers[] = $browser;

        return $browser;
    }

    public function quitLaunchedBrowsers()
    {
        foreach ($this->browsers as $browser) {
            $browser->quit();
        }

        return $this;
    }
}
