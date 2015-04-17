<?php

namespace PrestaShop\PSTest\Cloud\PageObject;

use Exception;

class OnboardingBanner
{
    private $browser;

    public function __construct($browser)
    {
        $this->browser = $browser;
    }

    public function setStoreName($name)
    {
        $this->browser->fillIn('#create-online-store-shop_name', $name);
        return $this;
    }

    public function setEmailAddress($address)
    {
        $this->browser->fillIn('#create-online-store-email', $address);
        return $this;
    }

    public function submit()
    {
        $this->browser->click('#create-online-store a.submit');
        $this->browser->waitFor('#inputCountry', 30);
        return new StoreConfiguration($this->browser);
    }
}
