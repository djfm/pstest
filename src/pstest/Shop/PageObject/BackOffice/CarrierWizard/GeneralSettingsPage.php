<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\CarrierWizard;

use PrestaShop\PSTest\Shop\Shop;

class GeneralSettingsPage
{
    private $browser;
    private $shop;
    private $backOffice;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->backOffice = $shop->get('back-office');
        $this->browser = $shop->getBrowser();
    }

    public function setName($name)
    {
        $this->browser->fillIn('#name', $name);
        return $this;
    }

    public function getName()
    {
        return $this->browser->getValue('#name');
    }

    public function setTransitTime($time)
    {
        $this->browser->fillIn($this->backOffice->i18nFieldName('#delay'), $time);
        return $this;
    }

    public function getTransitTime()
    {
        return $this->browser->getValue($this->backOffice->i18nFieldName('#delay'));
    }

    public function setSpeedGrade($grade)
    {
        $this->browser->fillIn('#grade', $grade);
        return $this;
    }

    public function getSpeedGrade()
    {
        return $this->browser->getValue('#grade');
    }

    public function nextStep()
    {
        $this->browser->click('a.buttonNext');

        return new CostsSettingsPage($this->shop);
    }
}
