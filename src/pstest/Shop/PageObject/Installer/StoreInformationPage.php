<?php

namespace PrestaShop\PSTest\Shop\PageObject\Installer;

use PrestaShop\Selenium\Browser\BrowserInterface;

class StoreInformationPage
{
    private $browser;

    public function __construct(BrowserInterface $browser)
    {
        $this->browser = $browser;
    }

    public function setShopName($name)
    {
        $this->browser->fillIn('#infosShop', $name);

        return $this;
    }

    public function setCountry($iso)
    {
        $this->browser->jqcSelect('#infosCountry', $iso);

        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->browser->fillIn('#infosFirstname', $firstName);

        return $this;
    }

    public function setLastName($lastName)
    {
        $this->browser->fillIn('#infosName', $lastName);

        return $this;
    }

    public function setEmailAddress($email)
    {
        $this->browser->fillIn('#infosEmail', $email);

        return $this;
    }

    public function setPassword($password)
    {
        $this->browser
             ->fillIn('#infosPassword', $password)
             ->fillIn('#infosPasswordRepeat', $password)
        ;

        return $this;
    }

    public function nextStep()
    {
        $this->browser->click('#btNext');

        return new SystemConfigurationPage($this->browser);
    }
}
