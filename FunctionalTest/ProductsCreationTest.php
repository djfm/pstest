<?php

namespace PrestaShop\PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\TestCase;

use PrestaShop\PSTest\Shop\Entity\Product;
use PrestaShop\PSTest\Shop\Entity\Country;
use PrestaShop\PSTest\Shop\Entity\Tax;
use PrestaShop\PSTest\Shop\Entity\TaxRule;
use PrestaShop\PSTest\Shop\Entity\TaxRulesGroup;

class ProductsCreationTest extends TestCase
{
    private $backOffice;

    public function cacheInitialState()
    {
        return [
            'installer' => [
                'install' => [[
                    'language' => 'en',
                    'country' => 'us']
                ]]
        ];
    }

    /**
     * @beforeClass
     */
    public function loginToTheBackOffice()
    {
        $this->backOffice = $this->shop->get('back-office')->login();
    }

    public function test_create_product_basic()
    {
        $product = new Product;
        $product->setName('Hello Product')->setPrice(20)->setQuantity(42);
        $product->setTaxRulesGroup((new TaxRulesGroup)->setId(1));

        $this->backOffice->get('products')->createProduct($product);
    }

    public function test_create_product_autosaves_tax_rules_group()
    {
        $country = $this->backOffice->get('localization')->getCountryByISOCode('fr');

        $tax = (new Tax)->setName('Old French Vat')->setRate(19.6);
        $taxRule = (new TaxRule)->setTax($tax)->setCountry($country);
        $trg = new TaxRulesGroup;
        $trg->setName('Old French Vat TRG')->addTaxRule($taxRule);

        $product = new Product;
        $product->setName('Hello Product')->setPrice(20);
        $product->setTaxRulesGroup($trg);

        $this->backOffice->get('products')->createProduct($product);
    }
}
