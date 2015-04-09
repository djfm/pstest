<?php

namespace PrestaShop\PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\PrestaShopTest;

use PrestaShop\PSTest\Shop\Entity\Tax;
use PrestaShop\PSTest\Shop\Entity\TaxRule;
use PrestaShop\PSTest\Shop\Entity\TaxRulesGroup;

class TaxesSetupTest extends PrestaShopTest
{
    private $backOffice;

    private $France;
    private $Germany;

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

    public function test_localization_getCountryByISOCode()
    {
        $this->Germany = $this->backOffice->get('localization')->getCountryByISOCode('de');
        $this->assertEquals(
            1,
            $this->Germany->getId()
        );

        $this->France = $this->backOffice->get('localization')->getCountryByISOCode('fr');
        $this->assertEquals(
            8,
            $this->France->getId()
        );
    }

    public function test_tax_is_created()
    {
        $tax = (new Tax())->setName('hello')->setRate(20)->setEnabled(true);

        $this->backOffice->get('taxes')->saveTax($tax);

        $this->assertInternalType('int', $tax->getId());
        $this->assertGreaterThan(0, $tax->getId());
    }

    public function test_tax_rule_is_created()
    {
        $tax = (new Tax())->setName('hello')->setRate(20)->setEnabled(true);

        $taxRule = new TaxRule;
        $taxRule->setTax($tax)->setBehavior(TaxRule::THIS_TAX_ONLY)->setCountry($this->France);

        $taxRulesGroup = new TaxRulesGroup;
        $taxRulesGroup->setName('Example Tax Rules Group')->setEnabled(true);

        $taxRulesGroup->addTaxRule($taxRule);

        $this->backOffice->get('taxes')->createTaxRulesGroup($taxRulesGroup);
    }
}
