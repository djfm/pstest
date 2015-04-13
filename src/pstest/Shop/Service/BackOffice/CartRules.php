<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use PrestaShop\PSTest\Shop\BackOfficeService;

use PrestaShop\PSTest\Shop\Entity\CartRule;

class CartRules extends BackOfficeService
{
    public function createCartRule(CartRule $cartRule)
    {
        $this->backOffice->visitController('AdminCartRules', ['addcart_rule']);

        $informationPage = $this->get('PageObject:BackOffice\CartRules\InformationPage');

        $informationPage->setName($cartRule->getName());

        $this->browser->click('#cart_rule_link_actions');

        $actionsPage = $this->get('PageObject:BackOffice\CartRules\ActionsPage');

        $actionsPage->setFreeShipping($cartRule->getFreeShipping());

        $actionsPage->setDiscount(
            $cartRule->getDiscountType(),
            $cartRule->getDiscountAmount(),
            $cartRule->getDiscountIsBeforeTaxes()
        );

        $this->browser->click('#desc-cart_rule-save-and-stay');

        $this->shop->getErrorChecker()->checkStandardFormFeedback();

        $id = (int)$this->browser->getURLParameter('id_cart_rule');

        $cartRule->setId($id);

        return $this;
    }
}
