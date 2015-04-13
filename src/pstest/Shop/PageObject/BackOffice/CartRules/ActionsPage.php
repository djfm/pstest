<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\CartRules;

use PrestaShop\PSTest\Shop\PageObject\BackOfficePageObject;
use PrestaShop\PSTest\Shop\Entity\CartRule;

class ActionsPage extends BackOfficePageObject
{
    public function setFreeShipping($yes)
    {
        $this->shop->getPSForm()->toggle('free_shipping', $yes);
        return $this;
    }

    public function setDiscount($type, $amount, $beforeTaxes)
    {
        if ($type === CartRule::TYPE_AMOUNT) {
            $this->browser
                ->clickLabelFor('apply_discount_amount')
                ->fillIn('#reduction_amount', $amount)
                ->select('[name=reduction_tax]', 1 - (int)$beforeTaxes)
            ;
        } else if ($type === CartRule::TYPE_PERCENT) {
            $this->browser
                ->clickLabelFor('apply_discount_percent')
                ->fillIn('#reduction_percent', $amount);
        } else {
            $this->browser->clickLabelFor('apply_discount_off');
        }

        return $this;
    }
}
