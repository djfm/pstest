<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use PrestaShop\PSTest\Shop\Shop;

class Service
{
    protected $shop;
    protected $backOffice;
    protected $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->backOffice = $this->shop->get('back-office');
        $this->browser = $shop->getBrowser();
    }
}
