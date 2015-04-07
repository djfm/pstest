<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\Entity\Product;

use PrestaShop\PSTest\Shop\Shop;

use PrestaShop\PSTest\Shop\PageObject\BackOffice\Products\InformationPage;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\Products\PricesPage;

class Products
{
    private $shop;
    private $backOffice;
    private $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->backOffice = $this->shop->get('back-office');
        $this->browser = $shop->getBrowser();
    }

    private function gotoInformationTab()
    {
        $this->browser->click('#link-Informations');
        return new InformationPage($this->shop);

    }

    private function gotoPricesTab()
    {
        $this->browser->click('#link-Prices');
        return new PricesPage($this->shop);

    }

    private function saveProduct()
    {
        $this->browser->clickButtonNamed('submitAddproductAndStay');
        $this->shop->getErrorChecker()->checkStandardFormFeedback();
        return $this;
    }

    public function createProduct(Product $product)
    {
        if (($trg = $product->getTaxRulesGroup()) && !$trg->getId()) {
            $this->backOffice->get('taxes')->createTaxRulesGroup($trg);
        }

        $this->backOffice->visitController('AdminProducts', ['addproduct']);

        $this->gotoInformationTab()
             ->setName($product->getName())
        ;

        $pricesTab = $this->gotoPricesTab()
             ->setPrice($product->getPrice())
        ;

        if (($trg = $product->getTaxRulesGroup())) {
            $pricesTab->setTaxRulesGroupId($trg->getId());
        } else {
            $pricesTab->setTaxRulesGroupId(0);
        }

        $this->saveProduct();

        sleep(10);
    }
}
