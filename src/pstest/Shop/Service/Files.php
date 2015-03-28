<?php

namespace PrestaShop\PSTest\Shop\Service;

use Exception;

use PrestaShop\PSTest\Shop\LocalShop;

use PrestaShop\FileSystem\FileSystemOverHTTP;

class Files
{
    private $shop;
    private $fs;

    public function __construct(LocalShop $shop, FileSystemOverHTTP $fs)
    {
        $this->shop = $shop;
        $this->fs = $fs;
    }

    public function removeAll()
    {
        $this->fs->rmr(
            $this->shop->getSourceSettings()->getPathToShopFiles()
        );

        return $this;
    }
}
