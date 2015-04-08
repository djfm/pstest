<?php

namespace PrestaShop\PSTest\Shop\PageObject\FrontOffice;

use Exception;

use PrestaShop\PSTest\Shop\PageObject\PageObject;

class AuthenticationPage extends PageObject
{
    public function login($email = null, $password = null)
    {
        if (null === $email) {
            $email = $this->shop->getDefaults('customer.email');
        }

        if (null === $password) {
            $password = $this->shop->getDefaults('customer.password');
        }

        $this->browser
             ->fillIn('#email', $email)
             ->fillIn('#passwd', $password)
             ->click('#SubmitLogin')
        ;

        if ($this->browser->hasVisible('div.alert.alert-danger')) {
            throw new Exception('Could not log in!');
        }

        if (!$this->browser->hasVisible('a i.icon-user')) {
            throw new Exception('Could not assert that the login was successful.');
        }

        return new MyAccountPage($this->shop);
    }
}
