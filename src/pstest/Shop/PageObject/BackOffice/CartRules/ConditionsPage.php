<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\CartRules;

use Exception;

use PrestaShop\PSTest\Shop\PageObject\BackOfficePageObject;
use PrestaShop\PSTest\Shop\Entity\CartRule;

class ConditionsPage extends BackOfficePageObject
{
    public function addProductRestrictions(array $productIds)
    {
        $blockId = 1;
        $ruleId = 0;
        $this->browser
             ->click('#cart_rule_link_conditions')
             ->click('#product_restriction')
             ->click('{xpath}//a[contains(@href, "addProductRuleGroup")]')
             ->select('#product_rule_type_' . $blockId, 'products')
             ->click('{xpath}//a[contains(@href, "addProductRule(' . $blockId . ')")]')
             ->click('#product_rule_' . $blockId . '_' . (++$ruleId) . '_choose_link')
             ->multiSelect('#product_rule_select_' . $blockId . '_' . $ruleId . '_1', $productIds)
             ->click('#product_rule_select_' . $blockId . '_' . $ruleId . '_add')
             ->click('a.fancybox-close');
        // sanity check
        $matched = $this->browser->getValue('#product_rule_' . $blockId . '_' . $ruleId . '_match');
        if ($matched != count($productIds)) {
           throw new Exception('Could not select all of the requested products.');
        }
    }
}
