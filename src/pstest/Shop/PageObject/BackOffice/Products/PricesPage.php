<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\Products;

use PrestaShop\PSTest\Shop\PageObject\BackOfficePageObject;

class PricesPage extends BackOfficePageObject
{
    public function setPrice($price)
    {
        $this->browser->fillIn('#priceTE', $price);
        return $this;
    }

    public function getPrice()
    {
        return $this->browser->getValue('#priceTE');
    }

    public function setTaxRulesGroupId($id)
    {
        $this->browser->select('#id_tax_rules_group', $id);
    }

    public function getTaxRulesGroupId()
    {
        return $this->browser->getSelectedValue('#id_tax_rules_group');
    }
}
