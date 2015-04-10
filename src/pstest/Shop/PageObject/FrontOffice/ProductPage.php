<?php

namespace PrestaShop\PSTest\Shop\PageObject\FrontOffice;

use Exception;

use PrestaShop\PSTest\Shop\PageObject\PageObject;
use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

class ProductPage extends PageObject
{
    public function setQuantity($q)
    {
        $this->browser->fillIn('#quantity_wanted', $q);

        return $this;
    }

    public function getQuantity()
    {
        return (int)$this->browser->getValue('#quantity_wanted');
    }

    public function addtoCart()
    {
        $getNumberOfProductsInCart = function () {
            try {
                return (int)$this->browser->getText('.shopping_cart .ajax_cart_quantity');
            } catch (Exception $e) {
                return 0;
            }
        };

        $quantityToAdd = $this->getQuantity();

        if ($quantityToAdd <= 0) {
            throw new Exception('Looks like there is actually no product to add to the cart!');
        }

        $expectedQuantityAfterAdd = $getNumberOfProductsInCart() + $quantityToAdd;

        $this->browser->click('#add_to_cart button');

        Spin::assertTrue(function () use ($expectedQuantityAfterAdd, $getNumberOfProductsInCart) {
            return $expectedQuantityAfterAdd === $getNumberOfProductsInCart();
        }, 5, 1000, 'Could not add product to the cart.');

        $closePopinSelector = '#layer_cart span.cross';
        if ($this->browser->hasVisible($closePopinSelector)) {
            $this->browser->click($closePopinSelector);
        }

        return $this;
    }
}
