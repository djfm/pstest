<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\BackOfficeService;

use PrestaShop\PSTest\Shop\Entity\Tax;
use PrestaShop\PSTest\Shop\Entity\TaxRule;
use PrestaShop\PSTest\Shop\Entity\TaxRulesGroup;

use PrestaShop\PSTest\Shop\Shop;

class Taxes extends BackOfficeService
{
    public function saveTax(Tax $tax)
    {
        $this->backOffice->visitController('AdminTaxes', ['addtax']);

        $form = $this->get('PageObject:BackOffice\Taxes\TaxFormPage');

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

        $trgForm = $this->get('PageObject:BackOffice\Taxes\TaxRulesGroupFormPage');

        $id = $trgForm->setName($trg->getName())->setEnabled($trg->getEnabled())->submit()->getId();
        $trg->setId($id);

        $trgForm->addNewTaxRule();

        foreach ($trg->getTaxRules() as $taxRule) {
            $taxRuleForm = $this->get('PageObject:BackOffice\Taxes\TaxRuleFormPage');
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
