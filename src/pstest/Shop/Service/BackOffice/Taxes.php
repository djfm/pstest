<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use PrestaShop\PSTest\Shop\Shop;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\Taxes\TaxFormPage;

class Taxes
{
    private $shop;
    private $backOffice;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->backOffice = $this->shop->get('back-office');
    }

    public function createTax($name, $rate, $active = true)
    {
        $this->backOffice->visitController('AdminTaxes', ['addtax']);

        $form = new TaxFormPage($this->shop);

        $form
        ->setName($name)
        ->setRate($rate)
        ->setActive($active)
        ->submit();

        

        return $this;
    }
}
