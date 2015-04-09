<?php

namespace PrestaShop\PSTest\Shop;

use PrestaShop\PSTest\Shop\Service\BackOffice;

class BackOfficeService
{
    protected $shop;
    protected $backOffice;
    protected $browser;

    public function __construct(BackOffice $backOffice)
    {
        $this->backOffice = $backOffice;
        $this->shop = $backOffice->getShop();
        $this->browser = $this->shop->getBrowser();
    }

    public function get($serviceName)
    {
        return $this->backOffice->get($serviceName);
    }
}
