<?php

namespace PrestaShop\PSTest\Shop\PageObject\Installer;

use PrestaShop\PSTest\Shop\Browser\Browser;

class ChooseYourLanguagePage
{
    private $browser;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    public function setLanguage($code)
    {
        $this->browser->select('#langList', $code);

        return $this;
    }

    public function nextStep()
    {
        $this->browser->click('#btNext');

        return new LicensePage($this->browser);
    }
}
