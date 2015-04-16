<?php

namespace PrestaShop\PSTest\Shop\PageObject\Installer;

use PrestaShop\PSTest\Shop\Browser\Browser;

class InstallationFinishedPage
{
    private $browser;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }
}
