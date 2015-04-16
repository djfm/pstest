<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\CarrierWizard;

use Exception;

use PrestaShop\PSTest\Shop\Shop;

class FinalPage
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

    public function setEnabled($yes = true)
    {
        $this->browser->toggle('active', $yes);
        return $this;
    }

    public function getEnabled()
    {
        return $this->browser->getToggleValue('active');
    }

    public function submit()
    {
        $this->browser->click('a.buttonFinish');

        $this->browser->checkStandardFormFeedback();

        return $this;
    }
}
