<?php

namespace PrestaShop\PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\PrestaShopTest;

use PrestaShop\PSTest\Shop\Entity\CartRule;

class CartRulesSetupTest extends PrestaShopTest
{
    private $backOffice;

    public function cacheInitialState()
    {
        return [
            'installer' => [
                'install' => [[
                    'language' => 'en',
                    'country' => 'us']
                ]]
        ];
    }

    /**
     * @beforeClass
     */
    public function loginToTheBackOffice()
    {
        $this->backOffice = $this->shop->get('back-office')->login();
    }

    public function test_creation_of_percent_cart_rule()
    {
        $cartRule = new CartRule;
        $cartRule
            ->setFreeShipping(false)
            ->setName('10% off')
            ->setDiscountType(CartRule::TYPE_PERCENT)
            ->setDiscountAmount(10)
        ;
        $this->backOffice->get('cart-rules')->createCartRule($cartRule);
    }

    public function test_creation_of_amount_cart_rule()
    {
        $cartRule = new CartRule;
        $cartRule
            ->setFreeShipping(false)
            ->setName('10 after tax')
            ->setDiscountType(CartRule::TYPE_AMOUNT)
            ->setDiscountAmount(10)
            ->setDiscountIsBeforeTaxes(false)
        ;
        $this->backOffice->get('cart-rules')->createCartRule($cartRule);
    }

    public function test_creation_of_free_shipping_cart_rule()
    {
        $cartRule = new CartRule;
        $cartRule->setFreeShipping(true)->setName('Free Shipping CartRule');
        $this->backOffice->get('cart-rules')->createCartRule($cartRule);
    }
}
