<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\CarrierWizard;

use Exception;

use PrestaShop\PSTest\Shop\Shop;
use PrestaShop\PSTest\Shop\Entity\CarrierRange;

use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

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
        return $this->browser->getSelectedValue('#range_behavior');
    }

    public function setRanges(array $ranges)
    {
        foreach ($ranges as $n => $range) {

            if ($n > 0) {
                $this->browser->click('#add_new_range');
            }

            $this->setRange($n, $range);
        }
    }

    private function setRange($n, CarrierRange $range)
    {
        $index = $n + 3;

        $infSelector = "tr.range_inf td:nth-of-type($index) input";
        $supSelector = "tr.range_sup td:nth-of-type($index) input";

        $this->browser
             ->fillIn($infSelector, $range->getFromIncluded())
             ->fillIn($supSelector, $range->getToExcluded())
        ;

        if ($range->getZones()) {
            // We want a cost per zone
            foreach ($range->getZones() as $zone) {
                $costSelector = "tr.fees[data-zoneid='{$zone->getId()}'] td:nth-of-type($index) input";
                $this->browser->checkbox("tr.fees[data-zoneid='{$zone->getId()}'] input[type='checkbox']", true);

                Spin::assertNoException(function () use ($costSelector, $zone) {
                    $this->browser->fillIn($costSelector, $zone->getCost());
                }, 15, 1000, 'Could not set shipping cost for zone.');

            }
        } else {
            // We want same cost for all zones
            $this->browser
                 // this selector is not immediately visible, we must not fill anything
                 // else until it show up otherwise the JS won't work properly
                 ->waitFor("tr.fees_all td:nth-of-type($index) input")
                 ->checkbox('tr.fees_all input[type=checkbox]', true)
                 ->fillIn("tr.fees_all td:nth-of-type($index) input", $range->getCost())
                 // need to click somewhere to make cost input lose focus
                 // and trigger the JS that updates the costs for all ranges
                 ->click("tr.range_inf td:nth-of-type($index) input")
            ;
        }
    }

    public function nextStep()
    {
        $this->browser->click('a.buttonNext');

        return new SizeWeightAndGroupAccessPage($this->shop);
    }
}
