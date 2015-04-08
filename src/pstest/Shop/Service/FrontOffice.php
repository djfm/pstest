<?php

namespace PrestaShop\PSTest\Shop\Service;

use Exception;

use PrestaShop\PSTest\Shop\Shop;

class FrontOffice
{
    private $shop;
    private $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();
    }

    public function visitMyAccount()
    {
        
    }
}
