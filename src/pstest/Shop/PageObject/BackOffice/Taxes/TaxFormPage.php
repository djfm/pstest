<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\Taxes;

use PrestaShop\PSTest\Shop\Shop;

class TaxFormPage
{
    private $shop;
    private $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();
    }

    public function getName()
    {
        return $this->browser->getValue(
            $this->shop->get('back-office')->i18nFieldName('#name')
        );
    }

    public function setName($name)
    {
        $this->browser->fillIn(
            $this->shop->get('back-office')->i18nFieldName('#name'),
            $name
        );

        return $this;
    }

    public function setRate($rate)
    {
        $this->browser->fillIn(
            '#rate',
            $rate
        );

        return $this;
    }

    public function getRate()
    {
        return $this->browser->getValue('#rate');
    }

    public function setActive($yes) {
        if ($yes) {
            $this->browser->clickLabelFor('active_on');
        } else {
            $this->browser->clickLabelFor('active_off');
        }
        return $this;
    }

    public function isActive()
    {
        return $this->browser->find('#active_on')->isSelected();
    }

    public function submit()
    {
        $this->browser->clickButtonNamed('submitAddtax');

        $this->shop->checkStandardErrorFeedback('Could not save Tax form.');

        return $this;
    }
}
