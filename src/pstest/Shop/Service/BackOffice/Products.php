<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\BackOfficeService;
use PrestaShop\PSTest\Shop\Entity\Product;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\Products\InformationPage;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\Products\PricesPage;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\Products\QuantitiesPage;

class Products extends BackOfficeService
{
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

    private function gotoQuantitiesTab()
    {
        $this->browser->click('#link-Quantities');
        return new QuantitiesPage($this->shop);
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

        if (($q =$product->getQuantity())) {
            $this->gotoQuantitiesTab()->setQuantity($q);
            $storedQuantity = $this->saveProduct()->gotoQuantitiesTab()->getQuantity();
            if ($storedQuantity != $q) {
                throw new Exception('Product was not saved correctly, quantity is not valid.');
            }
        }

        $product->setFrontOfficeURL($this->browser->getAttribute('#page-header-desc-product-preview', 'href'));

        return $this;
    }
}
