<?php

namespace PrestaShop\PSTest\Shop\BrowserExtension;

use PrestaShop\PSTest\Shop\Shop;

class PSForm
{
    private $shop;

    public function __construct(Shop $shop)
    {
            $this->shop = $shop;
    }

    public function getBrowser()
    {
        return $this->shop->getBrowser();
    }

    public function toggle($name, $yesOrNo)
    {
        return $this->shop->getBrowser()->clickLabelFor($name . ($yesOrNo ? '_on' : '_off'));
    }

    public function getToggleValue($name)
    {
        return $this->shop->getBrowser()->find('#' . $name . '_on')->isSelected();
    }
}
