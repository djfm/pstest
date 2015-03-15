<?php

namespace PrestaShop\PSTest\Shop;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

class LocalShopFactory
{
    private $systemSettings;
    private $sourceSettings;

    public function __construct(SystemSettings $systemSettings, LocalShopSourceSettings $sourceSettings)
    {
        $this->systemSettings = $systemSettings;
        $this->sourceSettings = $sourceSettings;
    }

    public function makeShop()
    {

    }
}
