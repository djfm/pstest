<?php

namespace PrestaShop\PSTest\Shop;

interface ShopInterface
{
    public function getInstallerURL();
    public function getBrowser();
    public function get($serviceAsString);
}
