<?php

namespace PrestaShop\PSTest\Shop\PageObject;

use PrestaShop\PSTest\Shop\Shop;

class BackOfficePageObject extends PageObject
{
    protected $backOffice;

    public function __construct(Shop $shop)
    {
        parent::__construct($shop);
        $this->backOffice = $shop->get('back-office');
    }
}
