<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\Taxes;

use PrestaShop\PSTest\Shop\Shop;

class TaxRuleFormPage
{
    private $shop;
    private $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();
    }

    public function setCountryId($id)
    {
        $this->browser->select('#country', $id);
        return $this;
    }

    public function getCountryId()
    {
        return $this->browser->getSelectedValue('#country');
    }

    public function setBehavior($id)
    {
        $this->browser->select('#behavior', $id);
        return $this;
    }

    public function getBehavior()
    {
        return $this->browser->getSelectedValue('#behavior');
    }

    public function setTaxId($id)
    {
        $this->browser->select('#id_tax', $id);
        return $this;
    }

    public function getTaxId()
    {
        return $this->browser->getSelectedValue('#id_tax');
    }

    public function setZipCodeRange($range)
    {
        $this->browser->fillIn('#zipcode', $range);
        return $this;
    }

    public function getZipCodeRange()
    {
        return $this->browser->getValue('#zipcode');
    }

    public function setDescription($description)
    {
        $this->browser->fillIn('#description', $description);
        return $this;
    }

    public function getDescription()
    {
        return $this->browser->getValue('#description');
    }

    public function submit()
    {
        $this->browser->clickButtonNamed('create_ruleAndStay');

        $this->shop->getErrorChecker()->checkStandardFormFeedback('Could not save TaxRulesGroup form.');

        return $this;
    }
}
