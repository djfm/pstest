<?php

namespace PrestaShop\PSTest\Shop\Service;

use Exception;

use PrestaShop\PSTest\Shop\Shop;

use PrestaShop\PSTest\Shop\Entity\Product;

use PrestaShop\PSTest\Shop\PageObject\FrontOffice\AuthenticationPage;
use PrestaShop\PSTest\Shop\PageObject\FrontOffice\ProductPage;

class FrontOffice
{
    private $shop;
    private $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();
    }

    private function loggedIn()
    {
        return $this->browser->hasVisible('.header_user_info a.logout');
    }

    public function visitHome()
    {
        $this->browser->visit($this->shop->getFrontOfficeURL());
        return $this;
    }

    public function visitMyAccount()
    {
        $this->visitHome();

        if ($this->loggedIn()) {
            $this->browser->click('.header_user_info a.account');
        } else {
            $this->browser->click('.header_user_info a');
            return new AuthenticationPage($this->shop);
        }
    }

    public function login($email = null, $password = null)
    {
        $account = $this->visitMyAccount();
        if ($account instanceof AuthenticationPage) {
            $account->login($email, $password);
        } else {
            throw new Exception('Seems like a customer is already logged in.');
        }
    }

    public function visitProduct(Product $product)
    {
        if (!$product->getFrontOfficeURL()) {
            throw new Exception('Don\'t know the product\'s Front-Office URL, cannot visit it.');
        }
        $this->browser->visit($product->getFrontOfficeURL());
        return new ProductPage($this->shop);
    }

    public function visitCart()
    {
        $this->visitHome();
        $this->browser->click('.shopping_cart > a');

        try {
            $this->browser->waitFor('a.standard-checkout');
        } catch (Exception $e) {
            throw new Exception('Seems we\'re not on the Cart page as expected.');
        }
    }

    public function checkoutCart(array $options)
    {
        $this->visitCart();
        $this->browser->click('a.standard-checkout');

        $this->browser->clickButtonNamed('processAddress');

        if (isset($options['carrierName'])) {
            $this->browser->click('{xpath}//tr[contains(., "' . $options['carrierName'] . '")]//input[@type="radio"]');
        }

        $this->browser->clickLabelFor('cgv');

        $this->browser->clickButtonNamed('processCarrier');

        $this->browser->click('p.payment_module a.bankwire');

        $this->browser->click('#cart_navigation button');
    }
}
