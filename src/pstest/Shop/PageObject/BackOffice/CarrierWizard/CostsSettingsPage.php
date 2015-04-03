<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\CarrierWizard;

use Exception;

use PrestaShop\PSTest\Shop\Shop;

class CostsSettingsPage
{
    private $browser;
    private $shop;
    private $backOffice;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->backOffice = $shop->get('back-office');
        $this->browser = $shop->getBrowser();
    }

    public function setAddHandlingCosts($yes = true)
    {
        $this->shop->getPSForm()->toggle('shipping_handling', $yes);
        return $this;
    }

    public function getAddHandlingCosts()
    {
        return $this->shop->getPSForm()->getToggleValue('shipping_handling');
    }

    public function setFreeShipping($yes = true)
    {
        $this->shop->getPSForm()->toggle('is_free', $yes);
        return $this;
    }

    public function getFreeShipping()
    {
        return $this->shop->getPSForm()->getToggleValue('is_free');
    }

    public function setBillAccordingToPrice($yes = true)
    {
        if ($yes) {
            $this->browser->click('#billing_price');
        } else {
            $this->setBillAccordingToWeight(true);
        }
        return $this;
    }

    public function getBillAccordingToPrice()
    {
        return $this->browser->find('#billing_price')->isSelected();
    }

    public function setBillAccordingToWeight($yes = true)
    {
        if ($yes) {
            $this->browser->click('#billing_weight');
        } else {
            $this->setBillAccordingToPrice(true);
        }
        return $this;
    }

    public function getBillAccordingToWeight()
    {
        return $this->browser->find('#billing_weight')->isSelected();
    }

    public function setTaxRulesGroupId($id)
    {
        $this->browser->select('#id_tax_rules_group', $id);
        return $this;
    }

    public function getTaxRulesGroupId()
    {
        return $this->browser->getSelectedValue('#id_tax_rules_group');
    }

    public function setOutOfRangeBehavior($behavior)
    {
        $this->browser->select('#range_behavior', $behavior);
        return $this;
    }

    public function getOutOfRangeBehavior()
    {
        return $this->getBrowser()->getSelectedValue('#range_behavior');
    }
}
