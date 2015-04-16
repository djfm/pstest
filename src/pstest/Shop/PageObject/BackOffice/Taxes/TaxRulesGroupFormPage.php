<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\Taxes;

use PrestaShop\PSTest\Shop\Shop;

class TaxRulesGroupFormPage
{
    private $shop;
    private $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();
    }

    public function getName()
    {
        return $this->browser->getValue(
            '#name'
        );
    }

    public function setName($name)
    {
        $this->browser->fillIn(
            '#name',
            $name
        );

        return $this;
    }

    public function setEnabled($yes) {
        if ($yes) {
            $this->browser->clickLabelFor('active_on');
        } else {
            $this->browser->clickLabelFor('active_off');
        }
        return $this;
    }

    public function isEnabled()
    {
        return $this->browser->find('#active_on')->isSelected();
    }

    public function submit()
    {
        $this->browser
             ->click('#tax_rules_group_form_submit_btn')
             ->checkStandardFormFeedback('Could not save TaxRulesGroup form.')
        ;

        return $this;
    }

    public function getId()
    {
        return (int)$this->browser->getURLParameter('id_tax_rules_group');
    }

    public function addNewTaxRule()
    {
        $this->browser->click('#page-header-desc-tax_rule-new');
        return $this;
    }
}
