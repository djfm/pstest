<?php

namespace PrestaShop\PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\TestCase;

use PrestaShop\PSTest\Shop\Entity\Product;

class FrontOfficeBasicsTest extends TestCase
{
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

    public function test_customer_can_log_in()
    {
        $this->shop->get('front-office')->login();
    }

    public function test_validate_an_order()
    {
        $this->shop->get('back-office')->login()->get('orders')->visitById(4)->validate()->getInvoiceData();
    }

    public function test_a_product_is_added_to_the_cart()
    {
        $product = new Product;
        $product->setFrontOfficeURL($this->shop->getFrontOfficeURL() . '/casual-dresses/3-printed-dress.html');

        $this->shop->get('front-office')->visitProduct($product)->setQuantity(5)->addToCart();

        $this->shop->get('front-office')->checkoutCart(['carrierName' => 'My carrier']);
    }


}
