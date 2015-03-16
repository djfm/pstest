<?php

namespace PrestaShop\PSTest\Shop;

use Exception;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

class LocalShop
{
    private $systemSettings;
    private $sourceSettings;

    public function __construct(SystemSettings $systemSettings, LocalShopSourceSettings $sourceSettings)
    {
        $this->systemSettings = $systemSettings;
        $this->sourceSettings = $sourceSettings;
    }
}
