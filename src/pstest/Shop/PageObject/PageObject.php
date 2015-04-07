<?php

namespace PrestaShop\PSTest\Shop\PageObject;

use PrestaShop\PSTest\Shop\Shop;

class PageObject
{
    protected $shop;
    protected $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();
    }
}
