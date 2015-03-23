<?php

namespace PrestaShop\PSTest\Shop\PageObject\Installer;

use PrestaShop\Selenium\Browser\BrowserInterface;

class InstallationFinishedPage
{
    private $browser;

    public function __construct(BrowserInterface $browser)
    {
        $this->browser = $browser;
    }
}
