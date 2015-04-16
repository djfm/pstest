<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use Exception;

use PrestaShop\PSTest\Shop\BackOfficeService;

use PrestaShop\PSTest\Shop\Entity\CartRule;

class CartRules extends BackOfficeService
{
    public function createCartRule(CartRule $cartRule)
    {
        $this->backOffice->visitController('AdminCartRules', ['addcart_rule']);

        $informationPage = $this->get('PageObject:BackOffice\CartRules\InformationPage');

        $informationPage->setName($cartRule->getName());

        $products = $cartRule->getProductRestrictions();
        if (!empty($products)) {
            $this->browser->click('#cart_rule_link_conditions');
            $conditionsPage = $this->get('PageObject:BackOffice\CartRules\ConditionsPage');

            $productIds = [];
            foreach ($products as $product) {
                if (!$product->getId()) {
                    throw new Exception('A product is missing an ID, cannot use it as a Cart Rule restriction.');
                }
                $productIds[] = $product->getId();
            }

            $conditionsPage->addProductRestrictions($productIds);
        }


        $this->browser->click('#cart_rule_link_actions');

        $actionsPage = $this->get('PageObject:BackOffice\CartRules\ActionsPage');

        $actionsPage->setFreeShipping($cartRule->getFreeShipping());

        $actionsPage->setDiscount(
            $cartRule->getDiscountType(),
            $cartRule->getDiscountAmount(),
            $cartRule->getDiscountIsBeforeTaxes()
        );

        if ($cartRule->getApplyToCheapestProduct()) {
            $actionsPage->applyToCheapestProduct();
        }

        $this->browser->click('#desc-cart_rule-save-and-stay');

        $this->browser->checkStandardFormFeedback();

        $id = (int)$this->browser->getURLParameter('id_cart_rule');

        $cartRule->setId($id);

        return $this;
    }
}
