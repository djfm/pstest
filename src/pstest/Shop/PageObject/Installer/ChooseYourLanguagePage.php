<?php

namespace PrestaShop\PSTest\Shop\PageObject\Installer;

use PrestaShop\Selenium\Browser\BrowserInterface;

class ChooseYourLanguagePage
{
    private $browser;

    public function __construct(BrowserInterface $browser)
    {
        $this->browser = $browser;
    }

    public function nextStep()
    {
        $this->browser->click('#btNext');

        return new LicensePage($this->browser);
    }
}
