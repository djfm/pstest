<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice;

use PrestaShop\PSTest\Shop\Shop;

class AdminLocalizationPage
{
    private $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    public function getDefaultLanguage()
    {
        return $this->shop->getBrowser()->getValue('#PS_LANG_DEFAULT > option[selected]');
    }
}
