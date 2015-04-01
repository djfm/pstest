<?php

namespace PrestaShop\PSTest\Shop\Service;

use Exception;

use PrestaShop\PSTest\Shop\Shop;

class BackOffice
{
    private $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    public function login($email = null, $password = null, $stayLoggedIn = true)
    {
        if (null === $email) {
            $email = $this->shop->getDefaults('employee.email');
        }

        if (null === $password) {
            $password = $this->shop->getDefaults('employee.password');
        }

        $browser = $this->shop->getBrowser();

        $browser->visit(
            $this->shop->getBackOfficeURL()
        );

        $controller = $browser->getURLParameter('controller');
        if ('AdminLogin' !== $controller) {
            throw new Exception(sprintf(
                'Expected to be on AdminLogin controller, but it seems we\'re somewhere else (%s).',
                $controller
            ));
        }

        $browser
        ->fillIn('#email', $email)
        ->fillIn('#passwd', $password);

        if ($stayLoggedIn) {
            $browser->clickLabelFor('stay_logged_in');
        }

        $browser->clickButtonNamed('submitLogin');

        return $this;
    }
}
