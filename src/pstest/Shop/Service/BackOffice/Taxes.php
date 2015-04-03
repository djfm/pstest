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

    /**
     * Create a tax.
     *
     * @param string    $name       The name of the tax
     * @param float     $rate       Tax rate, e.g. 11.00
     * @param bool      $active     Whether the tax should be active or not
     *
     * @return int      the id of the created tax
     */
    public function createTax($name, $rate, $active = true)
    {
        $this->backOffice->visitController('AdminTaxes', ['addtax']);

        $form = new TaxFormPage($this->shop);

        return $form
        ->setName($name)
        ->setRate($rate)
        ->setActive($active)
        ->submit()
        ->getId();
    }
}
