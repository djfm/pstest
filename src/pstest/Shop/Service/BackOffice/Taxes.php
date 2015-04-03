<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\Entity\Tax;
use PrestaShop\PSTest\Shop\Entity\TaxRule;
use PrestaShop\PSTest\Shop\Entity\TaxRulesGroup;

use PrestaShop\PSTest\Shop\Shop;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\Taxes\TaxFormPage;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\Taxes\TaxRulesGroupFormPage;
use PrestaShop\PSTest\Shop\PageObject\BackOffice\Taxes\TaxRuleFormPage;

class Taxes
{
    private $shop;
    private $backOffice;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->backOffice = $this->shop->get('back-office');
    }

    public function saveTax(Tax $tax)
    {
        $this->backOffice->visitController('AdminTaxes', ['addtax']);

        $form = new TaxFormPage($this->shop);

        $id = $form
        ->setName($tax->getName())
        ->setRate($tax->getRate())
        ->setEnabled($tax->getEnabled())
        ->submit()
        ->getId();

        $tax->setId($id);

        return $this;
    }

    public function createTaxRulesGroup(TaxRulesGroup $trg)
    {
        // Save all taxes that have no id
        foreach ($trg->getTaxRules() as $taxRule) {
            if (!$taxRule->getTax()->getId()) {
                $this->saveTax($taxRule->getTax());
            }

            if ($taxRule->getCountry() && !$taxRule->getCountry()->getId()) {
                throw new Exception('Country of TaxRule has no id, will not be able to save TaxRule.');
            }
        }

        $this->backOffice->visitController('AdminTaxRulesGroup', ['addtax_rules_group']);

        $trgForm = new TaxRulesGroupFormPage($this->shop);

        $id = $trgForm->setName($trg->getName())->setEnabled($trg->getEnabled())->submit()->getId();
        $trg->setId($id);

        $trgForm->addNewTaxRule();

        foreach ($trg->getTaxRules() as $taxRule) {
            $taxRuleForm = new TaxRuleFormPage($this->shop);
            $taxRuleForm->setBehavior($taxRule->getBehavior());
            $taxRuleForm->setTaxId($taxRule->getTax()->getId());
            if ($taxRule->getCountry()) {
                $taxRuleForm->setCountryId($taxRule->getCountry()->getId());
            }
            $taxRuleForm->setDescription($taxRule->getDescription());
            $taxRuleForm->submit();
        }

        return $this;
    }
}
