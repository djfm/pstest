<?php

namespace PrestaShop\PSTest\Shop\PageObject\Installer;

use PrestaShop\Selenium\Browser\BrowserInterface;

class LicensePage
{
    private $browser;

    public function __construct(BrowserInterface $browser)
    {
        $this->browser = $browser;
    }

    public function agreeToTermsAndConditions()
    {
        $this->browser->clickLabelFor('set_license');

        return $this;
    }

    public function nextStep()
    {
        $this->browser->click('#btNext');

        return new StoreInformationPage($this->browser);
    }
}
