<?php

namespace PrestaShop\PSTest\Shop\PageObject\BackOffice\CarrierWizard;

use Exception;

use PrestaShop\PSTest\Shop\Shop;
use PrestaShop\PSTest\Shop\Entity\CarrierRange;

use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

class SizeWeightAndGroupAccessPage
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

    public function setMaximumPackageWidth($m)
    {
        $this->browser->fillIn('#max_width', $m);
        return $this;
    }

    public function getMaximumPackageWidth()
    {
        return $this->browser->getValue('#max_width');
    }

    public function setMaximumPackageHeight($m)
    {
        $this->browser->fillIn('#max_height', $m);
        return $this;
    }

    public function getMaximumPackageHeight()
    {
        return $this->browser->getValue('#max_height');
    }

    public function setMaximumPackageDepth($m)
    {
        $this->browser->fillIn('#max_depth', $m);
        return $this;
    }

    public function getMaximumPackageDepth()
    {
        return $this->browser->getValue('#max_depth');
    }

    public function setMaximumPackageWeight($m)
    {
        $this->browser->fillIn('#max_weight', $m);
        return $this;
    }

    public function getMaximumPackageWeight()
    {
        return $this->browser->getValue('#max_weight');
    }

    public function setGroupAccess(array $permissions)
    {
        foreach ($permissions as $groupId => $allowed)
        {
            $this->browser->checkBox('#groupBox_' . $groupId, $allowed);
        }

        return $this;
    }

    public function nextStep()
    {
        $this->browser->click('a.buttonNext');

        return new FinalPage($this->shop);
    }

}
