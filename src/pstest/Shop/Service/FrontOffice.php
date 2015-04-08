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

    public function visitMyAccount()
    {
        $this->browser->visit($this->shop->getFrontOfficeURL());

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
}
